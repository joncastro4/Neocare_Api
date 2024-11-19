<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

use App\Models\Schedule;

class ScheduleSeeder extends Seeder
{
    public function run()
    {
        Schedule::factory(10)->create();
    }
}
