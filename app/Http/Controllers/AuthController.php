<?php

namespace App\Http\Controllers;

use App\Helpers\FilesFunctions;

use App\Models\PaymentPlan;
use App\Models\UsersPaymentPlan;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
                'email' => 'required|string|email|max:255|unique:users',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'mobile_number' => 'required|string|max:17|unique:users',
                'is_admin' => 'boolean',
                'password' => 'required|string|min:8|confirmed',
            ])->validate();


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
            //TODO: change the bcrypt to sha256
//            $validatedData["password"] = hash('sha256', $request->password);


            // Add the free trial expiration date to the validated data
            $validatedData['free_trial_expiration_date'] = Carbon::now()->addDays(7);
            // Create a new user
            $user = User::create($validatedData);

            if($user) {
                //subscribe with free plan when sign up new account
                $freePlan = PaymentPlan::where("payment_plan_title", '=', "Free")->first();
                UsersPaymentPlan::create([
                    'payment_plan_id' => $freePlan->id,
                    'user_id' => $user->id,
                    'status' => "Active",
                    'expire_date' => Carbon::now()->addYear(), // Setting expire_date to one year from now
                    'is_active' => 1,
                    'bill_date' => Carbon::now(),
                ]);
                // Create a token for the user
                //$token = $user->createToken('auth_token', [], Carbon::now()->addDay())->plainTextToken;
                return DefaultResponseBodies::getSuccessResponse(
                    data: $user,
                    message: "Sign Up Successfully"
                );

            }
            return DefaultResponseBodies::getBadRequestResponse(
                message: "Sign Up Failed"
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
            ])->validate();

            $credentials = request(['email', 'password']);
            // Check if the user exists
            $user = User::where('email', $request->email)->with("paymentPlans")->first();

            if (!$user || !auth()->attempt($credentials)) {
                return DefaultResponseBodies::getUnauthorizedResponse(
                    error: "Invalid credentials",
                    message: "Email or Password Incorrect"
                );
            }

            // Revoke all existing tokens for the user
            $user->tokens()->where('tokenable_id', $user->id)->delete();

            // Create a new token for the user
            $token = $user->createToken('auth_token', [], Carbon::now()->addDay())->plainTextToken;

            // Set the token in an HTTP-only cookie
            return DefaultResponseBodies::getSuccessLogInResponse(
                token: $token,
                data: $user,
                message: "Logged in Successfully"
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
     * Logout a user (Revoke the token).
     */
    public function logout(Request $request): JsonResponse
    {
        // Get the current authenticated user
        $user = $request->user();

        if ($user) {
            // Delete the current token
            $request->user()->tokens()->delete();

            // Logout the user (this will only affect session-based authentication)
            Auth::logout();

            // Return success response
            return DefaultResponseBodies::getSuccessLogOutResponse();
        }

        // Return unauthorized response if no user is authenticated
        return DefaultResponseBodies::getUnauthorizedResponse();
    }


    //fetch user data if token is valid
    public function fetchUserData(Request $request)
    {
        // Retrieve the authenticated user ID
        $userId = $request->user()->id;

        // Fetch the user with the paymentPlans relationship
        $user = User::with(['paymentPlans'])->find($userId);

        // Check if the user exists
        if ($user) {
            return DefaultResponseBodies::getSuccessResponse(
                data: $user,
                message: "Get User Successfully"
            );
        } else {
            return DefaultResponseBodies::getNotFoundResponse(
                message: "There is no logged user",
            );
        }
    }
}
