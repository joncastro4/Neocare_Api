<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    use HasFactory;

    protected $fillable = [
        'address_id',
        'name',
        'phone_number'
    ];

    public function addresses() {
        return $this->belongsTo(Address::class);
    }
}
