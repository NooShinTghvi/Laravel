<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonPhase extends Model
{
    use HasFactory;

    protected $table = 'lesson_phase';
    protected $fillable = ['lesson_id', 'phase_id',];
    protected $visible = [];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
