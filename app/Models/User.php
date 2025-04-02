<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function userPerson()
    {
        return $this->hasMany(UserPerson::class, 'user_id');
    }
    public function people()
    {
        return $this->hasManyThrough(Person::class, UserPerson::class, 'user_id', 'id', 'id', 'person_id');
    }
    public function nurse()
    { 
        return $this->hasOneThrough(Nurse::class, UserPerson::class, 'user_id', 'user_person_id', 'id', 'id');
    }

    public function nurseHospital()
    {
        return $this->hasOne(Nurse::class, 'user_person_id', 'userPerson.id')
            ->through('userPerson');
    }
}
