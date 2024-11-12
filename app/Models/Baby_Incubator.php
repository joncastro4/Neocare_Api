<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Baby_Incubator extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'baby_id', 
        'incubator_id'
    ];

    public function baby()
    {
        return $this->belongsTo(Baby::class);
    }

    public function incubator()
    {
        return $this->belongsTo(Incubator::class);
    }

    public function baby_data()
    {
        return $this->hasMany(Baby_Data::class);
    }
}
