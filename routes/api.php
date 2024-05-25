<?php

use App\Http\Controllers\AccountsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorizationController;
use App\Http\Controllers\TransactionController;
use App\Http\Middleware\BeforeAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('register', [AuthController::class, 'register'])->middleware(BeforeAction::class);;
Route::post('login', [AuthController::class, 'login'])->middleware(BeforeAction::class);
Route::post('me', [AuthController::class, 'me'])->middleware('auth');

Route::post('account', [AccountsController::class, 'create'])->middleware(['auth', BeforeAction::class]);
Route::post('trx/recharge', [TransactionController::class, 'recharge'])->middleware(['auth', BeforeAction::class]);
Route::post('trx/transfer', [TransactionController::class, 'transfer'])->middleware(['auth', BeforeAction::class]);
Route::post('trx/authorization', [AuthorizationController::class, 'approvedTransfer'])->middleware(['auth', BeforeAction::class]);
