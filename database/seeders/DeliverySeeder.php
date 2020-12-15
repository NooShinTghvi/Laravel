<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeliverySeeder extends Seeder
{
    private $wrench;
    public function __construct()
    {
        $this->wrench = new CommonSeeder();
    }

    public function run()
    {
        if (!Storage::disk('local')->exists('files\deliveries.json')) {
            return;
        }
        $jsonFile = Storage::disk('local')->get('files\deliveries.json');
        $deliveries = json_decode($jsonFile, true);
        foreach ($deliveries as $delivery) {
            if (DB::table('deliveries')->where('unique_id', $delivery['id'])->exists()) {
                $locationId = $this->wrench->getLocationId($delivery['location']['x'], $delivery['location']['y']);
                DB::table('deliveries')->where('unique_id', $delivery['id'])->update([
                    'velocity' => $delivery['velocity'],
                    'location_id' => $locationId,
                ]);
            } else {
                $locationId = $this->wrench->getLocationId($delivery['location']['x'], $delivery['location']['y']);
                DB::table('deliveries')->insert([
                    'unique_id' => $delivery['id'],
                    'velocity' => $delivery['velocity'],
                    'location_id' => $locationId,
                ]);
            }
        }
    }
}
