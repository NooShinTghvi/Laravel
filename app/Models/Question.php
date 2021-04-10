<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['question_text', 'choice1', 'choice2', 'choice3', 'choice4', 'answer', 'category_id'];
    protected $visible = ['id', 'question_text', 'choice1', 'choice2', 'choice3', 'choice4',];
    protected $hidden = ['answer', 'category_id', 'created_at', 'updated_at', 'deleted_at'];

    public function Category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function Lessons(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Lesson::class, 'question_lesson');
    }
}
