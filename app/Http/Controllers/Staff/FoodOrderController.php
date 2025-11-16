<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\FoodOrder;
use Illuminate\Http\Request;

class FoodOrderController extends Controller
{
    /**
     * Display all food orders for staff/manager.
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'active');
        
        $query = FoodOrder::with(['user', 'orderItems.menuItem']);

        // Filter by tab (active or completed)
        if ($tab === 'completed') {
            $query->where('status', 'completed');
        } else {
            // Active orders exclude cancelled and completed
            $query->whereNotIn('status', ['cancelled', 'completed']);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by order number or customer name
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_email', 'like', '%' . $request->search . '%');
            });
        }

        $orders = $query->latest()->paginate(20);

        // Get counts for tabs
        $activeCount = FoodOrder::whereNotIn('status', ['cancelled', 'completed'])->count();
        $completedCount = FoodOrder::where('status', 'completed')->count();

        // Get statistics
        $stats = [
            'total' => FoodOrder::count(),
            'pending' => FoodOrder::where('status', 'pending')->count(),
            'preparing' => FoodOrder::where('status', 'preparing')->count(),
            'ready' => FoodOrder::where('status', 'ready')->count(),
            'completed' => FoodOrder::where('status', 'completed')->count(),
            'cancelled' => FoodOrder::where('status', 'cancelled')->count(),
            'today_orders' => FoodOrder::whereDate('created_at', today())->count(),
            'today_revenue' => FoodOrder::whereDate('created_at', today())
                ->whereNotIn('status', ['cancelled'])
                ->sum('total_amount'),
        ];

        return view('staff.orders.index', compact('orders', 'stats', 'tab', 'activeCount', 'completedCount'));
    }

    /**
     * Display the specified food order.
     */
    public function show(FoodOrder $foodOrder)
    {
        $foodOrder->load(['user', 'orderItems.menuItem']);
        return view('staff.orders.show', compact('foodOrder'));
    }

    /**
     * Update the status of the specified food order.
     */
    public function updateStatus(Request $request, FoodOrder $foodOrder)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $foodOrder->update([
            'status' => $validated['status'],
            'staff_notes' => $validated['notes'] ?? $foodOrder->staff_notes,
        ]);

        // If order is completed, update completed_at timestamp
        if ($validated['status'] === 'completed') {
            $foodOrder->update(['completed_at' => now()]);
        }
        
        // If order is preparing, update prepared_at timestamp
        if ($validated['status'] === 'preparing' && !$foodOrder->prepared_at) {
            $foodOrder->update(['prepared_at' => now()]);
        }
        
        // If order is ready, update ready timestamp
        if ($validated['status'] === 'ready') {
            $foodOrder->update(['prepared_at' => $foodOrder->prepared_at ?? now()]);
        }
        
        // If order is delivered/completed, update delivered_at timestamp
        if ($validated['status'] === 'completed' && !$foodOrder->delivered_at) {
            $foodOrder->update(['delivered_at' => now()]);
        }

        // Return JSON for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully!',
                'status' => $validated['status']
            ]);
        }

        return redirect()->back()->with('success', 'Order status updated successfully!');
    }

    /**
     * Display order statistics and analytics.
     */
    public function statistics()
    {
        $today = today();
        $thisWeek = now()->startOfWeek();
        $thisMonth = now()->startOfMonth();

        $stats = [
            // Today's stats
            'today' => [
                'orders' => FoodOrder::whereDate('created_at', $today)->count(),
                'revenue' => FoodOrder::whereDate('created_at', $today)
                    ->whereNotIn('status', ['cancelled'])
                    ->sum('total_amount'),
                'completed' => FoodOrder::whereDate('created_at', $today)
                    ->where('status', 'completed')
                    ->count(),
            ],
            // This week's stats
            'week' => [
                'orders' => FoodOrder::where('created_at', '>=', $thisWeek)->count(),
                'revenue' => FoodOrder::where('created_at', '>=', $thisWeek)
                    ->whereNotIn('status', ['cancelled'])
                    ->sum('total_amount'),
                'completed' => FoodOrder::where('created_at', '>=', $thisWeek)
                    ->where('status', 'completed')
                    ->count(),
            ],
            // This month's stats
            'month' => [
                'orders' => FoodOrder::where('created_at', '>=', $thisMonth)->count(),
                'revenue' => FoodOrder::where('created_at', '>=', $thisMonth)
                    ->whereNotIn('status', ['cancelled'])
                    ->sum('total_amount'),
                'completed' => FoodOrder::where('created_at', '>=', $thisMonth)
                    ->where('status', 'completed')
                    ->count(),
            ],
            // All time stats
            'all_time' => [
                'orders' => FoodOrder::count(),
                'revenue' => FoodOrder::whereNotIn('status', ['cancelled'])->sum('total_amount'),
                'completed' => FoodOrder::where('status', 'completed')->count(),
            ],
        ];

        // Get popular items
        $popularItems = \DB::table('order_items')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->join('food_orders', 'order_items.food_order_id', '=', 'food_orders.id')
            ->whereNotIn('food_orders.status', ['cancelled'])
            ->select('menu_items.name', \DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        // Get recent orders
        $recentOrders = FoodOrder::with(['user', 'orderItems'])
            ->latest()
            ->limit(10)
            ->get();

        return view('staff.orders.statistics', compact('stats', 'popularItems', 'recentOrders'));
    }

    /**
     * Delete a cancelled food order permanently.
     */
    public function destroy(FoodOrder $foodOrder)
    {
        // Only allow deletion of cancelled orders
        if ($foodOrder->status !== 'cancelled') {
            return redirect()->back()->with('error', 'Only cancelled orders can be deleted.');
        }

        try {
            // Delete the order (cascade will handle order_items and payments)
            $orderNumber = $foodOrder->order_number;
            $foodOrder->delete();

            return redirect()->route('staff.orders.index')->with('success', "Order {$orderNumber} has been permanently deleted.");
        } catch (\Exception $e) {
            \Log::error('Failed to delete food order: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete order. Please try again.');
        }
    }
}
