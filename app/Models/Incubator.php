<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incubator extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'state'
    ];

    public function baby_incubator()
    {
        return $this->hasMany(Baby_Incubator::class);
    }
}
