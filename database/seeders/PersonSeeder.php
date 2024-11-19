<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

use App\Models\Person;

class PersonSeeder extends Seeder
{
    public function run()
    {
        Person::factory(100)->create();
    }
}
