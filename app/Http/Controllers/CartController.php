<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Exam;
use App\Models\ExamCart;
use Carbon\Carbon;

class CartController extends Controller
{
    public function shoppingCartPreparation()
    {
        $user = $this->getUser();

        if (!$this->existUnpaidCart($user->id)) {
            $this->create($user->id);
        }
        $cart = $user->LastUnpaidCart;
        $exams = $cart->Exams;

        $examsInfo = $this->giveBackInfoOfExams($exams);
        $finalCost = $this->calculateExamsOnCart($exams);

        $cart->exam_info = json_encode($examsInfo);
        $cart->final_cost = $finalCost;
        $cart->save();

        return response(['cart' => $cart]);
    }

    private function existUnpaidCart($userId) // false--> create / true --> give back items of cart
    {
        return Cart::where('user_id', $userId)->where('is_pay', false)->exists();
    }

    private function create($userId)
    {
        Cart::create([
            'user_id' => $userId,
            'expire_date' => Carbon::now()->addMonths(1)
        ]);
    }

    private function giveBackInfoOfExams($exams): array
    {
        $examsInfo = [];
        foreach ($exams as $exam) {
            array_push($examsInfo, [
                'id' => $exam->id,
                'name' => $exam->name,
                'price' => $exam->price,
                'image' => $exam->image_path
            ]);
        }
        return $examsInfo;
    }

    private function calculateExamsOnCart($exams)
    {
        $sum = 0;
        foreach ($exams as $exam) {
            $sum += $exam->price;
        }
        return $sum;
    }

    public function addExamToCart($examId)
    {
        $user = $this->getUser();

        $cart = $user->LastUnpaidCart;
        $examsInfo = json_decode($cart->exam_info, true);;

        foreach ($examsInfo as $item)
            if ($item['id'] == $examId)
                return response(['error' => true, 'message' => 'Already added to cart.',]);

        ExamCart::create([
            'cart_id' => $cart->id,
            'exam_id' => $examId,
        ]);

        $exam = Exam::find($examId);
        array_push($examsInfo, [
            'id' => $exam->id,
            'name' => $exam->name,
            'price' => $exam->price,
            'image' => $exam->image_path
        ]);

        $cart->exam_info = json_encode($examsInfo);
        $cart->final_cost = $cart->final_cost + $exam->price;
        $cart->save();

        return response(['error' => false, 'message' => 'done successfully',]);
    }

    public function deleteExamFromCart($examId)
    {
        $user = $this->getUser();

        $cart = $user->LastUnpaidCart;
        $examsInfo = json_decode($cart->exam_info, true);

        $existExamOnCart = false;
        foreach ($examsInfo as $key => $item)
            if ($item['id'] == $examId) {
                $existExamOnCart = true;
                $cart->final_cost = $cart->final_cost - $item['price'];
                unset($examsInfo[$key]);
                break;
            }
        if (!$existExamOnCart) {
            return response(['error' => true, 'message' => 'exam does not exist in shopping cart']);

        }

        $cart->exam_info = json_encode($examsInfo);
        $cart->save();

        ExamCart::where('cart_id', $cart->id)->where('exam_id', $examId)->delete();

        return response(['error' => false, 'message' => 'deleted successfully.']);
    }
}
