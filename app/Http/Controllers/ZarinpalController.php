<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ZarinpalController extends Controller
{
    public function pay($amount, $callbackURL, $email, $mobile): string
    {
        $response = zarinpal()
            ->amount($amount) // مبلغ تراکنش به تومان
            ->request()
            ->zarin() // فعالسازی زرین گیت - اختیاری
            ->callback($callbackURL) // آدرس برگشت پس از پرداخت
            ->description('transaction info') // توضیحات تراکنش
//            ->email($email) // ایمیل مشتری - اختیاری
//            ->mobile($mobile) // شماره موبایل مشتری - اختیاری
            ->send();

        if (!$response->success()) {
            return $response->error()->message();
        }
        return 'done';
    }

    public function order(Request $request, $amount)
    {
        $authority = $request->input('Authority'); // دریافت کوئری استرینگ ارسال شده توسط زرین پال

        $response = zarinpal()
            ->amount($amount)
            ->verification()
            ->authority($authority)
            ->send();

        if (!$response->success()) {
            return $response->error()->message();
        }

        // پرداخت موفقیت آمیز بود
        // دریافت شماره پیگیری تراکنش و انجام امور مربوط به دیتابیس
        return $response->referenceId();
    }
}
