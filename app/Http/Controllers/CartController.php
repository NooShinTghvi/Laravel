<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Models\ShoppingCart;
use App\Models\Cart;
use App\Models\CartContent;
use App\Models\DiscountedFood;
use App\Models\Food;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private $radius = 120;

    public function addFood($restaurantUid, $foodUid): string
    {
        $user = Auth::user();
        $restaurantStatus = $this->getStatusRestaurant($user, $restaurantUid);
        if ($restaurantStatus['id'] == -1)
            return 'restaurant not found';
        elseif (!$restaurantStatus['isNear'])
            return 'restaurant is not around of You!';
        $restaurantId = $restaurantStatus['id'];

        $cart = $this->createOrGetCart($user, $restaurantId);
        if ($restaurantId != $cart->restaurant_id)
            return 'multiple restaurants';

        $food = $this->getFood($foodUid, $restaurantId);
        if (is_null($food))
            return 'food not found';

        return $this->addFoodToCart($cart, $food);
    }

    private function getStatusRestaurant($user, $restaurantUid): array
    {
        $restaurant = Restaurant::where('uid', $restaurantUid)->first();
        if (is_null($restaurant))
            return array('id' => -1, 'isNear' => null);
        if ($this->isNear($user->location->x, $user->location->y, $restaurant->location->x, $restaurant->location->y))
            return array('id' => $restaurant->id, 'isNear' => true);
        return array('id' => $restaurant->id, 'isNear' => false);

    }

    public function isNear($userLocationX, $userLocationY, $restaurantLocationX, $restaurantLocationY): bool
    {
        return sqrt(pow($userLocationX - $restaurantLocationX, 2) + pow($userLocationY - $restaurantLocationY, 2)) < $this->radius;
    }

    private function getFood($foodUid, $restaurantId)
    {
        return Food::where('uid', $foodUid)->where('restaurant_id', $restaurantId)->first();
    }

    private function createOrGetCart($user, $restaurantId)
    {
        $cart = $user->cart;
        if (is_null($cart)) {
            $cart = Cart::create([
                'user_id' => $user->id,
                'restaurant_id' => $restaurantId
            ]);
        }
        return $cart;
    }

    private function addFoodToCart($cart, $food): string
    {
        $cartContents = $cart->cartContents;
        $cartId = $cart->id;
        $foodId = $food->id;
        if ($this->isFoodDiscounted($food))
            if (!$this->canUpdateDiscountedFoodData($food))
                return 'food is over';
        if (!is_null($cartContents) && !is_null($cartContents->where('food_id', $foodId)->first())) {
            $cartContents->where('food_id', $foodId)->first()->increment('count');
        } else {
            CartContent::create([
                'cart_id' => $cartId,
                'food_id' => $foodId
            ]);
        }
        return 'done';
    }

    private function isFoodDiscounted($food): bool
    {
        return !is_null($food->df_id);
    }

    private function canUpdateDiscountedFoodData($food): bool
    {
        $discountedFood = $food->discountedFood;
        if ($discountedFood->count > 0) {
            $discountedFood->decrement('count');
            $discountedFood->save();
            return true;
        } else
            return false;
    }

    public function getCart(): array|string
    {
        $user = Auth::user();
        $cart = $user->cart;
        if (is_null($cart))
            return 'cart is empty';
        $cartContents = $cart->cartContents;
        if (is_null($cartContents))
            return 'cart is empty';
        $shoppingCart = new ShoppingCart(Restaurant::find($cart->restaurant_id)->first()->name);
        foreach ($cartContents as $cartContent) {
            $food = Food::find($cartContent->food_id)->first();
            if (!is_null($food->df_id)) { //add Discounted Food
                $discountedFood = DiscountedFood::find($food->df_id)->first();
                $shoppingCart->addDiscountedFood($food->name, $cartContent->count, $discountedFood->new_price);
            } else { //add Food
                $shoppingCart->addFood($food->name, $cartContent->count, $food->price);
            }
        }
        return $shoppingCart->toJson();
    }

    public function submit(): array |string
    {
        $user = Auth::user();
        $cart = $user->cart;
        if (is_null($cart))
            return 'cart is empty';
        $cartContents = $cart->cartContents;
        if (is_null($cartContents))
            return 'cart is empty';
        $price = $this->calculateSumOfPriceOrders($cartContents);
        if ($user->credit < $price)
            return 'not enough';
        $restaurantLocation = Restaurant::find($cart->restaurant_id)->first()->location;
        $deliveryController = new DeliveryController();
        $result = $deliveryController->findDelivery($user->location, $restaurantLocation);
        if ($result['found']){
            $cart->is_payed = true;
            $cart->save();
            $user->credit -= $price;
            $user->save();
        }
        return $result;

    }

    private function calculateSumOfPriceOrders($cartContents): int
    {
        $sum = 0;
        foreach ($cartContents as $cartContent) {
            $food = Food::find($cartContent->food_id)->first();
            if (!is_null($food->df_id)) { //add Discounted Food Price
                $discountedFood = DiscountedFood::find($food->df_id)->first();
                $sum += $discountedFood->new_price;
            } else { //add Food Price
                $sum += $food->price;
            }
        }
        return $sum;
    }
}
