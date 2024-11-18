<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nurse_id',
        'message',
    ];

    public function nurse()
    {
        return $this->belongsTo(Nurse::class);
    }
    public function baby()
    {
        return $this->belongsTo(Baby::class);
    }
}
