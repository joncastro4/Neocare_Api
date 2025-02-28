<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoHospital extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'hospitals';

    protected $fillable = [
        'address_id',
        'name',
        'phone_number',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
