<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nurse extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'nurses';

    protected $fillable = [
        'rfc',
        'image_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

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
        return $this->hasMany(NurseBaby::class);
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
