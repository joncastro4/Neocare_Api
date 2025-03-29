<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Data extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'prueba';


    protected $fillable = [
        'incubator_id',
    ];
}

