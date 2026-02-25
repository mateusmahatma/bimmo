<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function subscribe(Request $request)
    {
        $user = auth()->user();

        // Create pending order
        $order = Order::create([
            'user_id' => $user->id,
            'amount' => 49000,
            'status' => 'pending',
        ]);

        try {
            if (!extension_loaded('curl')) {
                return response()->json([
                    'success' => false,
                    'message' => 'PHP CURL extension is not enabled in your web server. Please enable it in php.ini and restart Laragon.'
                ], 500);
            }

            $snapToken = $this->midtransService->getSnapToken($order);
            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $order->id
            ]);
        }
        catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Midtrans Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function webhook(Request $request)
    {
        $notification = $request->all();
        $orderIdFull = $notification['order_id'];
        $orderId = explode('-', $orderIdFull)[0];
        $transactionStatus = $notification['transaction_status'];
        $paymentType = $notification['payment_type'];
        $fraudStatus = $notification['fraud_status'];

        $order = Order::find($orderId);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $order->status = 'challenge';
            }
            else if ($fraudStatus == 'accept') {
                $order->status = 'success';
            }
        }
        else if ($transactionStatus == 'settlement') {
            $order->status = 'success';
        }
        else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            $order->status = 'failure';
        }
        else if ($transactionStatus == 'pending') {
            $order->status = 'pending';
        }

        $order->payment_method = $paymentType;
        $order->save();

        if ($order->status == 'success') {
            $user = User::find($order->user_id);
            $user->subscription_status = 1;
            $user->subscription_ends_at = now()->addMonth();
            $user->save();
        }

        return response()->json(['message' => 'OK']);
    }

    public function cancel()
    {
        $user = auth()->user();
        $user->subscription_auto_renew = false;
        $user->save();

        return back()->with('success', 'Subscription cancellation scheduled.');
    }
}
