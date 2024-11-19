<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

use App\Models\NurseBaby;

class NurseBabySeeder extends Seeder
{
    public function run()
    {
        NurseBaby::factory(10)->create();
    }
}
