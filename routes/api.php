<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TorobController;

Route::post('/torob/v3/products', [TorobController::class, '__invoke']);
