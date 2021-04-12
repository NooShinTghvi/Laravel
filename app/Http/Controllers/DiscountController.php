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
        $current = Carbon::now();
        if ($current->gt($discount->expire_date)) {
            return response([
                'error' => true,
                'message' => 'تاریخ استفاده از کد مورد نظر به پایان رسیده است'
//                'massage' => [
//                    'now' => $current,
//                    'expire' => $discount->expire_date,
//                    'diff' => $current->gt($discount->expire_date),
//                       'errors' => $validatedData->errors()
//                        ->add('field', 'تاریخ استفاده از کد مورد نظر به پایان رسیده است.'),
//                ],
            ]);
        }
        if ($discount->count == $discount->used_number) {
            return json_encode([
                'error' => true,
                'message' => 'تعداد افراد استفاده کننده از این کد تخفیف به حداکثر رسیده است'
            ]);
        }

        $exams = $discount->Exams;
        $examIdsOfDiscount = [];
        foreach ($exams as $key => $exam) {
            $examIdsOfDiscount[$key] = $exam->id;
        }

        $user = $this->getUser();
        $cart = $user->LastUnpaidCart;
        $examsInfo = json_decode($cart->exam_info, true);

        $userExams = [];
        $existInDiscount = false;
        $discountedCost = 0;
        $unDiscountedCost = 0;
        foreach ($examsInfo as $key => $item) {
            if (in_array($item['id'], $examIdsOfDiscount)) { //array_key_exists($value['id'], $examIdsOfDiscount)
                $existInDiscount = true;
                $discountedCost += $item['price'];
                $userExams[$key] = [
                    'id' => $item['id'], 'price' => $item['price'], 'useDiscount' => true,
                    'discountedCost' => $discountedCost, 'unDiscountedCost' => $unDiscountedCost,
                ];
            } else {
                $unDiscountedCost += $item['price'];
                $userExams[$key] = [
                    'id' => $item['id'], 'price' => $item['price'], 'useDiscount' => false,
                    'discountedCost' => $discountedCost, 'unDiscountedCost' => $unDiscountedCost,
                ];
            }
        }

        if (!$existInDiscount) {
            return response(['error' => true, 'message' => 'این کدتخفیف برای آزمون مورد نظر شما نیست']);
        }

        return $this->applyDiscountCode($discount, $userExams);
    }

    public function applyDiscountCode($discount, $userExams)
    {
        $totalDiscountCost = end($userExams)['discountedCost']; //Amount of expense that includes discount
        $totalUnDiscountCost = end($userExams)['unDiscountedCost']; //Amount of expense not including discount
        if ($discount->type == 'PERCENT') {
            $amountOfDiscount = $totalDiscountCost * $discount->value / 100;
            if ($amountOfDiscount > $discount->maximum_value) {
                $amountOfDiscount = $discount->maximum_value;
            }
            $amountOfPayment = $totalUnDiscountCost + ($totalDiscountCost - $amountOfDiscount);
        } else { //type is 'CASH'
            if ($totalDiscountCost < $discount->value) {
                $amountOfDiscount = $totalDiscountCost;
            } else {
                $amountOfDiscount = $discount->value;
            }
            $amountOfPayment = $totalUnDiscountCost + $totalDiscountCost - $amountOfDiscount;
            if ($amountOfPayment < 0) {
                $amountOfPayment = 0;
                $amountOfDiscount = $totalUnDiscountCost + $totalDiscountCost;
            }
        }

        return response([
            'code' => '200',
            'message' => ' کد تخفیف اعمال شد و مقدار ' . $amountOfDiscount . ' تومان از خرید شما کم شده است ',
            'amountOfDiscount' => $amountOfDiscount,
            'amountOfPayment' => $amountOfPayment
        ]);

////                'amountOfDiscount' => $amountOfDiscount,
////                'amountOfPayment' => $amountOfPayment,
////                'discountCode' => $discount->code,
////                'price' => $totalUnDiscountCost + $totalDiscountCost,
////                'type' => $discount->type,
////                'value' => $discount->value,
////                'max_value' => $discount->maximum_value,
    }
}
