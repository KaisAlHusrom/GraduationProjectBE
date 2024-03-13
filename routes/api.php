<?php

use App\Http\Controllers\ElementPropController;
use App\Http\Controllers\ElementTypeController;
use App\Http\Controllers\StylePropsCategoryController;
use App\Models\StylePropsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//style props categories
Route::group(['prefix' => 'style_props_categories'], function () {
    // Element Types routes
    Route::get("fetch_deleted", [StylePropsCategoryController::class, 'fetchDeleted']);
    Route::put('restore/{ids}', [StylePropsCategoryController::class, 'restore']);
    Route::delete('{ids}', [StylePropsCategoryController::class, 'destroy']);
    Route::delete('permanent-delete/{ids}', [StylePropsCategoryController::class, 'permanentDelete']);
});
Route::resource('style_props_categories', StylePropsCategoryController::class)->except(["destroy"]);


//Element Types
Route::group(['prefix' => 'element_types'], function () {
    // Element Types routes
    Route::get("fetch_deleted", [ElementTypeController::class, 'fetchDeleted']);
    Route::put('restore/{ids}', [ElementTypeController::class, 'restore']);
    Route::delete('{ids}', [ElementTypeController::class, 'destroy']);
    Route::delete('permanent-delete/{ids}', [ElementTypeController::class, 'permanentDelete']);
});
Route::resource('element_types', ElementTypeController::class)->except(["destroy"]);


//Element Props
Route::group(['prefix' => 'element_props'], function () {
    // Fetch deleted element props
    Route::get("fetch_deleted", [ElementPropController::class, 'fetchDeleted']);

    // Restore deleted element props
    Route::put('restore/{ids}', [ElementPropController::class, 'restore']);

    // Regular delete element props
    Route::delete('{ids}', [ElementPropController::class, 'destroy']);

    // Permanent delete element props
    Route::delete('permanent-delete/{ids}', [ElementPropController::class, 'permanentDelete']);

});
// Resourceful routes for element props excluding destroy
Route::resource('element_props', ElementPropController::class)->except(["destroy"]);


