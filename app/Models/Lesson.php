<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;


    protected $fillable = ['name', 'number_of_questions', 'coefficient', 'field_id', 'education_base_id', 'category_id'];
    protected $visible = ['name', 'number_of_questions', 'coefficient'];
    protected $hidden = ['field_id', 'education_base_id', 'category_id', 'created_at', 'updated_at', 'deleted_at'];

    public function Field(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Field::class, 'field_id');
    }

    public function EducationBase(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(EducationBase::class, 'education_base_id');
    }

    public function Category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function Questions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'question_lesson');
    }

    public function Phases(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Phase::class, 'lesson_phase');
    }
}
