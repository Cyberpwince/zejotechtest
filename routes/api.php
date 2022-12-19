<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegisterController;

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
// Route::prefix('v1/users')->group(function () {
//     Route::post('register', [RegisterController::class, 'register']);
// });
Route::prefix('v1')->group(function () {
    Route::post('user/register', [RegisterController::class, 'register']);
    Route::post('user/login', [RegisterController::class, 'login']);

});
Route::prefix('v1')->group(function () {
    Route::post('admin/register', [RegisterController::class, 'Adminregister']);
    Route::post('admin/login', [RegisterController::class, 'login']);

});
