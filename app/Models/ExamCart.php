<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 * @method static where(string $string, mixed $cartId)
 */
class ExamCart extends Model
{
    use HasFactory;

    protected $table = 'exam_cart';
    protected $fillable = ['exam_id', 'cart_id',];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
}

