<?php

namespace App\Http\Controllers;

use App\Exports\ResultsExportExcel;
use App\Exports\UsrWBghtExmExportExcel;
use App\Models\EducationBase;
use App\Models\Exam;
use App\Models\Field;
use App\Models\LessonTag;
use App\Models\Phase;
use App\Models\UPLesson;
use App\Models\UPLQuestion;
use App\Models\User;
use App\Models\UserPhase;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class ExamController extends Controller
{
    public function showAll()
    {
        return response([
            '$exams' => Exam::ShowAllExam(),
            'lessonTags' => LessonTag::all(),
            'fields' => Field::all(),
            'educationBases' => EducationBase::all()
        ]);
    }

    public function detailOne($examId)
    {
        return response(['exam' => Exam::DetailOfExam($examId),]);
    }

    public function allPhases($examId)
    {
        return response([
            'phases' => Exam::AllPhases($examId),
            'exam_id' => $examId
        ]);
    }

    public function filterExams(Request $request): string
    {
        $exams = Exam::ShowAllExam();
        $lessonTags = LessonTag::all()->toArray();
        $fields = Field::all()->toArray();
        $educationBases = EducationBase::all()->toArray();
        if ($request->filled('field')) {
            foreach ($exams as $k => $exam) {
                if (!in_array($exam['field_id'], array(json_decode($request->get('field')), true)))
                    unset($exams[$k]);
            }
        }
        if ($request->filled('educationBase')) {
            foreach ($exams as $k => $exam) {
                if (!in_array($exam['education_base_id'], array(json_decode($request->get('educationBase'), true))))
                    unset($exams[$k]);
            }
        }
        if ($request->filled('lessonTags')) {
            $lessonTags = $request->get('lessonTags');
            $deleteExam = true;
            foreach ($exams as $k => $exam) {
                $deleteExam = true;
                foreach ($exam['lessons'] as $kl => $vl) {
                    if (in_array($vl['id'], array(json_decode($request->get('lessonTags')), true))) {
                        $deleteExam = false;
                    }
                }
                if ($deleteExam) {
                    unset($exams[$k]);
                }
            }
        }

        return response([
            '$exams' => $exams,
            'lessonTags' => $lessonTags,
            'fields' => $fields,
            'educationBases' => $educationBases
        ]);
    }

    public function myExams()
    {
        $user = $this->getUser();
        $carts = $user->Carts;
        $myExams = [];
        foreach ($carts as $cart) {
            if ($cart->is_pay) {
                $exams = $cart->Exams()->get();
                foreach ($exams as $exam)
                    array_push($myExams, $exam->toArray());
            }
        }
        return response(['exams' => $myExams]);
    }

    public function entranceExam($examId, $phaseId)
    {
        return response([
            'exam' => Exam::DetailOfExam($examId),
            'phase' => Phase::find($phaseId)->toArray()
        ]);
    }


    public function canUserStartTest($examId, $phaseId)
    {
        $phase = Phase::find($phaseId);
        $crnDate = Carbon::now()->toDateString();
        $crnTime = Carbon::now()->toTimeString();
        $phaseDate = $phase->date->toDateString();

        if ($crnDate < $phaseDate or ($crnDate == $phaseDate and $crnTime < $phase->time_start)) {
            return response(['error' => true, 'message' => 'زمان آزمون هنوز شروع نشده است']);
        } else if ($crnDate > $phaseDate or ($crnDate == $phaseDate and $crnTime > $phase->time_end)) {
            return response(['error' => true, 'message' => 'زمان آزمون تمام شده است']);
        } else {
            $user = $this->getUser();
            if (!$this->buyUserExam($user->id, $examId)) {
                return response(['error' => true, 'message' => 'برای شرکت در این مرحله آزمون را خریداری کنید']);
            }
            if ($user->melli_image_path == null) {
                return response(['error' => true, 'message' => 'هنوز فایل کارت ملی خود را آپلود نکرده اید']);
            }
            if (!$user->isAcceptable) {
                return response(['error' => true, 'message' => 'هنوز فایل کارت ملی شما تایید نشده است, منتظر بمانید']);
            }
            $up_ = UserPhase::where('user_id', $user->id)->where('phase_id', $phaseId)->first();
            if ($up_ != null) {
                $t = $up_->created_at;
                if ($crnTime > $t->addMinutes($phase->duration)->toTimeString()) {
                    return response(['error' => true, 'message' => 'زمان آزمون دادن شما تمام شده است']);
                } else {
                    if ($up_->finish == null) {
                        $test = $this->questionsOfPhase($phase);
                        return response([
                            'test' => $test,
                            'duration' => $phase->duration,
                            'phaseId' => $phase->id,
                            'phase' => $phase
                        ]);
                    } else {
                        return response(['error' => true, 'message' => 'شما پاسخ نامه ی خود را ارسال کرده اید']);
                    }
                }
            } else {
                UserPhase::create([
                    'user_id' => $user->id,
                    'phase_id' => $phaseId,
                ]);
                $test = $this->questionsOfPhase($phase);
                return response([
                    'test' => $test,
                    'duration' => $phase->duration,
                    'phaseId' => $phase->id,
                    'phase' => $phase
                ]);
            }
        }
    }

    public function buyUserExam($userId, $examId): bool
    {
        $carts = User::find($userId)->Carts()->where('is_pay', true)->get();
        foreach ($carts as $cart) {
            foreach ($cart->Exams()->get() as $exam) {
                if ($exam->id == $examId)
                    return true;
            }
        }
        return false;
    }

    public function questionsOfPhase($phase): array
    {
        $lessons = $phase->Lessons()->get();
        $test = array();
        foreach ($lessons as $lesson) {
            array_push($test, [$lesson->name . '%!' . $lesson->id => $lesson->Questions()->get()->toArray()]);
        }
        return $test;
    }


    public function submitTest($lessons, $phaseId)
    {
        $up = UserPhase::where('user_id', session()->get('userId'))->where('phase_id', $phaseId)->first();
        $up->finish = Carbon::now()->toTimeString();
        $up->save();
        foreach ($lessons as $key => $value) {
            $upl = UPLesson::create([
                'u_p_id' => $up->id,
                'lesson_id' => $key
            ]);
            foreach ($value as $k => $v) {
                UPLQuestion::create([
                    'u_p_l_id' => $upl->id,
                    'question_id' => $k,
                    'selected_choice' => $v['q'],
                    'status' => $v['s']
                ]);
            }
        }
    }

    public function handle($phaseId)
    {
        $lessons = [];
        $inputs = request()->all();
        $keys = array_keys($inputs);
        for ($k = 0; $k < sizeof($keys); $k++) {
            if ($inputs[$keys[$k]] == '_token') {
                continue;
            }
            $questionOrStatus = preg_split("~_~", $keys[$k]);
            if ($questionOrStatus[0] == 'status') {
                $sTemp = preg_split("~_~", $inputs[$keys[$k]]);
                $lessons[$sTemp[0]][$sTemp[1]]['s'] = $sTemp[2];
                $lessons[$sTemp[0]][$sTemp[1]]['q'] = '#';
                continue;
            } else if ($questionOrStatus[0] == 'question') { // is question
                $sTemp = preg_split("~_~", $inputs[$keys[$k]]);
                $lessons[$sTemp[0]][$sTemp[1]]['q'] = $sTemp[2];
                $k++;
                $sTemp = preg_split("~_~", $inputs[$keys[$k]]);
                $lessons[$sTemp[0]][$sTemp[1]]['s'] = $sTemp[2];
            }
        }

        $this->submitTest($lessons, $phaseId);
        return response(['exam_id' => Phase::find($phaseId)->Exam->id]);
    }

    public function downloadAnswer($phaseId)
    {
        $phase = Phase::find($phaseId);
        if ($path = $phase->file_of_answer_path)
            return \response()->download(public_path(env('IMAGE_PATH_PREFIX') . $path));
        $message = "متاسفانه فایل پاسخنامه فاز " . $phase->name . " از آزمون " . $phase->Exam->name . " در دسترس نمیباشد";
        return response(['error' => true, 'message' => $message]);
    }

    public function downloadQuestion($phaseId)
    {
        $phase = Phase::find($phaseId);
        if ($path = $phase->file_of_question_path)
            return \response()->download(public_path(env('IMAGE_PATH_PREFIX') . $path));
        $message = "متاسفانه دفترچه سوالات فاز " . $phase->name . " از آزمون " . $phase->Exam->name . " در دسترس نمیباشد";
        return response(['error' => true, 'message' => $message]);
//        return \response()->download(public_path(env('IMAGE_PATH_PREFIX').Phase::find($phaseId)->file_of_question_path));
    }

    public function export()
    {
//        return Excel::download(new ResultsExportExcel(1), 'Re.xlsx');
    }
}
