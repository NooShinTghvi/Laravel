<?php

namespace Database\Seeders;

use App\Models\ContactGroup;
use Illuminate\Database\Seeder;

class ContactGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ContactGroup::factory(35)->create();
    }
}
