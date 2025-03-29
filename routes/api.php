<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProductController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');

  
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
     
Route::middleware('auth:api')->group( function () {
    Route::resource('products', ProductController::class);
});