<?php

namespace Database\Seeders;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FoodPartySeeder extends Seeder
{
    private CommonSeeder $wrench;

    public function __construct()
    {
        $this->wrench = new CommonSeeder();
    }

    public function run()
    {
        try {
            $jsonFile = Storage::disk('local')->get('files\foodParty.json');
            $partyFoods = json_decode($jsonFile, true);
            foreach ($partyFoods as $partyFood) {
                $restaurantId = $this->getRestaurantId($partyFood);
                foreach ($partyFood['menu'] as $food) {
                    $foodId = $this->getFoodId($food, $restaurantId);
                    $this->createPartyFood($food, $foodId);
                }
            }
        } catch (FileNotFoundException $e) {
            log::error('file does not exists - FoodPartySeeder');
        }
    }

    private function getRestaurantId($partyFood): int
    {
        if (DB::table('restaurants')->where('name', $partyFood['name'])->exists()) {
            $restaurantId = DB::table('restaurants')->where('name', $partyFood['name'])->first('id');
            $restaurantId = json_decode(json_encode($restaurantId), true)['id'];
        } else {
            $locationId = $this->wrench->getLocationId($partyFood['location']['x'], $partyFood['location']['y']);
            $restaurantId = DB::table('restaurants')->insertGetId([
                'name', $partyFood['name'],
                'logo' => $partyFood ['logo'],
                'location_id' => $locationId
            ]);
        }
        return $restaurantId;
    }

    private function getFoodId($food, $restaurantId): int
    {
        if (DB::table('foods')->where('restaurant_id', $restaurantId)->where('name', $food['name'])->exists()) {
            $foodId = DB::table('foods')->where('restaurant_id', $restaurantId)
                ->where('name', $food['name'])->first('id');
            $foodId = json_decode(json_encode($foodId), true)['id'];
        } else {
            $foodId = DB::table('foods')->insertGetId([
                'name' => $food['name'],
                'description' => $food['description'],
                'price' => $food['oldPrice'],
                'image' => $food['image'],
                'popularity' => $food['popularity'],
                'uid' => $this->wrench->creatUniqueIdForFood(),
                'restaurant_id' => $restaurantId,
                'df_id' => null,
            ]);
        }
        return $foodId;
    }

    private function createPartyFood($food, $foodId)
    {
        $partyFoodId = DB::table('foods')->where('id', $foodId)->first('df_id');
        $partyFoodId = json_decode(json_encode($partyFoodId), true)['df_id'];
        if ($partyFoodId == null) {
            $partyFoodId = DB::table('discounted_foods')->insertGetId([
                'new_price' => $food['price'],
                'count' => $food['count']
            ]);
            DB::table('foods')->where('id', $foodId)->update([
                'df_id' => $partyFoodId,
            ]);
        } else
            DB::table('discounted_foods')->where('id', $partyFoodId)->update([
                'new_price' => $food['price'],
                'count' => $food['count']
            ]);

    }
}
