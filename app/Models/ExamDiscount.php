<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamDiscount extends Model
{
    use HasFactory;

    protected $table = 'exam_discount';
    protected $fillable = ['exam_id', 'discount_id',];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}
