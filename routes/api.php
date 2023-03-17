<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



//authentication Routes:
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('forgot', 'forgot');
    Route::put('refresh','refresh');
    Route::put('reset/{token}', 'reset')->name('reset.password.post');
    Route::get('/email/verify/{id}/{hash}', 'verify')
    ->name('verification.verify');
});

Route::apiresource('categories', CategoryController::class);
Route::apiResource('books', BookController::class);
Route::apiResource('roles', RoleController::class);

Route::controller(UserController::class)->group(function () {
    Route::get('user','user');
    Route::put('user/updatePassword','updatePassword');
    Route::put('user/updateName','updateName');
    Route::put('user/updateEmail','updateEmail');

})->middleware('JwtAuth');

// Route::middleware('IsAdmin')->group(function () {
//     Route::get('users' , [UserController::class,'users']);
//     Route::put('admin/switch-role/{id}' ,[UserController::class,'switchRole']);
//     Route::get('user/{id}', [UserController::class,'showOneUser']);
//  });
