<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DefaultResponseBodies extends Controller
{
    public static function getSuccessResponse($data = [], $message = "200 Success Response") {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message
        ], 200)->header('Accept', 'application/json');
    }

    public static function getBadRequestResponse($error = "Bad Request", $message = "400 Bad Request") {
        return response()->json([
            'success' => false,
            'error' => $error,
            'message' => $message
        ], 400);
    }

    public static function getUnauthorizedResponse($error = "Unauthorized", $message = "401 Unauthorized") {
        return response()->json([
            'success' => false,
            'error' => $error,
            'message' => $message
        ], 401);
    }

    public static function getForbiddenResponse($error = "Forbidden", $message = "403 Forbidden") {
        return response()->json([
            'success' => false,
            'error' => $error,
            'message' => $message
        ], 403);
    }

    public static function getNotFoundResponse($error = "Not Found", $message = "404 Not Found") {
        return response()->json([
            'success' => false,
            'error' => $error,
            'message' => $message
        ], 404);
    }

    public static function getMethodNotAllowedResponse($error = "Method Not Allowed", $message = "405 Method Not Allowed") {
        return response()->json([
            'success' => false,
            'error' => $error,
            'message' => $message
        ], 405);
    }

    public static function getInternalServerErrorResponse($error = "Internal Server Error", $message = "500 Internal Server Error") {
        return response()->json([
            'success' => false,
            'error' => $error,
            'message' => $message
        ], 500);
    }


}

