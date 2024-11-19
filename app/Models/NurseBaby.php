<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NurseBaby extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'nurses_babies';

    protected $fillable = [
        'nurse_id',
        'baby_id'
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
