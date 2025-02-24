<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Baby extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'babies';

    protected $fillable = [
        'person_id',
        'date_of_birth',
        'ingress_date',
        'egress_date'
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function baby_incubator()
    {
        return $this->hasMany(BabyIncubator::class);
    }

    public function relative()
    {
        return $this->hasMany(Relative::class);
    }
}
