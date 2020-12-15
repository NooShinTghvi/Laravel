<?php

namespace App\Http\Controllers\Models;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShoppingCart extends Controller
{
    private $dataJson = array();

    public function __construct($restaurantName)
    {
        $this->dataJson['restaurantName'] = $restaurantName;
        $this->dataJson['foods'] = array();
        $this->dataJson['DiscountedFoods'] = array();
        $this->dataJson['price'] = 0;
    }

    public function addFood($name, $count, $price)
    {
        array_push($this->dataJson['foods'], array(
            'name' => $name,
            'count' => $count,
            'price' => $price));
        $this->dataJson['price'] += ($count * $price);
    }

    public function addDiscountedFood($name, $count, $price)
    {
        array_push($this->dataJson['DiscountedFoods'], array(
            'name' => $name,
            'count' => $count,
            'price' => $price));
        $this->dataJson['price'] += ($count * $price);
    }

    public function toJson(): array
    {
        return $this->dataJson;
    }
}
