<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

use App\Models\Check;

class CheckSeeder extends Seeder
{
    public function run()
    {
        Check::factory()->count(10)->create();
    }
}
