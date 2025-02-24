<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use function PHPUnit\Framework\returnArgument;

class Nurse extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'nurses';

    protected $fillable = [
        'user_person_id',
        'hospital_id',
        'rfc',
        'image_path',
    ];

    public function check()
    {
        return $this->hasMany(Check::class);
    }
    public function userPerson()
    {
        return $this->belongsTo(UserPerson::class, 'user_person_id');
    }
    public function babyIncubator()
    {
        return $this->hasMany(BabyIncubator::class);
    }
    public function hospital()
    {
        return $this->belongsTo(Hospital::class, 'hospital_id');
    }
}
