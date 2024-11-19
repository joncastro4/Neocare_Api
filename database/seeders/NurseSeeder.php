<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

use App\Models\Nurse;

class NurseSeeder extends Seeder
{
    public function run()
    {
        Nurse::factory(10)->create();
    }
}
