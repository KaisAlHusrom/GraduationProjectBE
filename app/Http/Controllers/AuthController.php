<?php

namespace App\Http\Controllers;

use App\Helpers\FilesFunctions;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request): JsonResponse
    {
        try {
            // Validate the request data
            $validatedData = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'mobile_number' => 'required|string|max:15|unique:users',
                'is_admin' => 'boolean',
                'birth_date' => 'required|date',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);


            //save profile images
            if ($request->hasFile("profile_image")) {
                $fileValue = $request->file("profile_image");
                $fileName = FilesFunctions::saveFile(
                    file: $fileValue,
                    filesFolderName: null,
                    imagesFolderName: "UsersProfileImages",
                );
                $validatedData["profile_image"] = $fileName;
            }

            $validatedData["password"] = hash('sha256', $request->password);




            // Create a new user
            $user = User::create($validatedData);

            // Create a token for the user
            $token = $user->createToken('auth_token')->plainTextToken;

            return DefaultResponseBodies::getSuccessSignUpResponse(
                $token,
                data: $user,
                message: "User created successfully"
            );

        } catch (ModelNotFoundException $e) {
            return DefaultResponseBodies::getNotFoundResponse(error: $e->getMessage(), message: "Related model not found");
        } catch (QueryException $e) {
            return DefaultResponseBodies::getInternalServerErrorResponse(error: $e->getMessage(), message: "Database query error");
        } catch (ValidationException $e) {
            return DefaultResponseBodies::getValidationErrorResponse(
                error: $e->getMessage(),
                message: "Validation Error",
                errors: $e->validator->errors()
            );
        } catch (MassAssignmentException $e) {
            return DefaultResponseBodies::getInternalServerErrorResponse(error: $e->getMessage(), message: "Fillable Error");
        } catch (\Exception $e) {
            return DefaultResponseBodies::getInternalServerErrorResponse(error: $e->getMessage(), message: "Unexpected error");
        }
    }


    /**
     * Login a user.
     */
    public function login(Request $request): JsonResponse
    {
        try {
            // Validate the request data
            $validatedData = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);


            // Check if the user exists
            $user = User::where('email', $request->email)->first();

            if (!$user || !hash_equals($user->password, hash('sha256', $request->password))) {
                return DefaultResponseBodies::getUnauthorizedResponse(
                    error: "Invalid credentials",
                    message: "Email or Password Incorrect"
                );
            }

            // Create a token for the user
            $token = $user->createToken('auth_token')->plainTextToken;

            // Set the token in an HTTP-only cookie
            return DefaultResponseBodies::getSuccessLogInResponse(
                $token,
                data: $user,
            );
        }catch (ModelNotFoundException $e) {
            return DefaultResponseBodies::getNotFoundResponse(error: $e->getMessage(), message: "Related model not found");
        } catch (QueryException $e) {
            return DefaultResponseBodies::getInternalServerErrorResponse(error: $e->getMessage(), message: "Database query error");
        } catch (ValidationException $e) {
            return DefaultResponseBodies::getValidationErrorResponse(
                error: $e->getMessage(),
                message: "Validation Error",
                errors: $e->validator->errors()
            );
        } catch (MassAssignmentException $e) {
            return DefaultResponseBodies::getInternalServerErrorResponse(error: $e->getMessage(), message: "Fillable Error");
        } catch (\Exception $e) {
            return DefaultResponseBodies::getInternalServerErrorResponse(error: $e->getMessage(), message: "Unexpected error");
        }
    }

    /**
     * Logout a user (Revoke the token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        // Remove the token cookie
        return DefaultResponseBodies::getSuccessLogOutResponse();
    }
}
