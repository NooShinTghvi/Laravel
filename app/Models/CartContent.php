<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 */
class CartContent extends Model
{
    use HasFactory;

    protected $fillable = ['cart_id', 'food_id'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'cart_id', 'food_id', 'count'];
}
