<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Discount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class TransactionController extends Controller
{
    private ZarinpalController $zpo; //zarinpal object
    private CartController $cto;

    public function __construct()
    {
        $this->zpo = new ZarinpalController();
        $this->cto = new CartController();
    }

    private function allowToCalculatePayment(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'code' => 'nullable|exists:discounts',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return ['error' => true, 'message' => $validator->errors()->all()];
        }

        if ($this->isThereDiscountCode($request)) {
            return $this->applyDiscountOnPayment($request);
        }

        $user = $this->getUser();
        $cart = $user->LastUnpaidCart;
        return [
            'error' => false,
            'amountOfDiscount' => 0,
            'amountOfPayment' => $cart->final_cost,
            'cart' => $cart,
            'discount' => null
        ];
    }

    private function isThereDiscountCode(Request $request): bool
    {
        return $request->filled('code');
    }

    private function applyDiscountOnPayment(Request $request): array
    {
        $discount = Discount::where('code', $request->input('code'))->first();
        $d = new DiscountController;
        $result = $d->checkingValidation($discount);
        if ($result['error'])
            return $result;

        $content = $d->applyDiscountCode($discount, $result['discountedCost'], $result['cart']);
        return [
            'error' => false,
            'amountOfDiscount' => $content['amountOfDiscount'],
            'amountOfPayment' => $content['amountOfPayment'],
            'cart' => $result['cart'],
            'discount' => $discount
        ];
    }

    public function buy(Request $request)
    {
        $result = $this->allowToCalculatePayment($request);
        if ($result['error']) {
            return response($result);
        }
        $amountOfPayment = $result['amountOfPayment'];
        $cart = $result['cart'];
        $discount = $result['discount'];

        $factorNumber = $this->createFactorNumber();

        Transaction::create([
            'amount' => $amountOfPayment,
            'factorNumber' => $factorNumber,
            'cart_id' => $cart->id,
            'discount_id' => is_null($discount) ? null : $discount->id,
            'description' => $request->filled('description') ? $request->input('description') : null,
        ]);

        $res = $this->zpo->pay($amountOfPayment, route('verify', ['factorNumber' => $factorNumber]), null, null);
        return response($res);
    }

    public function verifyInfo(Request $request, $factorNumber)
    {
        $t = Transaction::where('factorNumber', $factorNumber)->firstOrFail();
        $result = $this->zpo->order($request, $t->amount);
        $t->description = $result['massage'];
        if ($result['ok']) {
            $t->condition = 'SUCCESSFUL';
            $t->save();
            $c = Cart::find($t->cart_id);
            $c->is_pay = true;
            $c->transaction_id = $t->id;
            $c->save();
            $this->cto->create($c->user_id);
            if ($t->discount_id != null) {
                $d = Discount::find($t->discount_id);
                $d->used_number = $d->used_number + 1;
                $d->save();
            }
        } else {
            $t->condition = 'FAILED';
            $t->save();
        }
        return response(null, 204);
    }

    public function myTransaction()
    {
        $user = $this->getUser();
        $carts = $user->Carts;
        $transactions = [];
        foreach ($carts as $cart) {
            $c = $cart->Transaction()->where('condition', 'SUCCESSFUL')->get()->toArray();
            if ($c != null) {
                $c[0]['produces'] = $cart->Exams()->select('name')->get()->toArray();
                array_push($transactions, $c[0]);
            }
        }
        return response($transactions);
    }

    private function createFactorNumber(): string
    {
        $factorNumber = Str::random(10);
        while (Transaction::where('factorNumber', $factorNumber)->exists()) {
            $factorNumber = Str::random(10);
        }
        return $factorNumber;
    }

}

