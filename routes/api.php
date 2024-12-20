<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;

Route::post('login', [LoginController::class, 'index'])->name('login');
Route::post('logout', [LogoutController::class, 'index'])->name('logout');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('chat', [ChatMessageController::class, 'index'])->name('chat.index');
    Route::post('chat', [ChatMessageController::class, 'create'])->name('chat.create');
});
