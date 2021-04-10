<?php

namespace App\Http\Controllers;

use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class TransactionController extends Controller
{
    private $zpo; //zarinpal object
    private $cto;

    public function __construct()
    {
        $this->zpo = new ZarinpalController();
        $this->cto = new CartController();
    }

    public function viewPurchaseInformation(Request $request)
    {
        $calculatedNumbers = $this->calculatePayment($request);
        if (!$calculatedNumbers['keepOn']) {
            return redirect('blank')->withErrors($calculatedNumbers['content'], 'errors');
        }
        $myContent = [
            'price' => $calculatedNumbers['price'],
            'amountOfDiscount' => $calculatedNumbers['amountOfDiscount'],
            'amountOfPayment' => $calculatedNumbers['amountOfPayment'],
            'code' => $calculatedNumbers['discountCode'],
        ];
        return view('buyable::purchaseInformation')->with('myContent', $myContent);
    }

    private function calculatePayment(Request $request)
    {
        $keepOn = false;
        $price = 0;
        $amountOfDiscount = 0;
        $amountOfPayment = 0;
        $discountCode = null;
        $content = '';
        $validatedData = Validator::make($request->all(), [
            'code' => 'nullable|exists:discounts',
            'description' => 'nullable|string',
        ]);
        if ($validatedData->fails()) {
            $keepOn = false;
            $content = $validatedData->errors()->all();
        } else {
            if ($request->filled('code')) {
                $d = new DiscountController;
                $result = $d->isValidCode($request);
                if ($result->getData()->status == 'error') {
                    $keepOn = false;
                    $content = $result->getData()->massage;

                } else {
                    $keepOn = true;
                    $price = $result->getData()->massage->price;
                    $amountOfDiscount = $result->getData()->massage->amountOfDiscount;
                    $amountOfPayment = $result->getData()->massage->amountOfPayment;
                    $discountCode = $request->input('code');
                }
            } else {
                $keepOn = true;
                $price = session()->get('finalCost');
                $amountOfDiscount = 0;
                $amountOfPayment = $price;
                $discountCode = null;
            }
        }
        return [
            'keepOn' => $keepOn,
            'content' => $content,
            'price' => $price,
            'amountOfDiscount' => $amountOfDiscount,
            'amountOfPayment' => $amountOfPayment,
            'discountCode' => $discountCode,
        ];
    }

    public function buy(Request $request)
    {
        $calculatedNumbers = $this->calculatePayment($request);
        if (!$calculatedNumbers['keepOn']) {
            return response()->json($calculatedNumbers['content']);
        }

        $factorNumber = Factory::create()->unique()->numberBetween(1000000000, 999999999999);
        while (Transaction::where('factorNumber', $factorNumber)->exists()) {
            $factorNumber = Factory::create()->unique()->numberBetween(1000000000, 999999999999);
        }
        $tran = Transaction::create([
            'amount' => $calculatedNumbers['amountOfPayment'],
            'factorNumber' => $factorNumber,
            'cart_id' => session()->get('cartId'),
            'discount_id' => $request->filled('code') ?
                Discount::where('code', $calculatedNumbers['discountCode'])->firstOrFail()->id : null,
            'description' => $request->filled('description') ? $request->input('description') : null,
        ]);


        if ($calculatedNumbers['discountCode'] != null) {
            $tran->discount_id = Discount::where('code', $calculatedNumbers['discountCode'])->firstOrFail()->id;
            $tran->save();
        }

        $res = $this->zpo->pay($calculatedNumbers['amountOfPayment'], url('/buyable/verify/' . $factorNumber), "nooshin.tghvi@gmail.com", "09135138355");
        return redirect($res);
    }

    public function verifyInfo(Request $request, $factorNumber)
    {
        $t = Transaction::where('factorNumber', $factorNumber)->firstOrFail();
        $cnt = $this->zpo->order($request, $t->amount);
        if ($cnt['ok']) {
            $t->description = $cnt['massage'];
            $t->condition = 'SUCCESSFUL';
            $t->save();
            $c = Cart::find($t->cart_id);
            $c->is_pay = true;
            $c->transaction_id = $t->id;
            $c->save();
            $this->cto->storeDataInSession(['userId' => session()->get('userId')]);
            if ($t->discount_id != null) {
                $d = Discount::find($t->discount_id);
                $d->used_number = $d->used_number + 1;
                $d->save();
            }
        } else {
            $t->description = $cnt['massage'];
            $t->condition = 'FAILED';
            $t->save();
        }
        return redirect()->route('buyable.user.transaction');
    }

    public function myTransaction()
    {
        $user = auth('user')->user();
        $carts = $user->Carts;
        $transactions = [];
        foreach ($carts as $cart) {
            $c = $cart->Transaction()->where('condition', 'SUCCESSFUL')->get()->toArray();
            if ($c != null) {
                $c[0]['produces'] = $cart->Exams()->select('name')->get()->toArray();
                array_push($transactions, $c[0]);
            }
        }
//        return $transactions;
        return view('buyable::transactions', compact('transactions'));
    }

}

