<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function findDelivery($userLocation, $restaurantLocation): array
    {
        $result=array();
        $deliveries = Delivery::where('is_busy', false)->get();
        if (sizeof($deliveries) == 0) {
            $result['found'] = false;
            return $result;
        }
        $bestDelivery = null;
        $minTime = pow(10, 10);
        foreach ($deliveries as $delivery) {
            $deliveryLocation = $delivery->location;
            $time = (
                    sqrt(pow($restaurantLocation->x - $deliveryLocation->x, 2) +
                        pow($restaurantLocation->y - $deliveryLocation->y, 2))
                    +
                    sqrt(pow($userLocation->x - $deliveryLocation->x, 2) +
                        pow($userLocation->y - $deliveryLocation->y, 2))
                ) / $delivery->velocity;
            if ($time < $minTime){
                $bestDelivery = $delivery;
                $minTime = $time;
            }
        }
        $bestDelivery->is_busy = true;
        $bestDelivery->save();
        $current = Carbon::now();  // date + time
        $current = $current->addMinutes($minTime);
        $result['found'] = true;
        $result['time'] = $current->toDateTimeLocalString();
        return  $result;
    }
}
