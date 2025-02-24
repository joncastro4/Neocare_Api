<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BabyIncubator extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'babies_incubators';

    protected $fillable = [
        'baby_id',
        'incubator_id',
        'nurse_id',
    ];

    public function baby()
    {
        return $this->belongsTo(Baby::class, 'baby_id');
    }

    public function incubator()
    {
        return $this->belongsTo(Incubator::class, 'incubator_id');
    }

    public function baby_data()
    {
        return $this->hasMany(BabyData::class);
    }

    public function nurse()
    {
        return $this->belongsTo(Nurse::class, 'nurse_id');
    }
}
