<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

use App\Models\BabyIncubator;

class BabyIncubatorSeeder extends Seeder
{
    public function run()
    {
        BabyIncubator::factory(10)->create();
    }
}
