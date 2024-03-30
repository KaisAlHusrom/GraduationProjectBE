<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DefaultResponseBodies extends Controller
{
    // public static function fetchDataSuccessResponse(
    //     $data = [],
    //     $message = "200 Success Response",
    //     $length
    // ) {
    //     return response()->json([
    //         'success' => true,
    //         'data' => $data,
    //         'message' => $message
    //     ], 200)->header('Accept', 'application/json');
    // }

    public static function getSuccessResponse($data = [], $message = "200 Success Response"): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message
        ], 200)->header('Accept', 'application/json');
    }

    public static function getBadRequestResponse($error = "Bad Request", $message = "400 Bad Request"): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $error,
            'message' => $message
        ], 400);
    }

    public static function getUnauthorizedResponse($error = "Unauthorized", $message = "401 Unauthorized"): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $error,
            'message' => $message
        ], 401);
    }

    public static function getForbiddenResponse($error = "Forbidden", $message = "403 Forbidden"): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $error,
            'message' => $message
        ], 403);
    }

    public static function getNotFoundResponse($error = "Not Found", $message = "404 Not Found"): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $error,
            'message' => $message
        ], 404);
    }

    public static function getMethodNotAllowedResponse($error = "Method Not Allowed", $message = "405 Method Not Allowed"): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $error,
            'message' => $message
        ], 405);
    }

    public static function getInternalServerErrorResponse($error = "Internal Server Error", $message = "500 Internal Server Error"): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $error,
            'message' => $message
        ], 500);
    }

    public static function getValidationErrorResponse(
        $error = "Validation Error",
        $message = "422 Validation error",
        $errors = []
        ): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $error,
            'errors' => $errors,
            'message' => $message
        ], 422);
    }


}

