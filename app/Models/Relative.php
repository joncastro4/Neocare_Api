<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Relative extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'relatives';

    protected $fillable = [
        'person_id',
        'baby_id',
        'phone_number',
        'email',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function baby()
    {
        return $this->belongsTo(Baby::class);
    }
}
