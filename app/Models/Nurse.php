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
}
