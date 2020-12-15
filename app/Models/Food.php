<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, $foodUid)
 * @method static find($food_id)
 */
class Food extends Model
{
    use HasFactory;

    protected $table = 'foods';
    protected $fillable = ['name', 'description', 'price', 'image', 'popularity', 'uid', 'restaurant_id', 'df_id'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'restaurant_id', 'df_id'];

    public function discountedFood(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Models\DiscountedFood', 'df_id');
    }
}
