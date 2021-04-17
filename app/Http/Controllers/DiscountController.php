<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class DiscountController extends Controller
{
    public function isValidCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|exists:discounts',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $discount = Discount::where('code', $request->input('code'))->first();

        $result = $this->checkingValidation($discount);
        if ($result['error']) {
            return response(['error' => true, 'message' => $result['message']]);
        }

        $result = $this->applyDiscountCode($discount, $result['discountedCost'], $result['cart']);
        return response([
            'message' => ' کد تخفیف اعمال شد و مقدار ' . $result['amountOfDiscount'] . ' تومان از خرید شما کم شده است ',
            'amountOfDiscount' => $result['amountOfDiscount'],
            'amountOfPayment' => $result['amountOfPayment']

        ]);
    }

    public function checkingValidation($discount): array
    {
        $current = Carbon::now();
        if ($current->gt($discount->expire_date)) {
            return ['error' => true, 'message' => 'تاریخ استفاده از کد مورد نظر به پایان رسیده است'];
        }
        if ($discount->count == $discount->used_number) {
            return ['error' => true, 'message' => 'تعداد افراد استفاده کننده از این کد تخفیف به حداکثر رسیده است'];
        }

        $exams = $discount->Exams;
        $examIdsOfDiscount = [];
        foreach ($exams as $key => $exam) {
            $examIdsOfDiscount[$key] = $exam->id;
        }

        $user = $this->getUser();
        $cart = $user->LastUnpaidCart;
        $examsInfo = json_decode($cart->exam_info, true);

        $existInDiscount = false;
        $discountedCost = 0;
        foreach ($examsInfo as $key => $item) {
            if (in_array($item['id'], $examIdsOfDiscount)) { //array_key_exists($value['id'], $examIdsOfDiscount)
                $existInDiscount = true;
                $discountedCost += $item['price'];
            }
        }

        if (!$existInDiscount) {
            return ['error' => true, 'message' => 'این کدتخفیف برای آزمون مورد نظر شما نیست'];
        }

        return ['error' => false, 'discountedCost' => $discountedCost, 'cart' => $cart];
    }

    public function applyDiscountCode($discount, $discountedCost, $cart): array
    {
        if ($discount->type == 'PERCENT') {
            $amountOfDiscount = $discountedCost * $discount->value / 100;
            if ($amountOfDiscount > $discount->maximum_value) {
                $amountOfDiscount = $discount->maximum_value;
            }
            $amountOfPayment = $cart->final_cost - $amountOfDiscount;
        } else { //type is 'CASH'
            if ($discountedCost > $discount->value) {
                $amountOfDiscount = $discount->value;
                $amountOfPayment = $cart->final_cost - $amountOfDiscount;
            } else {
                $amountOfDiscount = $discountedCost;
                $t = $cart->final_cost - $amountOfDiscount;
                $amountOfPayment = ($t > 0) ? $t : 0;
            }
        }
        return [
            'amountOfDiscount' => $amountOfDiscount,
            'amountOfPayment' => $amountOfPayment
        ];
    }
}
