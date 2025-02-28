<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;


class MongoIncubator extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'incubators';

    protected $fillable = [
        'incubator_id',
        'room_id',
        'state',
        'sensors',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
