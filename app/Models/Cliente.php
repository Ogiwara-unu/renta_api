<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    protected $table='cliente';
    protected $fillable=['id','nombre','primer_apellido','segundo_apellido','telefono','email','direccion','fecha_nacimiento'];

    public function licencia(){ //RELACION 1 A 1
        return $this->hasMany('App\Models\Licencia');
    }
}


