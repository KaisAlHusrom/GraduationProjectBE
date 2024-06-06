<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;

class UtilsRoutes
{
    //routes for admins
    public static function generateResourceAdminRoutes($prefix, $controller): void
    {

        Route::group(['prefix' => 'admin/' . $prefix], function () use ($controller) {
            Route::get("fetch_deleted", [$controller, 'fetchDeleted']);
            Route::get("fetch/{id}", [$controller, 'fetchSpecificRecord']);
            Route::get("check/{id}", [$controller, 'checkSpecificRecord']);
            Route::delete('{ids}', [$controller, 'destroy']);
            Route::patch('{id}', [$controller, 'update']);
            Route::delete('permanent-delete/{ids}', [$controller, 'permanentDelete']);
        });

        // index and store routes
        Route::resource('admin/' . $prefix, $controller)->except(['destroy', 'update']);
    }

    //routes for normal users
    public static function generateResourceUsersRoutes($prefix, $controller, $excepts = []): void
    {
        $resourceExcludes = ['destroy', 'update'];

        if (in_array('store', $excepts)) {
            $resourceExcludes[] = 'store';
        }
        if (in_array('index', $excepts)) {
            $resourceExcludes[] = 'index';
        }

        // Register resource routes with the calculated exclusions
        Route::resource('user/' . $prefix, $controller)->except($resourceExcludes);

        Route::group(['prefix' => 'user/' . $prefix], function () use ($controller, $excepts) {
            if (!in_array('fetchDeleted', $excepts)) {
                Route::get("fetch_deleted", [$controller, 'fetchDeleted']);
            }

            if (!in_array('fetchSpecificRecord', $excepts)) {
                Route::get("fetch/{id}", [$controller, 'fetchSpecificRecord']);
            }

            if (!in_array('checkSpecificRecord', $excepts)) {
                Route::get("check/{id}", [$controller, 'checkSpecificRecord']);
            }

            if (!in_array('restore', $excepts)) {
                Route::put('restore/{ids}', [$controller, 'restore']);
            }

            if (!in_array('destroy', $excepts)) {
                Route::delete('{ids}', [$controller, 'destroy']);
            }

            if (!in_array('update', $excepts)) {
                Route::patch('{id}', [$controller, 'update']);
            }

            if (!in_array('permanentDelete', $excepts)) {
                Route::delete('permanent-delete/{ids}', [$controller, 'permanentDelete']);
            }

//        Route::patch('{id}', [$controller, 'update']);
        });

    }
}
