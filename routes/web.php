<?php

use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::get('/', function () {
    return view('welcome');
});

// Маршруты для работы с изображениями
Route::get('/images/brands/{filename}', [ImageController::class, 'showBrandImage']);
Route::get('/images/shoes/{filename}', [ImageController::class, 'showShoeImage']);
Route::post('/images/brands/upload', [ImageController::class, 'uploadBrandImage']);
Route::post('/images/shoes/upload', [ImageController::class, 'uploadShoeImage']);

