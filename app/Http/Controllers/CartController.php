<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Exam;
use App\Models\ExamCart;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function storeDataInSession($inputs)
    {
        if (!$this->existUnpaidCart($inputs['userId'])) {
            $this->create($inputs['userId']);
        }
        $cart = Cart::where('user_id', $inputs['userId'])->where('is_pay', false)->first();
        $exams = $cart->Exams;
        $examsInfo = $this->giveBackInfoOfExams($exams);
        if (session()->exists('cartId'))
            session()->remove('cartId');
        if (session()->exists('examsInfo'))
            session()->remove('examsInfo');
        if (session()->exists('finalCost'))
            session()->remove('finalCost');
        session()->put('cartId', $cart->id);
        session()->put('examsInfo', $examsInfo);
        session()->put('finalCost', $this->calculateExamsOnCart($exams));
    }

    private function existUnpaidCart($userId) // false--> create / true --> give back items of cart
    {
        return Cart::where('user_id', $userId)->where('is_pay', false)->exists();
    }

    private function create($userId)
    {
        Cart::create([
            'user_id' => $userId,
            'expire_date' => Carbon::now()->addMonths(3)
        ]);
    }

    private function giveBackInfoOfExams($exams): array
    {
        $examsInfo = [];
        foreach ($exams as $key => $exam) {
            $examsInfo[$key] = ['id' => $exam->id, 'price' => $exam->price];
        }
        return $examsInfo;
    }

    public function addExamToCart(Request $request, $examId)
    {

        if (session()->has('amountOfPayment'))
            session()->remove('amountOfPayment');
        $examsInfo = session()->get('examsInfo');
        $cartId = session()->get('cartId');

        foreach ($examsInfo as $key => $value)
            if ($value['id'] == $examId)
                // already added cart
                return redirect()->back()->with([
                    'code' => '201' ,
                    'message' => 'این درس به سبد خرید شما اضافه شده است' ,
                ]);

        $e = ExamCart::create([
            'cart_id' => $cartId,
            'exam_id' => $examId,
        ]);

        session()->push('examsInfo', ['id' => $examId, 'price' => Exam::find($examId)->price]);

        $this->calculateAddExam($examId);

//        return redirect('blank')->withErrors(['درس مورد نظر به سبد خرید شما اضافه شده است',
//            json_encode(session()->get('examsInfo'))], 'massages');
        return redirect()->back()->with([
            'code' => '200' ,
            'message' => 'درس مورد نظر به سبد خرید شما اضافه شده است' ,
        ]);
    }

    public function deleteExamFromCart(Request $request, $examId): \Illuminate\Http\RedirectResponse
    {
        if (session()->has('amountOfPayment'))
            session()->remove('amountOfPayment');

        $examsInfo = session()->get('examsInfo');
        $cartId = session()->get('cartId');


        $existExamOnCart = false;
        foreach ($examsInfo as $key => $value)
            if ($value['id'] == $examId) {
                $existExamOnCart = true;
                $this->calculateDeleteExam($value['price']);
                unset($examsInfo[$key]);
                break;
            }
        if (!$existExamOnCart) {
            return redirect()->back()->withErrors(['درس مورد نظر در سبد خرید شما وجود ندارد',
                json_encode(session()->get('examsInfo'))], 'errors');
        }

        ExamCart::where('cart_id', $cartId)->where('exam_id', $examId)->delete();
        session()->forget('examsInfo');
        session()->put('examsInfo', $examsInfo);

        return redirect()->back()->withErrors([
            'code' => 200 ,
            'message' => 'درس مورد نظر حذف شد'
        ]);
    }

    public function showExamsOnCart()
    {
        $examIds = [];
        $examsInfo = session()->get('examsInfo');
        foreach ($examsInfo as $key => $value)
            array_push($examIds, $value['id']);
        $exams = Exam::find($examIds)->toArray();

        return view('cart::basket')->with(compact('exams'));
    }

    private function calculateExamsOnCart($exams)
    {
        $sum = 0;
        foreach ($exams as $exam) {
            $sum += $exam->price;
        }
        return $sum;
    }

    private function calculateAddExam($examId)
    {
        $newCost = session()->get('finalCost') + Exam::find($examId)->price;
        session()->forget('finalCost');
        session()->put('finalCost', $newCost);
    }

    private function calculateDeleteExam($price)
    {
        $newCost = session()->get('finalCost') - $price;
        session()->forget('finalCost');
        session()->put('finalCost', $newCost);
    }

    public function addImmediateExam($examId): \Illuminate\Http\RedirectResponse
    {
        if (session()->has('amountOfPayment'))
            session()->remove('amountOfPayment');
        $examsInfo = session()->get('examsInfo');
        $cartId = session()->get('cartId');

        foreach ($examsInfo as $key => $value)
            if ($value['id'] == $examId)
                // already added cart
                return redirect()->back()->with([
                    'code' => '201' ,
                    'message' => 'این درس به سبد خرید شما اضافه شده است' ,
                ]);

        $e = ExamCart::create([
            'cart_id' => $cartId,
            'exam_id' => $examId,
        ]);

        session()->push('examsInfo', ['id' => $examId, 'price' => Exam::find($examId)->price]);

        $this->calculateAddExam($examId);

//        return redirect('blank')->withErrors(['درس مورد نظر به سبد خرید شما اضافه شده است',
//            json_encode(session()->get('examsInfo'))], 'massages');
        return redirect()->route('cart.showProducts');
    }
}
