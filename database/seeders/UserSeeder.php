<?php

namespace Database\Seeders;

use Hash;
use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Person;
use App\Models\Nurse;

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
            'email' => 'neocarea@gmail.com',
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

        User::factory(10)->create();
    }
}
