@extends('layouts.staff')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Order Statistics</h2>
                <a href="{{ route('staff.orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Today's Orders</h6>
                    <h2 class="text-primary">{{ $stats['today']['orders'] }}</h2>
                    <p class="mb-0">Revenue: ₱{{ number_format($stats['today']['revenue'], 2) }}</p>
                    <p class="mb-0 text-success">Completed: {{ $stats['today']['completed'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">This Week</h6>
                    <h2 class="text-info">{{ $stats['week']['orders'] }}</h2>
                    <p class="mb-0">Revenue: ₱{{ number_format($stats['week']['revenue'], 2) }}</p>
                    <p class="mb-0 text-success">Completed: {{ $stats['week']['completed'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">This Month</h6>
                    <h2 class="text-warning">{{ $stats['month']['orders'] }}</h2>
                    <p class="mb-0">Revenue: ₱{{ number_format($stats['month']['revenue'], 2) }}</p>
                    <p class="mb-0 text-success">Completed: {{ $stats['month']['completed'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">All Time</h6>
                    <h2 class="text-success">{{ $stats['all_time']['orders'] }}</h2>
                    <p class="mb-0">Revenue: ₱{{ number_format($stats['all_time']['revenue'], 2) }}</p>
                    <p class="mb-0 text-success">Completed: {{ $stats['all_time']['completed'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Popular Items -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Top 10 Popular Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th class="text-center">Total Orders</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($popularItems as $item)
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $item->total_quantity }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">No data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Orders</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Status</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('staff.orders.show', $order) }}">
                                                {{ $order->order_number }}
                                            </a>
                                        </td>
                                        <td>{{ $order->customer_name }}</td>
                                        <td>{{ $order->orderItems->count() }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'preparing' => 'info',
                                                    'ready' => 'primary',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                                $color = $statusColors[$order->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end">₱{{ number_format($order->total_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No orders yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
