<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MongoHospital;
class Hospital extends Model
{
    use HasFactory;

    protected $fillable = [
        'address_id',
        'name',
        'phone_number'
    ];

    public function addresses()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
    public function babies()
    {
        return $this->hasMany(Baby::class);
    }

    protected static function booted()
    {
        static::created(function ($hospital) {
            MongoHospital::create([
                'address_id' => $hospital->address_id,
                'name' => $hospital->name,
                'phone_number' => $hospital->phone_number,
                'created_at' => $hospital->created_at,
                'updated_at' => $hospital->updated_at,
                'deleted_at' => $hospital->deleted_at
            ]);
        });
    }
}
