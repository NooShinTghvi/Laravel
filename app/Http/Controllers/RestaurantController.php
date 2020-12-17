<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends Controller
{
    private int $radius = 120;

    public function findRestaurantAroundUser(): array
    {
        $user = Auth::user();

        $restaurants = Restaurant::all();
        $nearRestaurants = array();
        foreach ($restaurants as $restaurant) {
            if ($this->isNear($user->location->x, $user->location->y, $restaurant->location->y, $restaurant->location->y)) {
                array_push($nearRestaurants, $restaurant);
            }
        }
        return $this->myResponse('success', [], ['restaurants' => $nearRestaurants]);
    }

    public function findRestaurant($idOrName): array
    {
        $user = Auth::user();

        $restaurant = Restaurant::where('uid', $idOrName)->first();
        if (is_null($restaurant)) {
            $restaurant = Restaurant::where('name', $idOrName)->first();
        }
        if (!is_null($restaurant)) {
            if ($this->isNear($user->location->x, $user->location->y, $restaurant->location->y, $restaurant->location->y))
                return $this->myResponse('success', [], ['restaurant' => $this->packingRestaurantWithFoodsData($restaurant)]);
            else
                return $this->myResponse('error', ['restaurant is not around of You!'], []);
        }
        return $this->myResponse('error', ['restaurant not found!'], []);
    }

    public function isNear($userLocationX, $userLocationY, $restaurantLocationX, $restaurantLocationY): bool
    {
        return sqrt(pow($userLocationX - $restaurantLocationX, 2) + pow($userLocationY - $restaurantLocationY, 2)) < $this->radius;
    }

    private function packingRestaurantWithFoodsData($restaurant): array
    {
        $restaurant->foods; // add foods
        $discountedFood = [];
        $foods = [];
        foreach ($restaurant['foods'] as $f) {
            if (is_null($f->df_id))
                array_push($foods, $f);
            else
                array_push($discountedFood, [
                    'name' => $f->name,
                    'description' => $f->description,
                    'price' => $f->price,
                    'image' => $f->image,
                    'popularity' => $f->popularity,
                    'uid' => $f->uid,
                    'new_price' => $f->discountedFood->new_price,
                    'count' => $f->discountedFood->count
                ]);
        }
        return [
            'name' => $restaurant['name'],
            'logo' => $restaurant['logo'],
            'uid' => $restaurant['uid'],
            'location' => $restaurant['location'],
            'foods' => $foods,
            'discountedFood' => $discountedFood
        ];
    }
}
