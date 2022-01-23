<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// login, register, logout
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout']);

// get all active users
Route::get('users/active', [UserController::class, 'active']);
// get all inactive users
Route::get('users/inactive', [UserController::class, 'inactive']);
// get all blocked users
Route::get('users/blocked', [UserController::class, 'blocked']);

Route::resource('user', UserController::class)->except(['create', 'edit', 'store']);
Route::resource('admin', AdminController::class)->except(['create', 'edit']);
