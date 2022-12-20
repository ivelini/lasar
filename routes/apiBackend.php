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
Route::group(['prefix' => 'catalog'], function () {
    Route::group(['prefix' => 'import'], function () {
        Route::post('file', [\App\Http\Controllers\Api\Backend\Catalog\ImportDataController::class, 'uploadFile']);
        Route::post('tire', [\App\Http\Controllers\Api\Backend\Catalog\ImportDataController::class, 'importTire']);
        Route::post('price', [\App\Http\Controllers\Api\Backend\Catalog\ImportDataController::class, 'importPrice']);
        Route::get('status', [\App\Http\Controllers\Api\Backend\Catalog\ImportDataController::class, 'getUploadStatus']);
    });

    Route::group(['prefix' => 'storages'], function () {
        Route::get('', [\App\Http\Controllers\Api\Backend\Catalog\Pages\PageStorageController::class, 'index']);
        Route::post('', [\App\Http\Controllers\Api\Backend\Catalog\Pages\PageStorageController::class, 'update']);
    });

    Route::post('import-price-xml', [\App\Http\Controllers\Api\Backend\Catalog\Pages\ImportPriceXmlController::class, 'update']);
    Route::get('import-price-xml', [\App\Http\Controllers\Api\Backend\Catalog\Pages\ImportPriceXmlController::class, 'index']);
    Route::get('import-price-xml/get-file', [\App\Http\Controllers\Api\Backend\Catalog\Pages\ImportPriceXmlController::class, 'download']);
});

Route::group(['prefix' => 'page'], function () {
    Route::group(['prefix' => 'category'], function () {
        Route::get('index', [App\Http\Controllers\Api\Backend\Pages\PageCategoryController::class, 'index']);
        Route::post('add', [App\Http\Controllers\Api\Backend\Pages\PageCategoryController::class, 'store']);
        Route::delete('{id}', [App\Http\Controllers\Api\Backend\Pages\PageCategoryController::class, 'destroy']);
    });

    Route::get('index', [\App\Http\Controllers\Api\Backend\Pages\PageController::class, 'index']);
    Route::get('{id}', [\App\Http\Controllers\Api\Backend\Pages\PageController::class, 'show']);
    Route::post('add', [\App\Http\Controllers\Api\Backend\Pages\PageController::class, 'store']);
    Route::post('{id}', [\App\Http\Controllers\Api\Backend\Pages\PageController::class, 'update']);
    Route::delete('{id}', [\App\Http\Controllers\Api\Backend\Pages\PageController::class, 'destroy']);
});
