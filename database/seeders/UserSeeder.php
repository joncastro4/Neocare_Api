<?php

namespace Database\Seeders;

use Hash;
use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Person;
use App\Models\UserPerson;
use App\Models\Hospital;
use App\Models\Nurse;
use App\Models\Address;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Super admin
        $user = User::create([
            'name' => 'superAdmin',
            'email' => 'neocare@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin'),
            'role' => 'super-admin',
        ]);

        $person = Person::create([
            'name' => 'Admin',
            'last_name_1' => 'Admin',
            'last_name_2' => 'Admin',
        ]);

        UserPerson::create([
            'user_id' => $user->id,
            'person_id' => $person->id,
        ]);

        // Nurse Admin
        $user = User::create([
            'name' => 'nurseAdmin',
            'email' => 'imms@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin'),
            'role' => 'nurse-admin',
        ]);

        $personNurse = Person::create([
            'name' => 'nurseAdmin',
            'last_name_1' => 'nurseAdmin',
            'last_name_2' => 'nurseAdmin',
        ]);

        UserPerson::create([
            'user_id' => $user->id,
            'person_id' => $personNurse->id,
        ]);

        $address = Address::create([
            'number' => '123',
            'street' => 'Street 1',
            'neighborhood' => 'Neighborhood 1',
            'city' => 'City 1',
            'state' => 'State 1',
            'zip_code' => '12345',
        ]);

        $hospital = Hospital::create([
            'name' => 'Hospital 1',
            'phone_number' => '123456789',
            'address_id' => $address->id,
        ]);

        $nurse = Nurse::create([
            'user_person_id' => $personNurse->id,
            'hospital_id' => $hospital->id,
        ]);
    }
}
