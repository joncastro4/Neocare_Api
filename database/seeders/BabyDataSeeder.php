<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

use App\Models\BabyData;

class BabyDataSeeder extends Seeder
{
    public function run()
    {
        BabyData::factory()->count(10)->create();
    }
}
