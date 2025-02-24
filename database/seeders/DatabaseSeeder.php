<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Database\Seeders\SensoresTableSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\PersonSeeder;
use Database\Seeders\BabySeeder;
use Database\Seeders\RelativeSeeder;
use Database\Seeders\NurseSeeder;
use Database\Seeders\NurseBabySeeder;
use Database\Seeders\IncubatorSeeder;
use Database\Seeders\BabyIncubatorSeeder;
use Database\Seeders\CheckSeeder;
use Database\Seeders\BabyDataSeeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            CheckSeeder::class
        ]);
    }
}
