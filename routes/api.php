<?php

use App\Http\Controllers\DesignController;
use App\Http\Controllers\ElementPropController;
use App\Http\Controllers\ElementTypeController;
use App\Http\Controllers\StylePropController;
use App\Http\Controllers\StylePropsCategoryController;
use App\Http\Controllers\StylePropsValueController;
use App\Http\Controllers\StyleResponsiveBreakpointController;
use App\Http\Controllers\StyleStatusController;
use App\Models\Design;
use App\Models\StylePropsCategory;
use App\Models\StylePropsValue;
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

function generateResourceRoutes($prefix, $controller): void
{
    Route::group(['prefix' => $prefix], function () use($controller) {
        // Element Types routes
        Route::get("fetch_deleted", [$controller, 'fetchDeleted']);
        Route::put('restore/{ids}', [$controller, 'restore']);
        Route::delete('{ids}', [$controller, 'destroy']);
        Route::delete('permanent-delete/{ids}', [$controller, 'permanentDelete']);
//        Route::patch('{id}', [$controller, 'update']);
    });
    Route::resource($prefix, $controller)->except(["destroy"]);
}

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//style props categories
generateResourceRoutes("style_props_categories", StylePropsCategoryController::class);

//style props
generateResourceRoutes("style_props", StylePropController::class);

//style props Values
generateResourceRoutes("style_prop_values", StylePropsValueController::class);

//style statuses
generateResourceRoutes("style_statuses", StyleStatusController::class);

//style breakpoints
generateResourceRoutes("style_breakpoints", StyleResponsiveBreakpointController::class);

//Element Types
generateResourceRoutes("element_types", ElementTypeController::class);

//Element Props
generateResourceRoutes("element_props", ElementPropController::class);

//Designs
generateResourceRoutes("designs", DesignController::class);

