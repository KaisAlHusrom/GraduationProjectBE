<?php

use App\Http\Controllers\ElementPropController;
use App\Http\Controllers\ElementTypeController;
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

//Element Types
Route::get("element_types/fetch_deleted", [ElementTypeController::class, 'fetchDeleted']); //TODO: You have to put these routes before resource to be founded
Route::put('element_types/restore/{elementTypeId}', [ElementTypeController::class, 'restore']);
Route::resource("element_types", ElementTypeController::class);

//Element Props
Route::get("element_props/fetch_deleted", [ElementPropController::class, 'fetchDeleted']);
Route::put('element_props/restore/{elementTypeId}', [ElementPropController::class, 'restore']);
Route::resource("element_props", ElementPropController::class);
