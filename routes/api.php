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
use App\Models\Licencia;
use App\Models\Tarjeta;

Route::prefix('v1')->group(function () {
    // Rutas específicas
    
    /* RUTAS USER */
    Route::post('/user/login', [UserController::class,'login']);
    Route::post('/user/signup', [UserController::class,'store']); //RUTA SINGUP DONDE SE EJECUTA EL METODO STORE
    Route::post('/user/add', [UserController::class,'store'])->middleware(ApiAuthMiddleware::class);
    Route::get('/user/getUsers', [UserController::class, 'index'])->middleware(ApiAuthMiddleware::class);
    Route::get('/user/getUser/{email}', [UserController::class, 'show'])->middleware(ApiAuthMiddleware::class);
    Route::get('/user/getidentity', [UserController::class,'getIdentity'])->middleware(ApiAuthMiddleware::class);
    Route::put('/user/updateUser/{email}', [UserController::class, 'update'])->middleware(ApiAuthMiddleware::class);
    Route::delete('/user/destroyUser/{email}', [UserController::class, 'destroy'])->middleware(ApiAuthMiddleware::class);

    /* RUTAS LICENCIA */
    Route::post('/licencia/upload', [LicenciaController::class,'uploadImage'])->middleware(ApiAuthMiddleware::class);
    Route::get('/licencia/getImage/{filename}', [LicenciaController::class,'getImage'])->middleware(ApiAuthMiddleware::class);
    Route::post('/licencia/add', [LicenciaController::class,'store'])->middleware(ApiAuthMiddleware::class);
    Route::get('/licencia/getLicenses', [LicenciaController::class, 'index'])->middleware(ApiAuthMiddleware::class);
    Route::get('/licencia/getLicense/{id}', [LicenciaController::class, 'show'])->middleware(ApiAuthMiddleware::class);
    Route::put('/licencia/updateLicense/{id}', [LicenciaController::class, 'update'])->middleware(ApiAuthMiddleware::class);
    Route::delete('/licencia/destroyLicense/{id}', [LicenciaController::class, 'destroy'])->middleware(ApiAuthMiddleware::class);

    /* RUTAS VEHICULO */
    Route::post('/vehiculo/add', [VehiculoController::class, 'store'])->middleware(ApiAuthMiddleware::class);
    Route::get('/vehiculo/getCars', [VehiculoController::class, 'index']);
    Route::get('/vehiculo/getCar/{id}', [VehiculoController::class, 'show'])->middleware(ApiAuthMiddleware::class);
    Route::put('/vehiculo/updateCar/{id}', [VehiculoController::class, 'update'])->middleware(ApiAuthMiddleware::class);
    Route::delete('/vehiculo/destroyCar/{id}', [VehiculoController::class, 'destroy'])->middleware(ApiAuthMiddleware::class);
    Route::post('/vehiculo/upload', [VehiculoController::class,'uploadImage'])->middleware(ApiAuthMiddleware::class);
    Route::get('/vehiculo/getImage/{filename}', [VehiculoController::class,'getImage']);

    /* RUTAS renta */
    Route::post('/renta/add', [RentaController::class,'store'])->middleware(ApiAuthMiddleware::class);
    Route::get('/renta/getRents', [RentaController::class, 'index'])->middleware(ApiAuthMiddleware::class);
    Route::get('/renta/getRent/{id}', [RentaController::class, 'show'])->middleware(ApiAuthMiddleware::class);
    Route::put('/renta/updateRent/{id}', [RentaController::class, 'update'])->middleware(ApiAuthMiddleware::class);
    Route::delete('/vehiculo/destroyRent/{id}', [RentaController::class, 'destroy'])->middleware(ApiAuthMiddleware::class);
    
    /* RUTAS TARJETA */
    Route::put('/tarjeta/updateCard/{id}', [TarjetaController::class, 'update'])->middleware(ApiAuthMiddleware::class);
    Route::post('/tarjeta/add', [TarjetaController::class,'store'])->middleware(ApiAuthMiddleware::class);
    Route::get('/tarjeta/getCards', [TarjetaController::class, 'index'])->middleware(ApiAuthMiddleware::class);
    Route::get('/tarjeta/getCard/{id}', [TarjetaController::class, 'show'])->middleware(ApiAuthMiddleware::class);
    Route::delete('/tarjeta/destroyCard/{id}', [TarjetaController::class, 'destroy'])->middleware(ApiAuthMiddleware::class);

    /* RUTAS CLIENTE */
    Route::post('/cliente/add', [ClienteController::class,'store'])->middleware(ApiAuthMiddleware::class);
    Route::get('/cliente/getClients', [ClienteController::class, 'index'])->middleware(ApiAuthMiddleware::class);
    Route::get('/cliente/getClient/{id}', [ClienteController::class, 'show'])->middleware(ApiAuthMiddleware::class);
    Route::put('/cliente/updateClient/{id}',  [ClienteController::class, 'update'])->middleware(ApiAuthMiddleware::class);
    Route::delete('/cliente/destroyClient/{id}', [ClienteController::class, 'destroy'])->middleware(ApiAuthMiddleware::class);


    // Rutas automáticas RESTful
    Route::resource('/cliente', ClienteController::class, ['except' => ['create', 'edit']])->middleware(ApiAuthMiddleware::class);
    Route::resource('/tarjeta', TarjetaController::class, ['except' => ['create', 'edit']])->middleware(ApiAuthMiddleware::class);
    Route::resource('/user', UserController::class, ['except' => ['create', 'edit']])->middleware(ApiAuthMiddleware::class);
    Route::resource('/licencia', LicenciaController::class, ['except' => ['create','edit']])->middleware(ApiAuthMiddleware::class);
});

