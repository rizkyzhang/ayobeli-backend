<?php

use App\Http\Controllers\Api\V1\IndonesiaAddressController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Hello World'
    ]);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::prefix('indonesia-address')->group(function () {
        Route::get('/provinces', [IndonesiaAddressController::class, 'getProvinces']);
        Route::get('/cities/{provinceId}', [IndonesiaAddressController::class, 'getCities']);
        Route::get('/districts/{cityId}', [IndonesiaAddressController::class, 'getDistricts']);
        Route::get('/villages/{districtId}', [IndonesiaAddressController::class, 'getVillages']);
    });
});
