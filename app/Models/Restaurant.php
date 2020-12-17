<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @method static find($restaurant_id)
 * @method static where(string $string, $idOrName)
 */
class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'logo', 'uid', 'location_id'];
    protected $hidden = ['id', 'location_id', 'created_at', 'updated_at'];

    public function location(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function foods(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Food::class, 'restaurant_id', 'id');
//        return DB::table('foods')
//            ->join('restaurants', function ($join) {
//                $join->on('foods.restaurant_id', '=', 'restaurants.id')
//                    ->where('restaurants.id', '=', $this->id);
//            })
//            ->leftJoin('discounted_foods', 'foods.df_id', '=', 'discounted_foods.id')
//            ->select('foods.name, foods.description, foods.price, foods.image, foods.popularity,
//            foods.uid, discounted_foods.new_price, discounted_foods.count')
//            ->get();
    }
}
