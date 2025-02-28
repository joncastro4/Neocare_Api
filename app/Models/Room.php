<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MongoRoom;

class Room extends Model
{
    use HasFactory;
    protected $table = 'rooms';
    protected $fillable = [
        'hospital_id',
        'name',
        'number'
    ];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class, 'hospital_id');
    }
    public static function booted()
    {
        static::created(function ($room) {
            MongoRoom::create([
                'hospital_id' => $room->hospital_id,
                'name' => $room->name,
                'number' => $room->number,
                'created_at' => $room->created_at,
                'updated_at' => $room->updated_at,
                'deleted_at' => $room->deleted_at
            ]);
        });
    }
}
