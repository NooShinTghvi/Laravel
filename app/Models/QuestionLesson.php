<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionLesson extends Model
{
    use HasFactory;

    protected $table = 'question_lesson';
    protected $fillable = ['question_id', 'lesson_id',];
    protected $visible = [];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
