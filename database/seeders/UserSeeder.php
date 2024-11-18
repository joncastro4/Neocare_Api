<?php

namespace Database\Seeders;

use Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Nurse;
use App\Models\Person;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@neocare.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin'),
            'role' => 'admin',
        ]);

        $person = Person::create([
            'name' => 'Admin',
            'last_name_1' => 'Admin',
            'last_name_2' => 'Admin',
        ]);

        Nurse::create([
            'user_id' => $user->id,
            'person_id' => $person->id,
        ]);
    }
}
