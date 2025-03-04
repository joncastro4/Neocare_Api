<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Data;
use Illuminate\Support\Facades\Log;

class Sensor extends Model
{
    use HasFactory;

    protected $table = 'sensors';

    protected $fillable = [
        'incubator_id',
        'type',
        'unit',
    ];

    public function incubator()
    {
        return $this->belongsTo(Incubator::class, 'incubator_id');
    }

    static function booted()
    {
        static::created(function ($sensor) {
            try {
                Data::create([
                    'sensor' => $sensor->toArray(),
                    'value' => 0,
                    'reading_date' => null,
                ]);
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        });
    }
}
