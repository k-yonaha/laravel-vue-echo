<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatMessageController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('chat', [ChatMessageController::class,'index'])->name('chat.index');
Route::post('chat', [ChatMessageController::class,'create'])->name('chat.create');

