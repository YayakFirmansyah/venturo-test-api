<?php

use App\Http\Controllers\API\JsonController;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/menu', [JsonController::class, 'getMenu'])->name('menu');
Route::get('/transaksi/{tahun}', [JsonController::class, 'getAllTransaksi'])->name('transaksi');