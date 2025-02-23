<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'people';

    protected $fillable = [
        'name',
        'last_name_1',
        'last_name_2'
    ];

    public function baby()
    {
        return $this->hasMany(Baby::class);
    }

    public function relative()
    {
        return $this->hasMany(Relative::class);
    }
    public function userPerson()
    {
        return $this->hasMany(UserPerson::class, 'person_id');
    }
    public function users()
    {
        return $this->hasManyThrough(User::class, UserPerson::class, 'person_id', 'id', 'id', 'user_id');
    }
    public function nurse()
    {
        return $this->hasOneThrough(Nurse::class, UserPerson::class, 'person_id', 'user_person_id', 'id', 'id');
    }
}
