<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPerson extends Model
{
    use HasFactory;

    protected $table = 'user_person';

    protected $fillable = [
        'user_id',
        'person_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }
    public function nurse()
    {
        return $this->hasOne(Nurse::class, 'user_person_id');
    }
}
