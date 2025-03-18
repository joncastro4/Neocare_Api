<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
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

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }
}
