<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;
    protected $primaryKey = 'placa';
    protected $table='vehiculo';
    protected $fillable=['placa','marca','modelo','transmision','precio','kilometraje','anio','estado','img'];

    public function renta(){ 
        return $this->hasMany('App\Models\Renta');
    }

}
