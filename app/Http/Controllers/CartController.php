<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Models\ShoppingCart;
use App\Models\Cart;
use App\Models\CartContent;
use App\Models\DiscountedFood;
use App\Models\Food;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private int $radius = 120;

    private function preliminaryReview($restaurantUid, $foodUid): array
    {
        $user = Auth::user();
        $restaurantStatus = $this->getStatusRestaurant($user, $restaurantUid);
        if ($restaurantStatus['id'] == -1)
            return ['result' => 'error', 'msg' => 'restaurant not found'];
        elseif (!$restaurantStatus['isNear'])
            return ['result' => 'error', 'msg' => 'restaurant is not around of You!'];
        $restaurantId = $restaurantStatus['id'];

        $cart = $this->createOrGetCart($user, $restaurantId);
        if ($restaurantId != $cart->restaurant_id)
            return ['result' => 'error', 'msg' => 'multiple restaurants'];

        $food = $this->getFood($foodUid, $restaurantId);
        if (is_null($food))
            return ['result' => 'error', 'msg' => 'food not found'];
        return ['result' => 'success', 'cart' => $cart, 'food' => $food];

    }

    public function addFood($restaurantUid, $foodUid): array
    {
        $data = $this->preliminaryReview($restaurantUid, $foodUid);
        if ($data['result'] == 'error')
            return $this->myResponse('error', [$data['msg']], []);

        return $this->addFoodToCart($data['cart'], $data['food']);
    }

    public function removeFood($restaurantUid, $foodUid): array
    {
        $data = $this->preliminaryReview($restaurantUid, $foodUid);
        if ($data['result'] == 'error')
            return $this->myResponse('error', [$data['msg']], []);
        return $this->removeFoodFromCart($data['cart'], $data['food']);
    }

    private function getStatusRestaurant($user, $restaurantUid): array
    {
        $restaurant = Restaurant::where('uid', $restaurantUid)->first();
        if (is_null($restaurant))
            return ['id' => -1, 'isNear' => null];
        if ($this->isNear($user->location->x, $user->location->y, $restaurant->location->x, $restaurant->location->y))
            return ['id' => $restaurant->id, 'isNear' => true];
        return ['id' => $restaurant->id, 'isNear' => false];

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

    private function addFoodToCart($cart, $food): array
    {
        $cartContents = $cart->cartContents;
        $cartId = $cart->id;
        $foodId = $food->id;
        if ($this->isFoodDiscounted($food))
            if (!$this->canUpdateDiscountedFoodData($food))
                return $this->myResponse('error', ['food is over'], []);
        if (!is_null($cartContents) && !is_null($cartContents->where('food_id', $foodId)->first())) {
            $cartContents->where('food_id', $foodId)->first()->increment('count');
        } else {
            CartContent::create([
                'cart_id' => $cartId,
                'food_id' => $foodId
            ]);
        }
        return $this->myResponse('success', [], []);
    }

    private function removeFoodFromCart($cart, $food): array
    {
        $cartContents = $cart->cartContents;
        $foodId = $food->id;
        $cartContent = $cartContents->where('food_id', $foodId)->first();
        if (is_null($cartContent))
            return $this->myResponse('error', ['there is no food in your card'], []);
        if ($cartContent->count > 1)
            $cartContent->decrement('count');
        else if ($cartContent->count == 1)
            $cartContent->delete();
        return $this->myResponse('success', [], []);
    }

    private function isFoodDiscounted($food): bool
    {
        return !is_null($food->df_id);
    }

    private function canUpdateDiscountedFoodData($food): bool
    {
        $discountedFood = $food->discountedFood;
        if ($discountedFood->count > 0)
            return true;
        else
            return false;
    }

    public function getCart(): array
    {
        $user = Auth::user();
        $cart = $user->cart;
        if (is_null($cart))
            return $this->myResponse('warning', ['cart is empty'], []);
        $cartContents = $cart->cartContents;
        if (is_null($cartContents))
            return $this->myResponse('warning', ['cart is empty'], []);
        $restaurant = Restaurant::find($cart->restaurant_id);
        $shoppingCart = new ShoppingCart($restaurant->name, $restaurant->uid);
        foreach ($cartContents as $cartContent) {
            $food = Food::find($cartContent->food_id);
            if (is_null($food->df_id)) {  //add Food
                $shoppingCart->addFood($food->name, $food->uid, $cartContent->count, $food->price);
            } else {  //add Discounted Food
                $discountedFood = DiscountedFood::find($food->df_id);
                $shoppingCart->addDiscountedFood($food->name, $food->uid, $cartContent->count, $discountedFood->new_price);
            }
        }
        return $this->myResponse('success', [], $shoppingCart->toJson());
    }

    public function submit(): array
    {
        $user = Auth::user();
        $cart = $user->cart;
        if (is_null($cart))
            return $this->myResponse('error', ['cart is empty'], []);
        $cartContents = $cart->cartContents;
        if (is_null($cartContents))
            return $this->myResponse('error', ['cart is empty'], []);
        $price = $this->calculateSumOfPriceOrders($cartContents);
        if ($user->credit < $price)
            return $this->myResponse('error', ['not enough money'], []);
        $restaurantLocation = Restaurant::find($cart->restaurant_id)->location;
        $deliveryController = new DeliveryController();
        $result = $deliveryController->findDelivery($user->location, $restaurantLocation);
        if ($result['isFound']) {
            $cart->is_payed = true;
            $cart->save();
            $user->credit -= $price;
            $user->save();
            return $this->myResponse('success', '', ['deliveryTime' => $result['deliveryTime']]);
        } else
            return $this->myResponse('error', ['delivery not found, try later.'], []);
    }

    private function calculateSumOfPriceOrders($cartContents): int
    {
        $sum = 0;
        foreach ($cartContents as $cartContent) {
            $food = Food::find($cartContent->food_id)->first();
            if (!is_null($food->df_id)) { //add Discounted Food Price
                $discountedFood = DiscountedFood::find($food->df_id);
                $discountedFood->decrement('count');
                $sum += $discountedFood->new_price;
            } else { //add Food Price
                $sum += $food->price;
            }
        }
        return $sum;
    }
}
