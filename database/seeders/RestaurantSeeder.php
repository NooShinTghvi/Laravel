<?php

namespace Database\Seeders;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RestaurantSeeder extends Seeder
{
    private CommonSeeder $wrench;

    public function __construct()
    {
        $this->wrench = new CommonSeeder();
    }

    public function run()
    {
        try {
            $jsonFile = Storage::disk('local')->get('files\restaurants.json');
            $restaurants = json_decode($jsonFile, true);
            foreach ($restaurants as $restaurant) {
                if (DB::table('restaurants')->where('name', $restaurant['name'])->exists()) {
                    continue;
                }
                $locationId = $this->wrench->getLocationId($restaurant['location']['x'], $restaurant['location']['y']);
                $restaurantId = DB::table('restaurants')->insertGetId([
                    'name' => $restaurant['name'],
                    'logo' => $restaurant ['logo'],
                    'uid' => $restaurant['id'],
                    'location_id' => $locationId
                ]);
                $this->createFood($restaurantId, $restaurant['menu']);
            }
        } catch (FileNotFoundException $e) {
            log::error('file does not exists - RestaurantSeeder');
        }
    }


    private function createFood($restaurantId, $menu)
    {
        foreach ($menu as $food) {
            if (DB::table('foods')->where('restaurant_id', $restaurantId)->where('name', $food['name'])->exists())
                continue;
            DB::table('foods')->insert([
                'name' => $food['name'],
                'description' => $food['description'],
                'price' => $food['price'],
                'image' => $food['image'],
                'popularity' => $food['popularity'],
                'uid' => $this->wrench->creatUniqueIdForFood(),
                'restaurant_id' => $restaurantId,
                'df_id' => null,
            ]);
        }
    }
}
