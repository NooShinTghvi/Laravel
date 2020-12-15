<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JetBrains\PhpStorm\Pure;

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
        return $nearRestaurants;
    }

    public function findRestaurant($idOrName): string
    {
        $user = Auth::user();

        $restaurant = Restaurant::where('uid', $idOrName)->first();
        if (is_null($restaurant)) {
            $restaurant = Restaurant::where('name', $idOrName)->first();
        }
        if (!is_null($restaurant)) {
            if ($this->isNear($user->location->x, $user->location->y, $restaurant->location->y, $restaurant->location->y))
                return $this->packingRestaurantWithFoodsData($restaurant);
            else
                return 'restaurant is not around of You!';
        }
        return 'restaurant not found!';
    }

    public function isNear($userLocationX, $userLocationY, $restaurantLocationX, $restaurantLocationY): bool
    {
        return sqrt(pow($userLocationX - $restaurantLocationX, 2) + pow($userLocationY - $restaurantLocationY, 2)) < $this->radius;
    }

    private function packingRestaurantWithFoodsData($restaurant)
    {
        $restaurant->foods; // add foods
        return $restaurant;
    }
}
