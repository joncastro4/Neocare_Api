<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    use HasFactory;

     protected $table = 'sensores';

     protected $fillable = [
         'tipo_sensor',        
         'nombre_amigable',     
         'unidad',             
     ];
 
     protected $hidden = [
         'created_at',
         'updated_at',
     ];
}
