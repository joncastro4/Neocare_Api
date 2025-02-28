<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\MongoIncubator;

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
    public static function booted()
    {
        static::created(function ($incubator) {
            MongoIncubator::create([
                'incubator_id' => $incubator->id,
                'room_id' => $incubator->room_id,
                'state' => $incubator->state,
                'sensors' => [
                    [
                        'sensor_id' => '1',
                        'type' => 'temperatura',
                        'unit' => '°C',
                        'current_value' => 0.0,
                        'reading_date' => null
                    ],
                    [
                        'sensor_id' => '2',
                        'type' => 'humedad',
                        'unit' => '%',
                        'current_value' => 60.0,
                        'reading_date' => null
                    ],
                    [
                        'sensor_id' => '1',
                        'type' => 'vibration',
                        'unit' => '°C',
                        'current_value' => 0.0,
                        'reading_date' => null
                    ],
                    [
                        'sensor_id' => '2',
                        'type' => 'light',
                        'unit' => '%',
                        'current_value' => 0.0,
                        'reading_date' => null
                    ]
                ],
                'created_at' => $incubator->created_at,
                'updated_at' => $incubator->updated_at,
                'deleted_at' => $incubator->deleted_at
            ]);
        });
    }
}
