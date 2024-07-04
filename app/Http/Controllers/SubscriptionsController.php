<?php

namespace App\Http\Controllers;

use App\Models\PaymentPlan;
use App\Models\UsersPayment;
use App\Models\UsersPaymentPlan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubscriptionsController extends Controller
{
    private const INDIVIDUAL_PLAN_ID = "prod_QOIEBD6W80eZUr";
    private const INDIVIDUAL_PLAN_MONTHLY_PRICE_ID = "price_1PXVeL2NevGfJdqUFQKY9qC6";
    private const INDIVIDUAL_PLAN_YEARLY_PRICE_ID = "price_1PXVeL2NevGfJdqUyNHQ13yF";
    private const ENTERPRISE_PLAN_ID = "prod_QOIHKN4cRyh0Pj";
    private const ENTERPRISE_PLAN_MONTHLY_PRICE_ID = "price_1PXVgq2NevGfJdqUdFAwbbQV";
    private const ENTERPRISE_PLAN_YEARLY_PRICE_ID = "price_1PXVgq2NevGfJdqU2MYV0aZw";


    // !CANCEL SUBSCRIPTION
    public function cancelSubscription(Request $request): JsonResponse
    {
        $user = Auth::user();
        $subscriptionId = $request->input('subscription_id');

        $userPaymentPlanId = $request->input('user_payment_plan_id');

        $userPaymentPlan = UsersPaymentPlan::findOrFail($userPaymentPlanId);
        $userPaymentPlan->is_active = '0';
        $userPaymentPlan->save();

        try {
            $subscription = $user->subscriptions()->where('stripe_id', $subscriptionId)->firstOrFail();
            $subscription->cancel();

            return DefaultResponseBodies::getSuccessResponse(message: "Subscription canceled successfully");
        } catch (\Exception $e) {
            return DefaultResponseBodies::getInternalServerErrorResponse(message: "Unknown Error");
        }
    }

    // !Resume SUBSCRIPTION
    public function resumeSubscription(Request $request): JsonResponse
    {
        $user = Auth::user();
        $subscriptionId = $request->input('subscription_id');

        $userPaymentPlanId = $request->input('user_payment_plan_id');

        $userPaymentPlan = UsersPaymentPlan::findOrFail($userPaymentPlanId);
        $userPaymentPlan->is_active = '1';
        $userPaymentPlan->save();

        try {
            $subscription = $user->subscriptions()->where('stripe_id', $subscriptionId)->firstOrFail();
            $subscription->resume();

            return DefaultResponseBodies::getSuccessResponse(message: "Subscription canceled successfully");
        } catch (\Exception $e) {
            return DefaultResponseBodies::getInternalServerErrorResponse(message: "Unknown Error");
        }
    }


    // !update payment method
    public function updatePaymentMethod(Request $request)
    {
        $user = Auth::user();
        $paymentMethodId = $request->input('payment_method_id');

        if (!$paymentMethodId) {
            return DefaultResponseBodies::getValidationErrorResponse(error: "Subscription ID is required", message: "Subscription ID is required");
        }

        try {
            // Update the payment method
            $user->updateDefaultPaymentMethod($paymentMethodId);

            // Attach the payment method to the customer
            $stripe = new StripeClient(config('cashier.webhook.secret'));
            $stripe->paymentMethods->attach($paymentMethodId, ['customer' => $user->stripe_id]);

            return response()->json(['status' => 'Payment method updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    // !CHECKOUT
    public function checkoutPlan(Request $request, $plan = "individual", $price = 'monthly'): JsonResponse
    {
        $user = Auth::user();

        // Define the plan and price IDs
        $planIds = [
            'individual' => $this::INDIVIDUAL_PLAN_ID,
            'enterprise' => $this::ENTERPRISE_PLAN_ID
        ];

        $priceIds = [
            'individual' => [
                'monthly' => $this::INDIVIDUAL_PLAN_MONTHLY_PRICE_ID,
                'yearly' => $this::INDIVIDUAL_PLAN_YEARLY_PRICE_ID
            ],
            'enterprise' => [
                'monthly' => $this::ENTERPRISE_PLAN_MONTHLY_PRICE_ID,
                'yearly' => $this::ENTERPRISE_PLAN_YEARLY_PRICE_ID
            ]
        ];

        $paymentPlan = PaymentPlan::whereRaw("LOWER(REPLACE(payment_plan_title, ' ', '')) LIKE ?", ['%'.$plan.'%'])->first();
        if(!$paymentPlan) {
            return DefaultResponseBodies::getNotFoundResponse(message: "Payment Plan Not Found");
        }

        $planPrice = $price == "monthly" ? $paymentPlan->payment_plan_monthly_price : $paymentPlan->payment_plan_yearly_price;

        // Generate a secure token for the session
        $sessionToken = Str::random(40);

        //delete previous subscriptions before creating new one
        $user->subscriptions()->forceDelete(); //TODO: maybe this have to be in success method,


        //delete previous user payment plans before creating new one
        $userPaymentPlans = $user->userPaymentPlans()->get();
        Log::info("user payment info". json_encode($userPaymentPlans));
        if ($userPaymentPlans) {
            foreach ($userPaymentPlans as $userPaymentPlan) {
                $userPaymentPlan->is_active = "0";
                $userPaymentPlan->deleted_at = Carbon::now();
                $userPaymentPlan->save();
            }
        }

        //create unactive new user payment plan
        $subscription = new UsersPaymentPlan();
        $subscription->payment_plan_id = $paymentPlan->id;
        $subscription->user_id = $user->id;
        $subscription->status = $price;
        $subscription->is_active = '0';
        $subscription->bill_date = Carbon::now()->toDateString();
        $subscription->save();

        //create new payment
        $payment = new UsersPayment();
        $payment->user_id = $user->id;
        $payment->amount = $planPrice;
        $payment->currency = 'usd';
        $payment->status = 'pending';
        // Store the session token in the order for later verification
        $payment->session_token = $sessionToken;
        $payment->user_payment_plan_id = $subscription->id;
        $payment->save();

        // Determine the plan ID and price ID
        $planId = $planIds[$plan] ?? $this::INDIVIDUAL_PLAN_ID;
        $priceId = $priceIds[$plan][$price] ?? $priceIds['individual']['monthly'];

        // Create the checkout session
        $checkoutSession = $user->newSubscription($planId, $priceId)
            ->checkout([
                'success_url' => route("subscriptionSuccess", [
                    'token' => $sessionToken,
                    'subscription_id'=>$subscription->id,
                    'payment_id'=>$payment->id
                ]),
                'cancel_url' => route("subscriptionFail", [
                    'token' => $sessionToken,
                    'subscription_id'=>$subscription->id,
                    'payment_id'=>$payment->id
                ])
            ]);

        //update payment info after create session
        $payment->stripe_session_id = $checkoutSession->id;
        $payment->amount = $checkoutSession->amount_total / 100; // Convert to dollars
        $payment->currency = $checkoutSession->currency;
        $payment->save();


        // Return the response
        return DefaultResponseBodies::getSuccessResponse(
            data: [
                'url' => $checkoutSession->url
            ],
            message: "Go to payment page successfully"
        );
    }
    public function subscriptionSuccess(Request $request) {
        // Retrieve info
        $subscriptionId = $request->query('subscription_id');
        $paymentId = $request->query('payment_id');
        $token = $request->query('token');

        $payment = UsersPayment::where('session_token', $token)->first();
        if (!$payment) {
            return DefaultResponseBodies::getUnauthorizedResponse();
        }
        // Verify the payment ID from the query
        if ($paymentId != $payment->id) {
            return DefaultResponseBodies::getUnauthorizedResponse();
        }

        $usersPaymentPlan = UsersPaymentPlan::findOrFail($subscriptionId);

        // Update order status to accepted
        $usersPaymentPlan->is_active = '1';
        $usersPaymentPlan->save();

        //update payment status to accepted
        $payment->status = 'accepted';
        $payment->session_token = null;
        $payment->save();

        // Redirect to the specified URL
        return redirect(env('CLIENT_DOMAIN') . "/profile/payment-plan");
    }

    public function subscriptionFail(Request $request) {
        // Retrieve info
        $subscriptionId = $request->query('subscription_id');
        $paymentId = $request->query('payment_id');
        $token = $request->query('token');

        $payment = UsersPayment::where('session_token', $token)->first();
        if (!$payment) {
            return DefaultResponseBodies::getUnauthorizedResponse();
        }
        // Verify the payment ID from the query
        if ($paymentId != $payment->id) {
            return DefaultResponseBodies::getUnauthorizedResponse();
        }

        $usersPaymentPlan = UsersPaymentPlan::findOrFail($subscriptionId);

        // Update order status to accepted
        $usersPaymentPlan->is_active = '0';
        $usersPaymentPlan->save();

        //update payment status to accepted
        $payment->status = 'rejected';
        $payment->session_token = null;
        $payment->save();

        // Redirect to the specified URL
        return redirect(env('CLIENT_DOMAIN') . "/profile/payment-plan");
    }
}
