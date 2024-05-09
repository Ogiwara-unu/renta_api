<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarjeta extends Model
{
    use HasFactory;
    protected $table='tarjeta';
    protected $fillable=['numero_tarjeta','titular','fecha_vencimiento','cvv','id'];

    public function renta(){ 
        return $this->hasMany('App\Models\Renta');
    }

}
