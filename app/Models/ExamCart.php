<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamCart extends Model
{
    use HasFactory;

    protected $table = 'exam_cart';
    protected $fillable = ['exam_id', 'cart_id',];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}

