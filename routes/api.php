<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\UpdatePersonalInfoController;
use App\Http\Controllers\Auth\VerifyController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Auth
Route::prefix('/auth')->group(function () {
    Route::post('/register', [RegisterController::class, 'store'])->middleware('guest:sanctum');
    Route::post('/verify', [VerifyController::class, 'store'])->middleware('guest:sanctum');
    Route::post('/login', [LoginController::class, 'store'])->middleware('guest:sanctum');
    Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth:sanctum');
    Route::post('/forget-password', [ForgetPasswordController::class, 'store'])->middleware('guest:sanctum');
    Route::post('/new-password', [NewPasswordController::class, 'store'])->middleware('guest:sanctum');
    Route::post('/update-user-info', [UpdatePersonalInfoController::class, 'store'])->middleware('auth:sanctum');
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');
});

//Apartment
Route::get('/apartments', [ApartmentController::class, 'index']);
Route::get('/apartments/top-rated', [ApartmentController::class, 'topRated']);
Route::get('/apartments/{apartment}', [ApartmentController::class, 'show']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/apartments', [ApartmentController::class, 'store']);
    Route::put('/apartments/{apartment}', [ApartmentController::class, 'update']);
    Route::delete('/apartments/{apartment}', [ApartmentController::class, 'destroy']);
    Route::post('/apartments/{apartment}/rate', [ApartmentController::class, 'rateApartment']);
});

//Booking
Route::middleware('auth:sanctum')->group(function () {
    Route::post('bookings', [BookingController::class, 'store']);
    Route::get('bookings', [BookingController::class, 'index']);
    Route::patch('bookings/{booking}', [BookingController::class, 'update']);
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'tenantCancel']);
    Route::post('/bookings/{booking}/request-update', [BookingController::class, 'tenantRequestUpdate']);
});

//Admin
Route::prefix('/admin')->group(function () {
    Route::get('/show-registers', [AdminController::class, 'showRegisters'])->middleware(['auth:sanctum', 'checkAdmin']);
    Route::put('/approve-registration/{user}', [AdminController::class, 'approvedRegistration'])->middleware(['auth:sanctum', 'checkAdmin']);
    Route::delete('/delete-registration/{user}', [AdminController::class, 'deleteRegistration'])->middleware(['auth:sanctum', 'checkAdmin']);
});

//Favorite
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/{apartment}', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{apartment}', [FavoriteController::class, 'destroy']);
});
