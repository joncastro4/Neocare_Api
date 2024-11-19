<?php

namespace Database\Seeders;

use App\Models\Relative;
use Illuminate\Database\Seeder;

class RelativeSeeder extends Seeder
{
    public function run()
    {
        Relative::factory(10)->create();
    }
}
