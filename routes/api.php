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

Route::prefix('v1')->group(function () {
    // Rutas específicas
    Route::post('/user/login', [UserController::class,'login']);
    Route::post('/user/signup', [UserController::class,'store']); //RUTA SINGUP DONDE SE EJECUTA EL METODO STORE
    Route::get('/user/getidentity', [UserController::class,'getIdentity'])->middleware(ApiAuthMiddleware::class);
    Route::put('/user/{email}', [UserController::class, 'update'])->middleware(ApiAuthMiddleware::class);

    Route::post('/licencia/upload', [LicenciaController::class,'uploadImage'])->middleware(ApiAuthMiddleware::class);
    Route::get('/licencia/getImage/{filename}', [LicenciaController::class,'getImage'])->middleware(ApiAuthMiddleware::class);
    Route::put('/licencia/{id}', [LicenciaController::class, 'update'])->middleware(ApiAuthMiddleware::class);

    Route::post('/vehiculo', [VehiculoController::class, 'store'])->middleware(ApiAuthMiddleware::class);
    Route::get('/vehiculo/getCars', [VehiculoController::class, 'index']);
    Route::post('/vehiculo/upload', [VehiculoController::class,'uploadImage'])->middleware(ApiAuthMiddleware::class);
    Route::get('/vehiculo/getImage/{filename}', [VehiculoController::class,'getImage']);
    Route::put('/vehiculo/{id}', [VehiculoController::class, 'update'])->middleware(ApiAuthMiddleware::class);
    Route::get('/vehiculo/{id}', [VehiculoController::class, 'show'])->middleware(ApiAuthMiddleware::class);
    Route::delete('/vehiculo/{id}', [VehiculoController::class, 'destroy'])->middleware(ApiAuthMiddleware::class);

    Route::post('/renta', [RentaController::class,'store'])->middleware(ApiAuthMiddleware::class);
    Route::put('/renta/{id}', [RentaController::class, 'update'])->middleware(ApiAuthMiddleware::class);
    Route::get('/renta', [RentaController::class, 'index'])->middleware(ApiAuthMiddleware::class);
    Route::get('/renta/{id}', [RentaController::class, 'show'])->middleware(ApiAuthMiddleware::class);

    Route::put('/tarjeta/{id}', [TarjetaController::class, 'update'])->middleware(ApiAuthMiddleware::class);

    Route::put('/cliente/{id}',  [ClienteController::class, 'update'])->middleware(ApiAuthMiddleware::class);

    // Rutas automáticas RESTful
    Route::resource('/cliente', ClienteController::class, ['except' => ['create', 'edit']])->middleware(ApiAuthMiddleware::class);
    Route::resource('/tarjeta', TarjetaController::class, ['except' => ['create', 'edit']])->middleware(ApiAuthMiddleware::class);
    Route::resource('/user', UserController::class, ['except' => ['create', 'edit']])->middleware(ApiAuthMiddleware::class);
    Route::resource('/licencia', LicenciaController::class, ['except' => ['create','edit']])->middleware(ApiAuthMiddleware::class);
});

