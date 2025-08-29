<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Library\SslCommerz\SslCommerzNotification; // From the library
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{


  public function index(Request $request)
    {
        Log::info('Accessing admin orders page', [
            'user_id' => Auth::user()->id,
            'user_role' => Auth::user()->role
        ]);

        $query = Order::with('user');

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
            Log::info('Filtering orders by search term', ['search' => $search]);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();
        $admin = Auth::user();

        return view('admin.orders', compact('orders', 'admin'));
    }
  public function create()
    {
        $cartItems = Cart::where('user_id', Auth::id())->with(['product' => function ($query) {
            $query->with('images');
        }])->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        return view('orders.create', compact('cartItems', 'subtotal'));
    }

    public function store(Request $request)
    {
        Log::info('Store method called', [
            'user_id' => Auth::id(),
            'payment_method' => $request->payment_method,
            'request_data' => $request->all(),
        ]);

        $request->validate([
            'address' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'payment_method' => 'required|in:cod,online',
        ]);

        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();

        if ($cartItems->isEmpty()) {
            Log::warning('Cart is empty', ['user_id' => Auth::id()]);
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $total = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        if ($total <= 0) {
            Log::error('Invalid cart total', ['user_id' => Auth::id(), 'total' => $total]);
            return redirect()->route('cart.index')->with('error', 'Invalid cart total.');
        }

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => Auth::id(),
                'total' => $total,
                'status' => 'pending',
                'address' => $request->address,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'notes' => $request->notes,
                'transaction_id' => 'SSLCZ_' . uniqid(),
            ]);

            Log::info('Order created', [
                'order_id' => $order->id,
                'transaction_id' => $order->transaction_id,
                'total' => $total,
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                ]);
            }

            // Clear cart
            Cart::where('user_id', Auth::id())->delete();
            Log::info('Cart cleared', ['user_id' => Auth::id()]);

            if ($request->payment_method === 'cod') {
                $order->update([
                    'payment_status' => 'pending',
                    'status' => 'pending',
                    'updated_at' => now(),
                ]);
                Log::info('COD order processed', [
                    'order_id' => $order->id,
                    'payment_status' => 'pending',
                    'status' => 'pending',
                ]);
                DB::commit();
                return redirect()->route('user.dashboard')->with('success', 'Order placed successfully with Cash on Delivery.');
            }

            // Online payment via SSLCommerz
            $post_data = [
                'store_id' => config('sslcommerz.apiCredentials.store_id'),
                'store_passwd' => config('sslcommerz.apiCredentials.store_password'),
                'total_amount' => $total,
                'currency' => 'BDT',
                'tran_id' => $order->transaction_id,
                'success_url' => config('sslcommerz.success_url'),
                'fail_url' => config('sslcommerz.failed_url'),
                'cancel_url' => config('sslcommerz.cancel_url'),
                'ipn_url' => config('sslcommerz.ipn_url'),
                'cus_name' => Auth::user()->name,
                'cus_email' => Auth::user()->email,
                'cus_add1' => $request->address,
                'cus_add2' => '',
                'cus_city' => 'Dhaka',
                'cus_state' => 'Dhaka',
                'cus_postcode' => '1000',
                'cus_country' => 'Bangladesh',
                'cus_phone' => Auth::user()->phone ?? '01700000000',
                'cus_fax' => '',
                'ship_name' => Auth::user()->name,
                'ship_add1' => $request->address,
                'ship_add2' => '',
                'ship_city' => 'Dhaka',
                'ship_state' => 'Dhaka',
                'ship_postcode' => '1000',
                'ship_country' => 'Bangladesh',
                'shipping_method' => 'NO',
                'product_name' => 'Products from Styler BD',
                'product_category' => 'Clothing',
                'product_profile' => 'general',
                'value_a' => 'order_id:' . $order->id,
            ];

            Log::info('SSLCommerz post_data', $post_data);

            $sslc = new SslCommerzNotification();
            $payment_options = $sslc->makePayment($post_data, 'hosted');

            Log::info('SSLCommerz response', ['response' => $payment_options]);

            if (!is_array($payment_options) || !isset($payment_options['GatewayPageURL'])) {
                throw new \Exception('Payment initiation failed: ' . json_encode($payment_options));
            }

            // Store order ID in session for safety
            session(['pending_order_id' => $order->id]);
            Log::info('Session set in store', ['pending_order_id' => session('pending_order_id')]);

            DB::commit();
            return redirect($payment_options['GatewayPageURL']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return redirect()->route('checkout')->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }

       public function payPendingOrders()
    {
        Log::info('Pay pending orders method called', [
            'user_id' => Auth::id(),
        ]);

        $orders = Order::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->where('payment_status', 'pending')
            ->with('items')
            ->get();

        if ($orders->isEmpty()) {
            Log::warning('No pending orders found for payment', [
                'user_id' => Auth::id()
            ]);
            return redirect()->route('user.dashboard')->with('error', 'No pending orders to pay.');
        }

        $total = $orders->sum('total');
        if ($total <= 0) {
            Log::error('Invalid total for pending orders', [
                'user_id' => Auth::id(),
                'total' => $total
            ]);
            return redirect()->route('user.dashboard')->with('error', 'Invalid total for pending orders.');
        }

        DB::beginTransaction();
        try {
            // Create a consolidated order
            $latestOrder = $orders->sortByDesc('created_at')->first();
            $consolidatedOrder = Order::create([
                'user_id' => Auth::id(),
                'total' => $total,
                'status' => 'pending',
                'address' => $latestOrder->address ?? Auth::user()->address ?? 'N/A',
                'payment_method' => 'online',
                'payment_status' => 'pending',
                'notes' => 'Consolidated payment for pending orders: ' . $orders->pluck('id')->implode(','),
                'transaction_id' => 'SSLCZ_' . uniqid(),
            ]);

            Log::info('Consolidated order created', [
                'order_id' => $consolidatedOrder->id,
                'transaction_id' => $consolidatedOrder->transaction_id,
                'total' => $total,
                'original_order_ids' => $orders->pluck('id')->toArray()
            ]);

            // Copy all items from pending orders to the consolidated order
            foreach ($orders as $order) {
                foreach ($order->items as $item) {
                    OrderItem::create([
                        'order_id' => $consolidatedOrder->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                    ]);
                }
                // Mark original order as linked to consolidated order
                $order->update([
                    'notes' => ($order->notes ? $order->notes . ' | ' : '') . 'Consolidated into order #' . $consolidatedOrder->id,
                ]);
            }

            // Prepare SSLCommerz payment data
            $post_data = [
                'store_id' => config('sslcommerz.apiCredentials.store_id'),
                'store_passwd' => config('sslcommerz.apiCredentials.store_password'),
                'total_amount' => $total,
                'currency' => 'BDT',
                'tran_id' => $consolidatedOrder->transaction_id,
                'success_url' => config('sslcommerz.success_url'),
                'fail_url' => config('sslcommerz.failed_url'),
                'cancel_url' => config('sslcommerz.cancel_url'),
                'ipn_url' => config('sslcommerz.ipn_url'),
                'cus_name' => Auth::user()->name,
                'cus_email' => Auth::user()->email,
                'cus_add1' => $consolidatedOrder->address,
                'cus_add2' => '',
                'cus_city' => 'Dhaka',
                'cus_state' => 'Dhaka',
                'cus_postcode' => '1000',
                'cus_country' => 'Bangladesh',
                'cus_phone' => Auth::user()->phone ?? '01700000000',
                'cus_fax' => '',
                'ship_name' => Auth::user()->name,
                'ship_add1' => $consolidatedOrder->address,
                'ship_add2' => '',
                'ship_city' => 'Dhaka',
                'ship_state' => 'Dhaka',
                'ship_postcode' => '1000',
                'ship_country' => 'Bangladesh',
                'shipping_method' => 'NO',
                'product_name' => 'Products from Styler BD',
                'product_category' => 'Clothing',
                'product_profile' => 'general',
                'value_a' => 'consolidated_order_id:' . $consolidatedOrder->id . ',original_order_ids:' . $orders->pluck('id')->implode(','),
            ];

            Log::info('SSLCommerz post_data for payPendingOrders', $post_data);

            $sslc = new SslCommerzNotification();
            $payment_options = $sslc->makePayment($post_data, 'hosted');

            Log::info('SSLCommerz response for payPendingOrders', ['response' => $payment_options]);

            if (!is_array($payment_options) || !isset($payment_options['GatewayPageURL'])) {
                throw new \Exception('Payment initiation failed: ' . json_encode($payment_options));
            }

            // Store consolidated order ID in session
            session(['pending_order_id' => $consolidatedOrder->id]);
            Log::info('Session set in payPendingOrders', ['pending_order_id' => session('pending_order_id')]);

            DB::commit();
            return redirect($payment_options['GatewayPageURL']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Consolidated payment initiation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('user.dashboard')->with('error', 'Failed to initiate payment: ' . $e->getMessage());
        }
    }

    public function paymentSuccess(Request $request)
    {
        Log::info('Payment success callback received', $request->all());
        Log::info('Session data in paymentSuccess', ['pending_order_id' => session('pending_order_id')]);

        $tran_id = $request->input('tran_id');
        if (!$tran_id) {
            Log::error('No transaction ID in success callback', $request->all());
            return redirect()->route('user.dashboard')->with('error', 'Invalid transaction ID.');
        }

        $order = Order::where('transaction_id', $tran_id)->first();
        if (!$order) {
            Log::error('Order not found for transaction ID', ['tran_id' => $tran_id]);
            return redirect()->route('user.dashboard')->with('error', 'Order not found.');
        }

        $sslc = new SslCommerzNotification();
        $validation = $sslc->orderValidate($request->all(), $tran_id, $order->total, 'BDT');
        Log::info('Payment validation result', [
            'tran_id' => $tran_id,
            'validation' => $validation,
            'request_data' => $request->all(),
            'order_total' => $order->total,
        ]);

        if ($validation) {
            try {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                    'payment_val_id' => $request->input('val_id'),
                    'payment_card_type' => $request->input('card_type'),
                    'payment_card_no' => $request->input('card_no'),
                    'updated_at' => now(),
                ]);
                Log::info('Order updated successfully', [
                    'order_id' => $order->id,
                    'payment_status' => 'paid',
                    'status' => 'processing',
                    'payment_val_id' => $request->input('val_id'),
                    'payment_card_type' => $request->input('card_type'),
                    'payment_card_no' => $request->input('card_no'),
                ]);
                session()->forget('pending_order_id');
                return redirect()->route('user.dashboard')->with('success', 'Payment successful! Order is being processed.');
            } catch (\Exception $e) {
                Log::error('Failed to update order in paymentSuccess', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
                return redirect()->route('user.dashboard')->with('error', 'Payment processed but order update failed.');
            }
        } else {
            try {
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'failed',
                    'updated_at' => now(),
                ]);
                Log::error('Payment validation failed', [
                    'tran_id' => $tran_id,
                    'request' => $request->all(),
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to update order on validation failure', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
            return redirect()->route('user.dashboard')->with('error', 'Payment validation failed.');
        }
    }

    public function paymentFail(Request $request)
    {
        Log::info('Payment fail callback received', $request->all());

        $tran_id = $request->input('tran_id');
        $order = Order::where('transaction_id', $tran_id)->first();
        if ($order) {
            try {
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'failed',
                    'updated_at' => now(),
                ]);
                Log::info('Order updated on failure', ['order_id' => $order->id]);
            } catch (\Exception $e) {
                Log::error('Failed to update order in paymentFail', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        return redirect()->route('user.dashboard')->with('error', 'Payment failed.');
    }

    public function paymentCancel(Request $request)
    {
        Log::info('Payment cancel callback received', $request->all());

        $tran_id = $request->input('tran_id');
        $order = Order::where('transaction_id', $tran_id)->first();
        if ($order) {
            try {
                $order->update([
                    'payment_status' => 'cancelled',
                    'status' => 'cancelled',
                    'updated_at' => now(),
                ]);
                Log::info('Order updated on cancellation', ['order_id' => $order->id]);
            } catch (\Exception $e) {
                Log::error('Failed to update order in paymentCancel', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        return redirect()->route('user.dashboard')->with('error', 'Payment cancelled.');
    }

    public function paymentIpn(Request $request)
    {
        Log::info('IPN callback received', $request->all());
        $tran_id = $request->input('tran_id');
        $status = $request->input('status');

        if (!$tran_id || !$status) {
            Log::error('Invalid IPN data', ['request' => $request->all()]);
            return response()->json(['status' => 'error'], 400);
        }

        $order = Order::where('transaction_id', $tran_id)->first();
        if ($order && $status === 'VALID') {
            $sslc = new SslCommerzNotification();
            $validation = $sslc->orderValidate($request->all(), $tran_id, $order->total, 'BDT');
            Log::info('IPN validation result', [
                'tran_id' => $tran_id,
                'validation' => $validation,
                'request_data' => $request->all(),
                'order_total' => $order->total,
            ]);
            if ($validation) {
                try {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'processing',
                        'payment_val_id' => $request->input('val_id'),
                        'payment_card_type' => $request->input('card_type'),
                        'payment_card_no' => $request->input('card_no'),
                        'updated_at' => now(),
                    ]);
                    Log::info('Order updated via IPN', [
                        'order_id' => $order->id,
                        'payment_status' => 'paid',
                        'status' => 'processing',
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to update order in IPN', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } else {
            Log::error('Invalid IPN callback', ['tran_id' => $tran_id, 'status' => $status]);
        }
        return response()->json(['status' => 'received']);
    }

      public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|in:pending,confirmed,shipped,delivered',
        ]);

        $order = Order::find($validated['order_id']);
        if (!$order) {
            Log::warning('Attempted to update invalid order status', [
                'order_id' => $validated['order_id'],
                'user_id' => Auth::id()
            ]);
            return response()->json(['error' => 'Order not found'], 404);
        }

        $order->status = $validated['status'];
        $order->save();

        Log::info('Order status updated', [
            'user_id' => Auth::id(),
            'order_id' => $validated['order_id'],
            'new_status' => $validated['status']
        ]);

        return response()->json(['success' => true, 'message' => 'Order status updated successfully']);
    }

    public function payViaAjax(Request $request)
    {
        // Implement if using popup payment
        Log::info('Pay via AJAX called', $request->all());
        return response()->json(['status' => 'not_implemented']);
    }

    
}