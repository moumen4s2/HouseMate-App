<?php

use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\UpdatePersonalInfoController;
use App\Http\Controllers\Auth\VerifyController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ApartmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Auth
Route::post('/register', [RegisterController::class, 'store'])->middleware('guest:sanctum');
Route::post('/verify', [VerifyController::class, 'store'])->middleware('guest:sanctum');
Route::post('/login', [LoginController::class, 'store'])->middleware('guest:sanctum');
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth:sanctum');
Route::post('/forget-password', [ForgetPasswordController::class, 'store'])->middleware('guest:sanctum');
Route::post('/new-password', [NewPasswordController::class, 'store'])->middleware('guest:sanctum');
Route::post('/update-user-info', [UpdatePersonalInfoController::class, 'store'])->middleware('auth:sanctum');

Route::get('/apartments', [ApartmentController::class, 'index']);
Route::get('/apartments', [ApartmentController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/apartments', [ApartmentController::class, 'store']); 
    Route::put('/apartments/{apartment}', [ApartmentController::class, 'update']); 
    Route::delete('/apartments/{apartment}', [ApartmentController::class, 'destroy']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::post('bookings', [BookingController::class, 'store']);
    Route::get('bookings', [BookingController::class, 'index']);
    Route::patch('bookings/{booking}', [BookingController::class, 'update']);
});