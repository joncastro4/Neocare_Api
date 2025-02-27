<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incubator extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'incubators';

    protected $fillable = [
        'state',
        'room_id'
    ];

    public function baby_incubator()
    {
        return $this->hasMany(BabyIncubator::class);
    }
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
