<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Carbon\Carbon;

class DeliveryController extends Controller
{
    public function findDelivery($userLocation, $restaurantLocation): array
    {
        $deliveries = Delivery::where('is_busy', false)->get();
        if (sizeof($deliveries) == 0)
            return ['isFound' => false];
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
            if ($time < $minTime) {
                $bestDelivery = $delivery;
                $minTime = $time;
            }
        }
        $deliveryTime = Carbon::now()->addMinutes($minTime);  // date + time   +  min
        $bestDelivery->is_busy = true;
        $bestDelivery->delivery_time = $deliveryTime;
        $bestDelivery->save();
        return [
            'isFound' => true,
            'deliveryTime' => $deliveryTime->toDateTimeLocalString()
        ];
    }
}
