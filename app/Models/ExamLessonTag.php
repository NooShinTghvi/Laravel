<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamLessonTag extends Model
{
    use HasFactory;

    protected $table = 'exam_lesson_tag';
    protected $fillable = ['exam_id', 'lesson_tag_id'];
    protected $visible = [];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
