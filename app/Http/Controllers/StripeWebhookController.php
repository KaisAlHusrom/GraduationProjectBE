<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UsersPaymentPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;

class StripeWebhookController extends CashierController
{

    public function handleWebhook(Request $request)
    {
        // Call the parent handleWebhook method to process default behavior
        parent::handleWebhook($request);
        $endpointSecret = config('cashier.webhook.secret'); // Ensure you have this set in your config/services.php

        $payload = @file_get_contents('php://input');
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        if ($event['type'] == 'invoice.payment_succeeded') {
            $invoice = $event['data']['object'];
            $customerId = $invoice['customer'];
            //TODO: add to subscribes table, I thing after server listen this function, default event is paused

            // Find the user by Stripe customer ID
            $user = User::where('stripe_id', $customerId)->first();

            if ($user) {
                try {
                    // Get the active payment plan
                    $activePaymentPlan = UsersPaymentPlan::with("paymentPlan")
                        ->where("user_id", $user->id)
                        ->first();

                    if ($activePaymentPlan) {
                        $status = $activePaymentPlan->status; // monthly or yearly

                        // Determine the next payment date based on the subscription cycle
                        $currentBillDate = $activePaymentPlan->bill_date ? strtotime($activePaymentPlan->bill_date) : time();
                        if ($status == 'monthly') {
                            $nextBillDate = strtotime('+1 month', $currentBillDate);
                        } elseif ($status == 'yearly') {
                            $nextBillDate = strtotime('+1 year', $currentBillDate);
                        } else {
                            Log::warning('Unknown subscription status:', ['status' => $status]);
                            return response()->json(['error' => 'Unknown subscription status'], 400);
                        }

                        // Update the bill_date
                        $activePaymentPlan->bill_date = date('Y-m-d', $nextBillDate);
                        $activePaymentPlan->save();

                        Log::info('User bill date updated:', ['user_id' => $user->id, 'next_bill_date' => $user->bill_date]);
                    } else {
                        Log::warning('Active payment plan not found for user:', ['user_id' => $user->id]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error updating bill date:', ['error' => $e->getMessage()]);
                }
            } else {
                Log::warning('User not found for Stripe customer ID:', ['customer_id' => $customerId]);
            }
        } else {
            Log::info('Unhandled Stripe event type:', ['event_type' => $event['type']]);
        }

        return response()->json(['status' => 'success'], 200);
    }
}
