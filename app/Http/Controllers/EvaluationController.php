<?php

namespace App\Http\Controllers;

use App\Models\LessonPhase;
use App\Models\Phase;
use App\Models\UPLesson;
use App\Models\UPLQuestion;
use App\Models\UserPhase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class EvaluationController extends Controller
{
    private $numberOfParticipants = []; // all user exam test
    private $negativeScore;
    private $testKeys = [];
    private $lessonCoefficient = [];
    private $numberOfQuestionPerLesson = [];
    private $allUserGradeInOneLesson = []; // sum of all user grade for one lesson
    private $allUserStandardDeviationInOneLesson = [];//sum of all user standard deviation for one lesson
    private $testDetailsOfEachIndividual = [];
    private $lessonUserBalance = [];

    public function obtainSomeInfo($phaseId)
    {
        $phase = Phase::find($phaseId);
        $lessons = $phase->Lessons()->get();
        $this->negativeScore = $phase->negative_score;
        foreach ($lessons as $lesson) {
            $this->lessonCoefficient [$lesson->id] = $lesson->coefficient;
            $questions = $lesson->Questions()->get();
            $this->numberOfQuestionPerLesson [$lesson->id] = sizeof($questions);
            foreach ($questions as $question) {
                $this->testKeys [$question->id] = $question->answer;
            }
        }
    }

    public function lessonCorrection($phaseId): string
    {
        $this->obtainSomeInfo($phaseId);
        $user_phases = UserPhase::where('phase_id', $phaseId)->get();
        $this->numberOfParticipants = sizeof($user_phases);
        foreach ($user_phases as $user_phase) {
            $up_lessons = UPLesson::where('u_p_id', $user_phase->id)->get();
            $correct = 0;
            $wrong = 0;
            $notAnswered = 0;
            foreach ($up_lessons as $up_lesson) {
                $upl_questions = UPLQuestion::where('u_p_l_id', $up_lesson->id)->get();
                foreach ($upl_questions as $upl_question) {
                    if ($upl_question->selected_choice == $this->testKeys[$upl_question->question_id])
                        $correct++;
                    else if ($upl_question->selected_choice == '#')
                        $notAnswered++;
                    else
                        $wrong++;
                }
                $up_lesson->correct_question = $correct;
                $up_lesson->incorrect_question = $wrong;
                $up_lesson->unanswered_question = $notAnswered;
                $cc = $correct * $this->negativeScore; // correct * negative score
                $tc = $this->numberOfQuestionPerLesson[$up_lesson->lesson_id] * $this->negativeScore; // total * negative score
                $up_lesson->grade = (($cc - $wrong) / $tc) * 100;
                if (empty($this->allUserGradeInOneLesson[$up_lesson->lesson_id]))
                    $this->allUserGradeInOneLesson[$up_lesson->lesson_id] = 0;
                $this->allUserGradeInOneLesson[$up_lesson->lesson_id] += $up_lesson->grade;
                $this->testDetailsOfEachIndividual[$user_phase->user_id][$up_lesson->lesson_id]['grade'] = $up_lesson->grade;
                $up_lesson->save();
                $correct = 0;
                $wrong = 0;
                $notAnswered = 0;
            }
        }

        /*$this->calculateAverageAndStandardDeviationOfEachLesson($phaseId);
        $this->calculateBalanceOfLessonEachUser($phaseId);*/
        return 'ok';
    }

    /*public function calculateAverageAndStandardDeviationOfEachLesson($phaseId)
    {
        $lps = LessonPhase::where('phase_id', $phaseId)->get();
        $s = 0;
        foreach ($this->allUserGradeInOneLesson as $lk => $lv) { //$lk --> lesson key / $lv --> lesson value
            foreach ($this->testDetailsOfEachIndividual as $usr) {
                $s += pow(($usr[$lk]['grade'] - ($lv / $this->numberOfParticipants)), 2);
            }
            $this->allUserStandardDeviationInOneLesson[$lk] = sqrt($s / $this->numberOfParticipants);
            $s = 0;
            $lp = $lps->where('lesson_id', $lk)->first();
            $lp->average = $this->allUserGradeInOneLesson[$lk] / $this->numberOfParticipants;
            $lp->standard_deviation = $this->allUserStandardDeviationInOneLesson[$lk];
            $lp->save();
        }
    }

    public function calculateBalanceOfLessonEachUser($phaseId)
    {
        $grade = 0; // grade of each user
        $balance = 0; // balance of each user
        $users_balance = [];
        $sumOfCoefficients = array_sum($this->lessonCoefficient);
        $user_phases = UserPhase::where('phase_id', $phaseId)->get();
        foreach ($user_phases as $user_phase) {
            $up_lessons = UPLesson::where('u_p_id', $user_phase->id)->get();
            foreach ($up_lessons as $up_lesson) {
                $gu = $this->testDetailsOfEachIndividual[$user_phase->user_id][$up_lesson->lesson_id]['grade']; //lesson grade of user
                $ta = $this->allUserGradeInOneLesson[$up_lesson->lesson_id] / $this->numberOfParticipants; //total average
                $tsd = $this->allUserStandardDeviationInOneLesson[$up_lesson->lesson_id]; //total standard deviation
                if ($tsd == 0) $tsd = 1; //todo check it !
                $z = (1000 * (($gu - $ta) / $tsd)) + 5000;
                $up_lesson->balance = $z;
                $up_lesson->save();
                $this->testDetailsOfEachIndividual[$user_phase->user_id][$up_lesson->lesson_id]['balance'] = $z;
                $this->lessonUserBalance[$up_lesson->lesson_id][$user_phase->user_id] = $z;
                $grade += ($gu * $this->lessonCoefficient[$up_lesson->lesson_id]);
                $balance += ($z * $this->lessonCoefficient[$up_lesson->lesson_id]);
            }
            $user_phase->grade = $grade / $sumOfCoefficients;
            $user_phase->balance = $balance / $sumOfCoefficients;
            $users_balance[$user_phase->user_id] = $user_phase->balance; //for calculating user rank
            $user_phase->save();
            $grade = 0;
            $balance = 0;
        }
        $this->calculateRankOfUser($user_phases, $users_balance);
        $this->calculateIndividualRankInEachLesson($user_phases, $phaseId);
    }*/

    public function calculateRankOfUser($user_phases, $users_balance)
    {
        $ur = []; // user rank
        arsort($users_balance);
        $rate = 1;
        $bestBalance = $users_balance[array_key_first($users_balance)];
        foreach ($users_balance as $userId => $balance) {
            if ($balance == $bestBalance) {
                $ur[$userId] = $rate;
            } else {
                $rate++;
                $bestBalance = $balance;
            }
        }
        foreach ($user_phases as $up) {
            $up->rating = $ur[$up->user_id];
            $up->save();
        }
    }

    public function calculateIndividualRankInEachLesson($user_phases, $phaseId)
    {
        $ulr = []; //user lesson rank
        foreach ($this->lessonUserBalance as $lk => $ub) {
            $lessonOfPhase = LessonPhase::where('lesson_id', $lk)->where('phase_id', $phaseId)->first();
            arsort($this->lessonUserBalance[$lk]);
            $rate = 1;
            $akf = array_key_first($this->lessonUserBalance[$lk]);
            $bestBalance = $this->lessonUserBalance[$lk][$akf];
            $highestPercentage = UPLesson::where('lesson_id', $lk)->where('balance',$bestBalance)->first();
            $lessonOfPhase->highest_balance = $highestPercentage->grade;
            $lessonOfPhase->save();
            foreach ($ub as $u => $b) {
                if ($b == $bestBalance) {
                    $ulr[$u][$lk] = $rate;
                } else {
                    $rate++;
                    $ulr[$u][$lk] = $rate;
                    $bestBalance = $b;
                }
            }
        }

        foreach ($user_phases as $up) {
            $up_lessons = UPLesson::where('u_p_id', $up->id)->get();
            foreach ($up_lessons as $up_lesson) {
                $up_lesson->rating = $ulr[$up->user_id][$up_lesson->lesson_id];
                $up_lesson->save();
            }
        }
    }


    public function reportOfTest($phaseId)
    {
        $report = [];
        $phase=Phase::find($phaseId);
        $exam=$phase->Exam;
//        return view('exam::report')->with(compact('phase'))->with(compact('exam'));
        $ups = UserPhase::where('phase_id', $phaseId)->get();
        $up = $ups->where('user_id', session()->get('userId'))->first();
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

        return view('exam::report')
            ->with(compact('phase'))
            ->with(compact('exam'))
                ->with(compact('report'));
//        dd(json_encode($report));
//        $phase = Phase::find($phaseId);
//        return view("exam::report")
//            ->with(compact('report'))
//            ->with(compact('phase'));
    }

    public function reportAll(){
        return view('exam::reportAll');
    }
}
