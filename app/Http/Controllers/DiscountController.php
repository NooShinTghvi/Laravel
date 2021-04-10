<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;


class DiscountController extends Controller
{
    public function isValidCode(Request $request)
    {
//        return Validator::make($request->all(), [
//            'code' => 'required|exists:discounts',
//        ],[
//            'code.required' => 'وارد کردن کد اجباری است' ,
//            'code.exists' => 'کد وارد شده نا معتبر است' ,
//        ]);
        $validatedData = Validator::make($request->all(), [
            'code' => 'required|exists:discounts',
        ]);
        if ($validatedData->fails()) {
            return json_encode([
                'code' => '404',
                'message' => 'کد تایید وارد شده نادرست است',
            ]);
        }

        $discount = Discount::where('code', $request->get('code'))->firstOrFail();
        $current = Carbon::now();
        if ($current->gt($discount->expire_date)) {
            return json_encode([
                'code' => '304',
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
                'code' => '304',
                'message' => 'تعداد افراد استفاده کننده از این کد تخفیف به حداکثر رسیده است'
            ]);
        }

        $exams = $discount->Exams;
        $examIdsOfDiscount = array();
        foreach ($exams as $key => $exam) {
            $examIdsOfDiscount[$key] = $exam->id;
        }
        $examsInfo = session()->get('examsInfo');
        $userExams = array();
        $existInDiscount = false;
        $discountedCost = 0;
        $unDiscountedCost = 0;
        foreach ($examsInfo as $key => $value) {
            if (in_array($value['id'], $examIdsOfDiscount)) { //array_key_exists($value['id'], $examIdsOfDiscount)
                $existInDiscount = true;
                $discountedCost += $value['price'];
                $userExams[$key] = [
                    'id' => $value['id'], 'price' => $value['price'], 'useDiscount' => true,
                    'discountedCost' => $discountedCost, 'unDiscountedCost' => $unDiscountedCost,
                ];
            } else {
                $unDiscountedCost += $value['price'];
                $userExams[$key] = [
                    'id' => $value['id'], 'price' => $value['price'], 'useDiscount' => false,
                    'discountedCost' => $discountedCost, 'unDiscountedCost' => $unDiscountedCost,
                ];
            }
        }

        if (!$existInDiscount) {
            return json_encode([
                'code' => '300',
                'message' => 'این کدتخفیف برای آزمون مورد نظر شما نیست'
            ]);

        }
//        dd($userExams);
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

        session(['amountOfPayment' => $amountOfPayment]);
        $json = ['code' => '200',
            'message' => ' کد تخفیف اعمال شد و مقدار ' . $amountOfDiscount . ' تومان از خرید شما کم شده است ',
            'amountOfDiscount' => $amountOfDiscount,
            'amountOfPayment' => $amountOfPayment];
        return json_encode($json);
////                'amountOfDiscount' => $amountOfDiscount,
////                'amountOfPayment' => $amountOfPayment,
////                'discountCode' => $discount->code,
////                'price' => $totalUnDiscountCost + $totalDiscountCost,
////                'type' => $discount->type,
////                'value' => $discount->value,
////                'max_value' => $discount->maximum_value,
    }
}
