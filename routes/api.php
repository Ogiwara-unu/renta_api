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
        Route::get('/user/getidentity',[UserController::class,'getIdentity'])->middleware(ApiAuthMiddleware::class);
        Route::put('/user/{email}', [UserController::class, 'update']);

        Route::post('/licencia/upload',[LicenciaController::class,'uploadImage'])->middleware(ApiAuthMiddleware::class);
        Route::get('/licencia/getImage/{filename}',[LicenciaController::class,'getImage'])->middleware(ApiAuthMiddleware::class);
        Route::put('/licencia/{id}', [LicenciaController::class, 'update']);

        Route::post('/vehiculo',[VehiculoController::class, 'store'])->middleware(ApiAuthMiddleware::class);
        Route::post('/vehiculo/upload',[VehiculoController::class,'uploadImage'])->middleware(ApiAuthMiddleware::class);
        Route::get('/vehiculo/getImage/{filename}',[VehiculoController::class,'getImage'])->middleware(ApiAuthMiddleware::class);
        Route::put('/vehiculo/{id}', [VehiculoController::class, 'update'])->middleware(ApiAuthMiddleware::class);
        Route::get('/vehiculo/{id}', [VehiculoController::class, 'show']);
        Route::delete('/vehiculo/{id}', [VehiculoController::class, 'destroy'])->middleware(ApiAuthMiddleware::class);
      

        //CUANDO SE AGREGA UN MIDDLEWARE PARA ESTE TIPO DE RUTAS HAY QUE TENER "CUIDADO"
        //PORQUE SI EXISTE UNA RUTA RESTFUL, ESTA NO VA A PERMITIR QUE SE EJECUTE BIEN LA RUTA CON MIDDLEWARE
        Route::post('/renta',[RentaController::class,'store'])->middleware(ApiAuthMiddleware::class);
        Route::put('/renta/{id}', [VehiculoController::class, 'update']);

        Route::put('/tarjeta/{id}', [VehiculoController::class, 'update']);

        Route::put('/clientes/{id}', 'ClienteController@update');
    
        //RUTAS AUTOMATICAS restful
         //RUTA DE VEHICULO
        // Route::resource('/vehiculo',VehiculoController::class,['except'=>['create','edit']]); //SE EXCLUYEN CREATE Y EDIT POR OBSOLETOS JIJIJ y POR MOTIVOS DE SEGURIDAD

         //RUTA DE CLIENTE
         Route::resource('/cliente', ClienteController::class, ['except' => ['create', 'edit']]);

        //RUTA DE TARJETA
        Route::resource('/tarjeta', TarjetaController::class, ['except' => ['create', 'edit']]);

        //RUTA DE USERS
        Route::resource('/users', UserController::class, ['except' => ['create', 'edit']]);//->middleware(ApiAuthMiddleware::class);

        //RUTA DE RENTA
        // Route::resource('/renta', RentaController::class, ['except' => ['create', 'edit']]);
        
         //RUTA DE LICENCIA
        Route::resource('/licencia', LicenciaController::class, ['except' => ['create','edit']]);
    }
    );
