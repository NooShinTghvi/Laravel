<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @method static find($phaseId)
 */
class Phase extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'number', 'exam_id', 'date', 'time_start', 'time_end', 'duration', 'negative_score',];
    protected $visible = ['id', 'name', 'number', 'date', 'time_start', 'time_end', 'duration', 'image_path', 'negative_score',];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['date'];

    public function Exam(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function Lessons(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Lesson::class, 'lesson_phase');
    }

    public function Users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_phase', 'user_id', 'phase_id');
    }

    public function Reports(): \Illuminate\Support\Collection
    {
        //user_phase
        return DB::table('users')
            ->join('user_phase', function ($join) {
                $join->on('users.id', '=', 'user_phase.user_id')
                    ->where('user_phase.phase_id', '=', $this->id);
            })
            ->leftJoin('education_bases', 'users.education_base_id', '=', 'education_bases.id')
            ->leftJoin('fields', 'users.field_id', '=', 'fields.id')
            ->leftJoin('city', 'users.city_id', '=', 'city.id')
            ->select('users.id', 'users.first_name', 'users.last_name', 'users.melli_code', 'education_bases.name AS education base',
                'fields.name AS field', 'user_phase.rating', 'user_phase.grade', 'user_phase.balance', 'city.name AS city')
            ->get();
    }
}
