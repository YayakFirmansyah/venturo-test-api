<?php

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BaseController::class, 'index']);
Route::get('/menu', [BaseController::class, 'menu']);
Route::get('/transaksi/{tahun}', [BaseController::class, 'transaksi']);
