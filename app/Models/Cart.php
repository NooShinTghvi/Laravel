<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 */
class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['is_payed', 'user_id', 'restaurant_id'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'is_payed', 'user_id', 'restaurant_id'];

    public function cartContents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CartContent::class);
    }
}
