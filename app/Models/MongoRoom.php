<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class MongoRoom extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'rooms';

    protected $fillable = [
        'name',
        'number',
        'hospital_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
