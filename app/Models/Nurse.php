<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nurse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'person_id', 
        'rfc'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function nurses_babies()
    {
        return $this->hasMany(Nurse_Baby::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function check()
    {
        return $this->hasMany(Check::class);
    }
}
