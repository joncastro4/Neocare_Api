<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class MongoSensor extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'sensors';

    protected $fillable = [
        'type',
        'unit',
        'current_value',
        'min_value',
        'max_value',
        'reading_date',
        'created_at',
        'updated_at'
    ];
}
