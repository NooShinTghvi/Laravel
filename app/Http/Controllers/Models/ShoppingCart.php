<?php

namespace App\Http\Controllers\Models;

use App\Http\Controllers\Controller;

class ShoppingCart extends Controller
{
    private array $dataJson;

    public function __construct($restaurantName, $uid)
    {
        $this->dataJson['restaurantName'] = $restaurantName;
        $this->dataJson['uid'] = $uid;
        $this->dataJson['foods'] = [];
        $this->dataJson['DiscountedFoods'] = [];
        $this->dataJson['price'] = 0;
    }

    public function addFood($name, $uid, $count, $price)
    {
        array_push($this->dataJson['foods'], [
            'name' => $name,
            'uid' => $uid,
            'count' => $count,
            'price' => $price
        ]);
        $this->dataJson['price'] += ($count * $price);
    }

    public
    function addDiscountedFood($name, $uid, $count, $price)
    {
        array_push($this->dataJson['DiscountedFoods'], [
            'name' => $name,
            'uid' => $uid,
            'count' => $count,
            'price' => $price
        ]);
        $this->dataJson['price'] += ($count * $price);
    }

    public
    function toJson(): array
    {
        return $this->dataJson;
    }
}
