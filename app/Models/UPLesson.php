<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UPLesson extends Model
{
    use HasFactory;

    protected $table = 'u_p_lesson';
    protected $fillable = ['u_p_id', 'lesson_id',];
    protected $visible = [];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function Lesson(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
