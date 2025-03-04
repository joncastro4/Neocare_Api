<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sensor;
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
    public function sensors()
    {
        return $this->hasMany(Sensor::class);
    }

    static function booted()
    {
        static::created(function ($incubator) {
            $incubator->sensors()->create([
                'type' => 'temperature',
                'unit' => 'C',
            ]);
            $incubator->sensors()->create([
                'type' => 'humidity',
                'unit' => '%',
            ]);
            $incubator->sensors()->create([
                'type' => 'light',
                'unit' => 'lumen',
            ]);
            $incubator->sensors()->create([
                'type' => 'motion',
                'unit' => 'm/s',
            ]);
            $incubator->sensors()->create([
                'type' => 'vibration',
                'unit' => 'm/s',
            ]);
            $incubator->sensors()->create([
                'type' => 'sound',
                'unit' => 'dB',
            ]);
        });
    }
}
