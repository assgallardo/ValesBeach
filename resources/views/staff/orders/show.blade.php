@extends('layouts.staff')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Order Details - {{ $foodOrder->order_number }}</h2>
                <a href="{{ route('staff.orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Order Details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Order Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Order Number:</strong><br>
                            {{ $foodOrder->order_number }}
                        </div>
                        <div class="col-md-6">
                            <strong>Order Date:</strong><br>
                            {{ $foodOrder->created_at->format('M d, Y h:i A') }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Customer Name:</strong><br>
                            {{ $foodOrder->customer_name }}
                        </div>
                        <div class="col-md-6">
                            <strong>Customer Email:</strong><br>
                            {{ $foodOrder->customer_email }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Phone:</strong><br>
                            {{ $foodOrder->customer_phone ?? 'N/A' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong><br>
                            @php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'preparing' => 'info',
                                    'ready' => 'primary',
                                    'completed' => 'success',
                                    'cancelled' => 'danger'
                                ];
                                $color = $statusColors[$foodOrder->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }} fs-6">
                                {{ ucfirst($foodOrder->status) }}
                            </span>
                        </div>
                    </div>

                    @if($foodOrder->delivery_address)
                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>Delivery Address:</strong><br>
                                {{ $foodOrder->delivery_address }}
                            </div>
                        </div>
                    @endif

                    @if($foodOrder->notes)
                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>Customer Notes:</strong><br>
                                <div class="alert alert-info">{{ $foodOrder->notes }}</div>
                            </div>
                        </div>
                    @endif

                    @if($foodOrder->staff_notes)
                        <div class="row">
                            <div class="col-12">
                                <strong>Staff Notes:</strong><br>
                                <div class="alert alert-secondary">{{ $foodOrder->staff_notes }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Items -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($foodOrder->orderItems as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->menuItem->name ?? 'N/A' }}</strong>
                                            @if($item->special_instructions)
                                                <br>
                                                <small class="text-muted">Note: {{ $item->special_instructions }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">₱{{ number_format($item->price, 2) }}</td>
                                        <td class="text-end">₱{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th class="text-end">₱{{ number_format($foodOrder->total_amount, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Status -->
        <div class="col-md-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header">
                    <h5 class="mb-0">Update Order Status</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('staff.orders.update-status', $foodOrder) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="status" class="form-label">New Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending" {{ $foodOrder->status === 'pending' ? 'selected' : '' }}>
                                    Pending
                                </option>
                                <option value="preparing" {{ $foodOrder->status === 'preparing' ? 'selected' : '' }}>
                                    Preparing
                                </option>
                                <option value="ready" {{ $foodOrder->status === 'ready' ? 'selected' : '' }}>
                                    Ready for Pickup
                                </option>
                                <option value="completed" {{ $foodOrder->status === 'completed' ? 'selected' : '' }}>
                                    Completed
                                </option>
                                <option value="cancelled" {{ $foodOrder->status === 'cancelled' ? 'selected' : '' }}>
                                    Cancelled
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Staff Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                      placeholder="Add notes about this order...">{{ old('notes', $foodOrder->staff_notes) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Update Status
                        </button>
                    </form>
                </div>

                <!-- Order Timeline -->
                <div class="card-footer">
                    <h6 class="mb-3">Order Timeline</h6>
                    <div class="timeline">
                        <div class="mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Created:</strong><br>
                            <small>{{ $foodOrder->created_at->format('M d, Y h:i A') }}</small>
                        </div>
                        @if($foodOrder->completed_at)
                            <div>
                                <i class="fas fa-check-circle text-success"></i>
                                <strong>Completed:</strong><br>
                                <small>{{ $foodOrder->completed_at->format('M d, Y h:i A') }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
