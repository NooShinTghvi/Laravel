<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @method static select(string $string, string $string1, string $string2, string $string3, string $string4, string $string5)
 * @method static find($id)
 */
class Exam extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'number_of_phases', 'day_of_holding', 'field_id', 'education_base_id', 'price'];
    protected $visible = ['id', 'name', 'number_of_phases', 'day_of_holding', 'price', 'field_id', 'education_base_id',
        'image_path', 'description', 'description_file'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function Discounts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Discount::class, 'discount_exam');
    }

    public function Carts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Cart::class, 'exam_cart');
    }

    public function Field(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Field::class, 'field_id');
    }


    public function EducationBase(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(EducationBase::class, 'education_base_id');
    }


    public function Phases(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Phase::class);
    }

    public function LessonTags(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(LessonTag::class, 'exam_lesson_tag');
    }

    static public function ShowAllExam()
    {
        $exams = Exam::select('id', 'name', 'price', 'field_id', 'education_base_id', 'image_path')->get();
        $data = json_decode(json_encode($exams), true);
        foreach ($exams as $k => $exam) {
            $data[$k]['lessons'] = $exam->LessonTags()->get()->toArray();
        }
        Controller::ReturnParentValue(
            Field::class, 'field_id', 'name', $data, 'field_name');
        Controller::ReturnParentValue(
            EducationBase::class, 'education_base_id', 'name', $data, 'education_base_name');
        return $data;
    }

    static public function DetailOfExam($id) //show to users
    {
        $exam = Exam::find($id);
        $data[0] = $exam->toArray();
        $data [0]['lessons'] = $exam->LessonTags()->get()->toArray();
        Controller::ReturnParentValue(
            Field::class, 'field_id', 'name', $data, 'field_name');
        Controller::ReturnParentValue(
            EducationBase::class, 'education_base_id', 'name', $data, 'education_base_name');
        return $data[0];
    }

    static public function AllPhases($id) //show some data to users
    {
        return json_decode(json_encode(Exam::find($id)->Phases()->select(
            'id', 'name', 'number', 'date', 'time_start', 'time_end', 'duration', 'image_path')->get()), true);
    }

    public function usersWhoBoughtExam(): \Illuminate\Support\Collection
    {
        /*$carts = $this->Carts()->where('is_pay', true)->get();
        $users = [];
        foreach ($carts as $cart) {
            $users[] = $cart->User()->get();
        }
        return $users;*/
        /*return DB::select('select      u.id,
                                   u.first_name,
                                   u.last_name,
                                   u.email,
                                   u.password,
                                   u.melli_code,
                                   u.mobile,
                                   u.field_id,
                                   u.education_base_id,
                                   u.city_id,
                                   u.melli_image_path,
                                   u.isAcceptable,
                                   u.remember_token,
                                   u.created_at,
                                   u.updated_at
                        from exams
                                     inner join exam_cart on exams.id = exam_cart.exam_id and exams.id=?
                                     inner join carts c on exam_cart.cart_id = c.id and c.is_pay = 1
                                     inner join users u on c.user_id = u.id'

            , [$this]);*/
        return DB::table('exams')
            ->join('exam_cart', function ($join) {
                $join->on('exams.id', '=', 'exam_cart.exam_id')
                    ->where('exams.id', '=', $this->id);
            })
            ->join('carts', function ($join) {
                $join->on('exam_cart.cart_id', '=', 'carts.id')
                    ->where('carts.is_pay', '=', true);
            })
            ->join('users', 'carts.user_id', '=', 'users.id')
            ->leftJoin('education_bases', 'users.education_base_id', '=', 'education_bases.id')
            ->leftJoin('fields', 'users.field_id', '=', 'fields.id')
            ->leftJoin('city', 'users.city_id', '=', 'city.id')
            ->select('users.first_name', 'users.last_name', 'users.mobile',
                'users.melli_code', 'education_bases.name AS education base', 'fields.name AS field', 'city.name AS city', 'users.email', 'users.isAcceptable')
            ->get();
    }
}
