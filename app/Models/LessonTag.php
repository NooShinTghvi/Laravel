<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonTag extends Model
{
    use HasFactory;


    protected $fillable = ['name',];
    protected $visible = ['id', 'name',];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function Lesson(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function Exams(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'exam_lesson_tag', 'lesson_tag_id', 'exam_id');
    }
}
