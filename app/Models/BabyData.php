<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BabyData extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'baby_datas';
    
    protected $fillable = [
        'baby_incubator_id',
        'oxygen',
        'heart_rate',
        'temperature',
    ];

    public function baby_incubator()
    {
        return $this->belongsTo(BabyIncubator::class);
    }
}
