<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static find($df_id)
 */
class DiscountedFood extends Model
{
    use HasFactory;

    protected $table = 'discounted_foods';
    protected $fillable = ['new_price', 'count'];
    protected $hidden = ['id', 'created_at', 'updated_at'];
}
