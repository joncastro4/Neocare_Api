<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Check extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'checks';

    protected $fillable = [
        'nurse_id',
        'baby_incubator_id',
        'title',
        'description'
    ];

    public function baby_incubator()
    {
        return $this->belongsTo(BabyIncubator::class);
    }

    public function nurse()
    {
        return $this->belongsTo(Nurse::class);
    }
}
