<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nurse_id', 
        'day', 
        'start_time', 
        'end_time'
    ];

    public function nurse()
    {
        return $this->belongsTo(Nurse::class);
    }
}
