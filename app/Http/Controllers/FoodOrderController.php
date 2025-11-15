<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\FoodOrder;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FoodOrderController extends Controller
{
    public function __construct()
    {
        // Authentication will be handled via routes
    }

    /**
     * Display the menu with categories and items
     */
    public function menu()
    {
        $categories = MenuCategory::active()
            ->ordered()
            ->with(['menuItems' => function ($query) {
                $query->available()->orderBy('name');
            }])
            ->get();

        $featuredItems = MenuItem::featured()->available()->limit(6)->get();

        return view('food-orders.menu', compact('categories', 'featuredItems'));
    }

    /**
     * Add item to cart (session-based cart)
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1|max:20',
            'special_instructions' => 'nullable|string|max:500'
        ]);

        $menuItem = MenuItem::findOrFail($request->menu_item_id);
        
        if (!$menuItem->is_available) {
            return response()->json(['error' => 'This item is currently unavailable'], 400);
        }

        $cart = session('food_cart', []);
        $cartKey = $request->menu_item_id . '_' . hash('md5', $request->special_instructions ?? '');

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $request->quantity;
        } else {
            $cart[$cartKey] = [
                'menu_item_id' => $menuItem->id,
                'name' => $menuItem->name,
                'price' => $menuItem->price,
                'quantity' => $request->quantity,
                'special_instructions' => $request->special_instructions,
                'total' => $menuItem->price * $request->quantity
            ];
        }

        $cart[$cartKey]['total'] = $cart[$cartKey]['price'] * $cart[$cartKey]['quantity'];

        session(['food_cart' => $cart]);

        $cartCount = array_sum(array_column($cart, 'quantity'));
        $cartTotal = array_sum(array_column($cart, 'total'));

        return response()->json([
            'message' => 'Item added to cart',
            'cart_count' => $cartCount,
            'cart_total' => number_format($cartTotal, 2)
        ]);
    }

    /**
     * Display the cart
     */
    public function cart()
    {
        $cart = session('food_cart', []);
        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $key => $item) {
            $menuItem = MenuItem::find($item['menu_item_id']);
            if ($menuItem && $menuItem->is_available) {
                $cartItems[$key] = [
                    'menu_item' => $menuItem,
                    'quantity' => $item['quantity'],
                    'special_instructions' => $item['special_instructions'],
                    'total' => $menuItem->price * $item['quantity']
                ];
                $subtotal += $cartItems[$key]['total'];
            }
        }

        return view('food-orders.cart', compact('cartItems', 'subtotal'));
    }

    /**
     * Update cart item quantity
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'cart_key' => 'required|string',
            'quantity' => 'required|integer|min:0|max:20'
        ]);

        $cart = session('food_cart', []);

        if ($request->quantity == 0) {
            unset($cart[$request->cart_key]);
        } else {
            if (isset($cart[$request->cart_key])) {
                $cart[$request->cart_key]['quantity'] = $request->quantity;
                $cart[$request->cart_key]['total'] = $cart[$request->cart_key]['price'] * $request->quantity;
            }
        }

        session(['food_cart' => $cart]);

        $cartCount = array_sum(array_column($cart, 'quantity'));
        $cartTotal = array_sum(array_column($cart, 'total'));

        return response()->json([
            'cart_count' => $cartCount,
            'cart_total' => number_format($cartTotal, 2)
        ]);
    }

    /**
     * Show checkout form
     */
    public function checkout()
    {
        $cart = session('food_cart', []);

        if (empty($cart)) {
            return redirect()->route('guest.food-orders.menu')->with('error', 'Your cart is empty');
        }

        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $key => $item) {
            $menuItem = MenuItem::find($item['menu_item_id']);
            if ($menuItem && $menuItem->is_available) {
                $cartItems[$key] = [
                    'menu_item' => $menuItem,
                    'quantity' => $item['quantity'],
                    'special_instructions' => $item['special_instructions'],
                    'total' => $menuItem->price * $item['quantity']
                ];
                $subtotal += $cartItems[$key]['total'];
            }
        }

        // Get user's current booking if any
        $currentBooking = Auth::user()->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->first();

        return view('food-orders.checkout', compact('cartItems', 'subtotal', 'currentBooking'));
    }

    /**
     * Process the order
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'delivery_type' => 'required|in:room_service,pickup,dining_room',
            'delivery_location' => 'nullable|string|max:50',
            'special_instructions' => 'nullable|string|max:1000',
            'requested_delivery_time' => 'nullable|date'
        ]);

        $cart = session('food_cart', []);

        if (empty($cart)) {
            return redirect()->route('guest.food-orders.menu')->with('error', 'Your cart is empty');
        }

        // Get current booking for date validation
        $currentBooking = Auth::user()->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->first();

        // Validate requested delivery time is within booking dates
        if ($request->requested_delivery_time && $currentBooking) {
            $requestedTime = \Carbon\Carbon::parse($request->requested_delivery_time);
            $checkInDate = $currentBooking->check_in_date->startOfDay();
            $checkOutDate = $currentBooking->check_out_date->endOfDay();

            if ($requestedTime->lt($checkInDate) || $requestedTime->gt($checkOutDate)) {
                return back()->with('error', 'Delivery time must be within your booking dates (' . 
                    $currentBooking->check_in_date->format('M j') . ' - ' . 
                    $currentBooking->check_out_date->format('M j, Y') . ')');
            }

            // Also ensure it's at least 30 minutes from now
            if ($requestedTime->lt(now()->addMinutes(30))) {
                return back()->with('error', 'Delivery time must be at least 30 minutes from now');
            }
        }

        DB::beginTransaction();        try {
            // Calculate totals
            $subtotal = 0;
            $validItems = [];

            foreach ($cart as $key => $item) {
                $menuItem = MenuItem::find($item['menu_item_id']);
                if ($menuItem && $menuItem->is_available) {
                    $itemTotal = $menuItem->price * $item['quantity'];
                    $subtotal += $itemTotal;
                    $validItems[] = [
                        'menu_item' => $menuItem,
                        'quantity' => $item['quantity'],
                        'special_instructions' => $item['special_instructions'],
                        'unit_price' => $menuItem->price,
                        'total_price' => $itemTotal
                    ];
                }
            }

            if (empty($validItems)) {
                throw new \Exception('No valid items in cart');
            }

            // Calculate delivery fee
            $deliveryFee = $request->delivery_type === 'room_service' ? 5.00 : 0.00;
            $taxAmount = 0.00; // No tax for food orders
            $totalAmount = $subtotal + $deliveryFee;

            // Create food order
            $foodOrder = FoodOrder::create([
                'order_number' => FoodOrder::generateOrderNumber(),
                'user_id' => Auth::id(),
                'guest_name' => Auth::user()->name,
                'guest_email' => Auth::user()->email,
                'booking_id' => $currentBooking?->id,
                'status' => 'pending',
                'delivery_type' => $request->delivery_type,
                'delivery_location' => $request->delivery_location,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'payment_status' => 'pending',
                'special_instructions' => $request->special_instructions,
                'requested_delivery_time' => $request->requested_delivery_time
            ]);

            // Create order items
            foreach ($validItems as $item) {
                OrderItem::create([
                    'food_order_id' => $foodOrder->id,
                    'menu_item_id' => $item['menu_item']['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                    'special_instructions' => $item['special_instructions']
                ]);
            }

            // Determine payment transaction ID
            // If user has any NON-COMPLETED payments, use the same transaction
            // If all payments are completed OR no payments exist, create new transaction
            $activePayment = \App\Models\Payment::where('user_id', Auth::id())
                ->whereIn('status', ['pending', 'confirmed', 'processing', 'overdue', 'failed', 'cancelled', 'refunded'])
                ->first();
            
            if ($activePayment) {
                // Use existing active transaction
                $paymentTransactionId = $activePayment->payment_transaction_id;
            } else {
                // Create new transaction (all previous are completed or this is first payment)
                $paymentTransactionId = 'TXN-' . strtoupper(\Illuminate\Support\Str::random(12));
            }
            
            // Create payment record
            Payment::create([
                'food_order_id' => $foodOrder->id,
                'user_id' => Auth::id(),
                'payment_reference' => 'PAY-' . strtoupper(uniqid()),
                'payment_transaction_id' => $paymentTransactionId,
                'amount' => $totalAmount,
                'payment_method' => 'cash', // Default to cash, will be updated when payment is processed
                'status' => 'pending',
                'payment_date' => now(),
                'notes' => 'Food Order #' . $foodOrder->order_number . ' - Payment upon delivery'
            ]);

            DB::commit();

            // Clear cart
            session()->forget('food_cart');

            return redirect()->route('guest.food-orders.show', $foodOrder)->with('success', 'Your order has been placed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Food order creation failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return more detailed error in development
            if (config('app.debug')) {
                return back()->with('error', 'Failed to place order: ' . $e->getMessage());
            }
            
            return back()->with('error', 'Failed to place order. Please try again.');
        }
    }

    /**
     * Display order details
     */
    public function show(FoodOrder $foodOrder)
    {
        // Ensure user can only see their own orders
        if ($foodOrder->user_id !== Auth::id()) {
            abort(403);
        }

        $foodOrder->load(['orderItems.menuItem', 'user', 'booking']);

        return view('food-orders.show', compact('foodOrder'));
    }

    /**
     * Display user's order history
     */
    public function orders()
    {
        $orders = Auth::user()->foodOrders()
            ->where('status', '!=', 'cancelled')
            ->with(['orderItems.menuItem'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('food-orders.orders', compact('orders'));
    }

    /**
     * Cancel an order (only if pending)
     */
    public function cancel(FoodOrder $foodOrder)
    {
        if ($foodOrder->user_id !== Auth::id()) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        if ($foodOrder->status !== 'pending') {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['error' => 'This order cannot be cancelled'], 400);
            }
            return back()->with('error', 'This order cannot be cancelled');
        }

        $foodOrder->update(['status' => 'cancelled']);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully'
            ]);
        }

        return back()->with('success', 'Order cancelled successfully');
    }

    /**
     * Get cart count for header display
     */
    public function cartCount()
    {
        $cart = session('food_cart', []);
        $count = array_sum(array_column($cart, 'quantity'));

        return response()->json(['count' => $count]);
    }
}
