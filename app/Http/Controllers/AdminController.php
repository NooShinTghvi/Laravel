<?php

namespace App\Http\Controllers;

use App\Models\LessonPhase;
use App\Models\UPLesson;
use App\Models\UserPhase;
use Illuminate\Routing\Controller;


class AdminController extends Controller
{
    public function report($phaseId, $userId)
    {
        $report = [];
        $ups = UserPhase::where('phase_id', $phaseId)->get();
        $up = $ups->where('user_id', $userId)->first();
        // رتبه ی کلی فرد
        $report['rating'] = $up->rating;
        // درصد هر فرد با احتساب نمره ی منفی
        $report['grade'] = $up->grade;
        //تراز کلی فرد
        $report['balance'] = $up->balance;
        $report['number_of_participants'] = sizeof($ups);
        $upls = UPLesson::where('u_p_id', $up->id)->get();
        foreach ($upls as $upl) {
            $l = $upl->Lesson()->first();
            $lessonOfPhase = LessonPhase::where('lesson_id', $l->id)->where('phase_id', $phaseId)->first();
            //بر اساس آی دی میگه اسم درس چی بوده و میانگین درس چقدر بوده
            $report['lessons'][] = [
                //اسم درس
                'name' => $l->name,
                //تعداد سوالات درس
                'numberOfQuestion' => $l->number_of_questions,
                //ضریب هر درس
                'coefficient' => $l->coefficient,
                //میانگین کل دانش آموزهای شرکت کننده
                'average' => $lessonOfPhase->average,
                //انحراف از معیار درس
                'standard_deviation' => $lessonOfPhase->standard_deviation,
                //بالاترین درصد
                'highest_balance' => $lessonOfPhase->highest_balance,
                //تعداد درست ها
                'correct_question' => $upl->correct_question,
                //تعداد غلط ها
                'incorrect_question' => $upl->incorrect_question,
                //نزده ها
                'unanswered_question' => $upl->unanswered_question,
                //درصد فرد با نمره ی منفی
                'grade' => $upl->grade,
                //درصد فرد بدون نمره ی منفی
                'score' => ($upl->correct_question / ($upl->correct_question + $upl->incorrect_question + $upl->unanswered_question)) * 100,
                //تراز خودش
                'balance' => $upl->balance,
                //رتبه فرد در درس
                'rating' => $upl->rating,
            ];
        }

        return response($report);
    }
}
