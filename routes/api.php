<?php

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\product\ProductController;
    use App\Http\Controllers\Api\AuthController;
    // Route::get('/user', function (Request $request) {
        //     return $request->user();
        // })->middleware('auth:sanctum');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    Route::post('/register',[AuthController::class,'register'])->name('register');
    Route::post('/login',[AuthController::class,'login'])->name('login');
    Route::get('/all-product',[ProductController::class,'getProduct']);
    Route::middleware('auth:api')->group(function(){
        Route::get('/profile',[AuthController::class,'me'])->name('profile');
        Route::post('/logout',[AuthController::class,'logout'])->name('logout');
    });
    });

