<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Check extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nurse_id', 
        'baby_incubator_id', 
        'description'
    ];

    public function baby_incubator()
    {
        return $this->belongsTo(Baby_Incubator::class);
    }

    public function nurse()
    {
        return $this->belongsTo(Nurse::class);
    }
}
