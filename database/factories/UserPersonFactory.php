<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UserPerson;
use App\Models\Person;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserPerson>
 */
class UserPersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = UserPerson::class;
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'person_id' => Person::factory(),
        ];
    }
}
