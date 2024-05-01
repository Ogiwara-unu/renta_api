<?php

use App\Http\Controllers\ClienteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\TarjetaController;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(
    function(){
        //RUTAS ESPECIFICAS 
    
        //RUTAS AUTOMATICAS restful
         //RUTA DE VEHICULO
        Route::resource('/vehiculo',VehiculoController::class,['except'=>['create','edit']]); //SE EXCLUYEN CREATE Y EDIT POR OBSOLETOS JIJIJ y POR MOTIVOS DE SEGURIDAD

         //RUTA DE CLIENTE
         Route::resource('/cliente', ClienteController::class, ['except' => ['create', 'edit']]);

        //RUTA DE TARJETA
        Route::resource('/tarjeta', TarjetaController::class, ['except' => ['create', 'edit']]);

        //RUTA DE USERS
        Route::resource('/users', UserController::class, ['except' => ['create', 'edit']]);
    }
    );
