<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//Route::post('/catalog/store', [\App\Http\Controllers\Api\Backend\Catalog\ImportDataController::class, 'store']);
Route::post('/catalog/import/file', [\App\Http\Controllers\Api\Backend\Catalog\ImportDataController::class, 'uploadFile']);
Route::post('/catalog/import/tire', [\App\Http\Controllers\Api\Backend\Catalog\ImportDataController::class, 'importTire']);
Route::post('/catalog/import/price', [\App\Http\Controllers\Api\Backend\Catalog\ImportDataController::class, 'importPrice']);
Route::post('/catalog/import/status', [\App\Http\Controllers\Api\Backend\Catalog\ImportDataController::class, 'getUploadStatus']);
Route::get('/catalog/import/test', [\App\Http\Controllers\Api\Backend\Catalog\ImportDataController::class, 'test']);

Route::get('/catalog/filter-keys', [\App\Http\Controllers\Api\Backend\Catalog\FilterTiresController::class, 'getFilteredData']);
Route::post('/catalog/filtered-tires', [\App\Http\Controllers\Api\Backend\Catalog\FilterTiresController::class, 'getFilteredTires']);
Route::get('/catalog/import-price-xml', [\App\Http\Controllers\Api\Backend\Catalog\Pages\ImportPriceXmlController::class, 'index']);
Route::post('/catalog/import-price-xml', [\App\Http\Controllers\Api\Backend\Catalog\Pages\ImportPriceXmlController::class, 'update']);
