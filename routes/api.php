<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\ShopController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\PromoController;
use App\Http\Controllers\api\LaundryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// membuat api dengan endpoint (api/..) nantinya menampilkan respon dengan data yang diambil oleh controller dari models.
Route::get('/promo', [PromoController::class, 'readAll']);
Route::get('/shop', [ShopController::class, 'readAll']);
Route::get('/laundry', [LaundryController::class, 'readAll']);
Route::get('/user', [UserController::class, 'readAll']);

// method post karena kirim data
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// protected route, harus login dulu
Route::middleware('auth:sanctum')->group(function () {
    // route untuk mendapatkan promo dengan limit 5
    Route::get('/promo/limit', [PromoController::class, 'readLimit']);

    // route untuk mendapatkan rekomendasi shop dengan rate tertinggi
    Route::get('/shop/recommendation/limit', [ShopController::class, 'readRecommendation']);

    // route untuk mencari shop berdasarkan nama city
    Route::get('/shop/search/city/{name}', [ShopController::class, 'searchByCity']);

    // route untuk mendapatkan data laundry berdasarkan user_id
    Route::get('/laundry/user/{id}', [LaundryController::class, 'whereUserId']);

    // route untuk klaim laundry
    Route::post('/laundry/claim', [LaundryController::class, 'claim']);

});
