<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Data extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'data';
    protected $fillable = [
        'sensor',
        'value',
        'reading_date',
    ];
    
    protected $casts = [
        'sensor' => 'array',
    ];
}
