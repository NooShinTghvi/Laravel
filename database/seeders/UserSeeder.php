<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insertGetId([
            'first_name' => 'Admin',
            'last_name' => 'Adminiii',
            'email' => 'admin@gmail.com',
            'is_admin' => true,
            'password' => '$2y$10$I.Oyi9xXNfb.7zciPCSfV.6YCi9Ozpbmqj8Kj2Bx0nceoKN/x4J/u', //12345678
        ]);
        User::factory(8)->create();
    }
}
