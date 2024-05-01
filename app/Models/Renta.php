<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Renta extends Model
{
    use HasFactory;
    protected $table='renta';
    protected $fillable=['id','user_id','cliente_id','vehiculo_placa','tarjeta_id','tarifa_base','fecha_entrega','fecha_devolucion','total'];

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function cliente()
    {
        return $this->belongsTo('App\Models\Cliente','cliente_id');
    }

    public function vehiculo()
    {
        return $this->belongsTo('App\Models\Vehiculo','placa_vehiculo');
    }

    public function tarjeta()
    {
        return $this->belongsTo('App\Models\Tarjeta','tarjeta_id'); //TARJETA ID ES EL CAMPO EN LA BD QUE SE LLAMABA cuatro_digitos EN LA TABLA RENTA
    }
}
