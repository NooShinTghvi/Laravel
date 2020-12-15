<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommonSeeder extends Seeder
{
    public function getLocationId($x, $y): int
    {
        if (DB::table('locations')->where('x', $x)->where('y', $y)->exists()) {
            $locationId = DB::table('locations')->where('x', $x)->where('y', $y)->first('id');
            $locationId = json_decode(json_encode($locationId), true)['id'];
        } else
            $locationId = DB::table('locations')->insertGetId(['x' => $x, 'y' => $y]);
        return $locationId;
    }

    public function creatUniqueIdForFood()
    {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz012345678901234567890123456789';
        $uid = substr(str_shuffle($permitted_chars), 0, 24);
        while (DB::table('foods')->where('uid', $uid)->exists()) {
            $uid = substr(str_shuffle($permitted_chars), 0, 24);
        }
        return $uid;
    }
}
