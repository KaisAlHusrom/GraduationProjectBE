<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UsersPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Illuminate\Support\Str;

class StripePaymentController extends Controller
{

    public function checkout(Request $request) {
        $orderItems = $request->order_items;
        $user = Auth::user();

        // Generate a secure token for the session
        $sessionToken = Str::random(40);


        // Create the order with a pending status
        $order = new Order();
        $order->user_id = $user->id;
        $order->total_amount = array_sum(array_column($orderItems, 'product_price'));
        $order->currency = 'usd';
        $order->status = 'pending';
        // Store the session token in the order for later verification
        $order->session_token = $sessionToken;
        $order->save();

        // Save each order item to the database
        foreach ($orderItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'sub_total' => $item['product_price'],
                'quantity' => 1 // Adjust if you have a quantity field
            ]);
        }

        //create new payment
        $payment = new UsersPayment();
        $payment->user_id = $user->id;
        $payment->amount = array_sum(array_column($orderItems, 'product_price'));
        $payment->currency = 'usd';
        $payment->status = 'pending';
        // Store the session token in the order for later verification
        $payment->session_token = $sessionToken;
        $payment->order_id = $order->id;
        $payment->save();



        Stripe::setApiKey(config('stripe.sk'));

        // Map through $orderItems and prepare line items for Stripe
        $lineItems = array_map(function($item) {
            return [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item['product_name']
                    ],
                    'unit_amount' => ($item['product_price'] * 100)
                ],
                'quantity' => 1 // Assuming you have a quantity field
            ];
        }, $orderItems);

        $session = Session::create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route(
                'success-checkout',
                [
                    'token' => $sessionToken,
                    'order_id'=>$order->id,
                    'payment_id'=>$payment->id
                ]
            ),
            'cancel_url' => route(
                'fail-checkout',
                [
                    'token' => $sessionToken,
                    'order_id'=>$order->id,
                    'payment_id'=>$payment->id
                ]
            ),
            'payment_method_types' => ["card"],
            'metadata' => [
            ]
        ]);

        //update order info after create session
        $order->stripe_session_id = $session->id;
        $order->total_amount = $session->amount_total / 100; // Convert to dollars
        $order->currency = $session->currency;
        $order->save();

        //update payment info after create session
        $payment->stripe_session_id = $session->id;
        $payment->amount = $session->amount_total / 100; // Convert to dollars
        $payment->currency = $session->currency;
        $payment->save();

        return DefaultResponseBodies::getSuccessResponse(
            data: [
                'url' => $session->url
            ],
            message: "Go to payment page successfully"
        );
    }


    //when payment success
    public function success(Request $request) {
        Stripe::setApiKey(config('stripe.sk'));

        // Retrieve session ID from request
        $orderId = $request->query('order_id');
        $paymentId = $request->query('payment_id');
        $token = $request->query('token');

        // Fetch the order using the token
        $order = Order::where('session_token', $token)->first();
        if (!$order) {
            return DefaultResponseBodies::getUnauthorizedResponse();
        }

        $payment = UsersPayment::where('session_token', $token)->first();
        if (!$payment) {
            return DefaultResponseBodies::getUnauthorizedResponse();
        }

        // Verify the order ID from the query
        if ($orderId != $order->id) {
            return DefaultResponseBodies::getUnauthorizedResponse();
        }
        // Verify the payment ID from the query
        if ($paymentId != $payment->id) {
            return DefaultResponseBodies::getUnauthorizedResponse();
        }

        // Update order status to accepted
        $order->status = 'accepted';
        $order->session_token = null;
        $order->save();

        //update payment status to accepted
        $payment->status = 'accepted';
        $payment->session_token = null;
        $payment->save();

        //transfer the percentage that product creator will profit
        // Define the service tax percentage
        $serviceTaxPercentage = 20; // 20% //TODO: I have to add this to db

        // Loop through each product in the order
        foreach ($order->products as $product) {
            // Get the owner of the product
            $productOwner = $product->user;

            // Calculate the profit after deducting the service tax
            $profit = $product->product_price - ($product->product_price * $serviceTaxPercentage / 100);

            // Update the owner's total balance
            $productOwner->total_balance += $profit;

            // Save the updated balance back to the database
            $productOwner->save();
        }


        // Redirect to the specified URL
        return redirect(env('CLIENT_DOMAIN') . "/profile/orders-billing");
    }

    //when payment fail
    public function fail(Request $request)
    {
        Stripe::setApiKey(config('stripe.sk'));

        // Retrieve session ID from request
        $orderId = $request->query('order_id');
        $paymentId = $request->query('payment_id');
        $token = $request->query('token');

        // Fetch the order using the token
        $order = Order::where('session_token', $token)->first();
        if (!$order) {
            return DefaultResponseBodies::getUnauthorizedResponse();
        }

        //fetch the payment
        $payment = UsersPayment::where('session_token', $token)->first();
        if (!$payment) {
            return DefaultResponseBodies::getUnauthorizedResponse();
        }

        // Verify the order ID from the query
        if ($orderId != $order->id) {
            return DefaultResponseBodies::getUnauthorizedResponse();
        }
        // Verify the payment ID from the query
        if ($paymentId != $payment->id) {
            return DefaultResponseBodies::getUnauthorizedResponse();
        }

        // Update order status to accepted
        $order->status = 'rejected';
        $order->session_token = null;
        $order->save();

        //update payment status to accepted
        $payment->status = 'rejected';
        $payment->session_token = null;
        $payment->save();

        // Redirect to the specified URL
        return redirect(env('CLIENT_DOMAIN') . "/profile/orders-billing");
    }
//env('CLIENT_DOMAIN') . "/profile/orders-billing"
}
