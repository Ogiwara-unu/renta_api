<?php

use App\Http\Controllers\ClienteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\TarjetaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RentaController;
use App\Http\Controllers\LicenciaController;
use App\Http\Middleware\ApiAuthMiddleware;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('v1')->group(
    function(){
        //RUTAS ESPECIFICAS 
        //POR SER DATOS SENSIBLES SE MANDA POR MEDIO DE POST
        Route::post('/user/login',[UserController::class,'login']);
    
        //RUTAS AUTOMATICAS restful
         //RUTA DE VEHICULO
        Route::resource('/vehiculo',VehiculoController::class,['except'=>['create','edit']]); //SE EXCLUYEN CREATE Y EDIT POR OBSOLETOS JIJIJ y POR MOTIVOS DE SEGURIDAD

         //RUTA DE CLIENTE
         Route::resource('/cliente', ClienteController::class, ['except' => ['create', 'edit']]);

        //RUTA DE TARJETA
        Route::resource('/tarjeta', TarjetaController::class, ['except' => ['create', 'edit']]);

        //RUTA DE USERS
        Route::resource('/users', UserController::class, ['except' => ['create', 'edit']]);//->middleware(ApiAuthMiddleware::class);

        //RUTA DE RENTA
        Route::resource('/renta', RentaController::class, ['except' => ['create', 'edit']]);
         //RUTA DE LICENCIA
        Route::resource('/licencia', LicenciaController::class, ['except' => ['create','edit']]);
    }
    );
