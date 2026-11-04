<?php

use App\Http\Controllers\EmallsProductsController;
use App\Http\Controllers\TorobOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TorobController;

Route::post('/torob/v3/products', [TorobController::class, '__invoke']);
Route::get('/torob/v1/orders', [TorobOrderController::class, '__invoke']);

Route::match(['get', 'post'], '/list', [EmallsProductsController::class, 'index']);
