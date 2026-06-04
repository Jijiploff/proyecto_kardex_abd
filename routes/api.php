<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KardexController;

Route::get('/kardex/{producto}', [KardexController::class, 'consultaKardex']);
Route::get('/kardex/{producto}/fechas', [KardexController::class, 'consultaPorFechas']);
Route::get('/kardex/{producto}/mes', [KardexController::class, 'consultaPorMes']);
Route::get('/kardex/{producto}/anio', [KardexController::class, 'consultaPorAnio']);
