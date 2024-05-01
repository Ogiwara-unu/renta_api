<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//EN ESTA ARQUITECTURA SE SE JALA LO QUE ESTA EN LA BD AL MODELO

class Licencia extends Model
{
    use HasFactory;
    protected $table='licencia';  //NOMBRE DE LA TABLA
    protected $fillable=['id_licencia','cliente_id','fecha_vencimiento','tipo','img']; //ELEMENTOS QUE SERAN MODIFICABLES EN LA BD 
 
    public function cliente(){
        return $this->belongsTo('App\Models\Cliente','cliente_id');
    }
}
