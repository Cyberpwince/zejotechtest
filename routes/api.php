<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminController;

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
    Route::post('user/register', [UserController::class, 'register']);
    Route::post('user/login', [UserController::class, 'login']);
    Route::get('user/emailotp/{id}', [UserController::class, 'requestVerification']);
    Route::post('user/emailotp', [UserController::class, 'VerifyCode']);
    Route::post('user/withdraw', [UserController::class, 'withdrawfund']);
    Route::get('user/withdraw/{id}', [UserController::class, 'withdrawall']);
    Route::get('user/withdraw/{id}/{withdrawalid}', [UserController::class, 'withdrawalid']);

});
Route::prefix('v1')->group(function () {
    Route::post('admin/register', [AdminController::class, 'Adminregister']);
    Route::post('admin/login', [AdminController::class, 'Adminlogin']);
    Route::post('admin/funduser', [AdminController::class, 'FundUser']);

});