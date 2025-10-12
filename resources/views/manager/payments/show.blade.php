@extends('layouts.admin')

@section('title', 'Payment Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('manager.payments.index') }}">Payment Tracking</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $payment->payment_reference }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">Payment Details</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('manager.payments.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Payments
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Payment Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Information</h6>
                    @php
                        $statusColors = [
                            'completed' => 'success',
                            'pending' => 'warning',
                            'processing' => 'info',
                            'failed' => 'danger',
                            'refunded' => 'danger',
                            'partially_refunded' => 'warning'
                        ];
                    @endphp
                    <span class="badge badge-{{ $statusColors[$payment->status] ?? 'secondary' }} badge-lg">
                        {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Payment Reference</label>
                                <div class="form-control-plaintext">
                                    <code>{{ $payment->payment_reference }}</code>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Payment Date</label>
                                <div class="form-control-plaintext">
                                    {{ $payment->payment_date ? $payment->payment_date->format('M d, Y h:i A') : 'Not processed yet' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Amount</label>
                                <div class="form-control-plaintext">
                                    <span class="h4 text-success">{{ $payment->formatted_amount }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Payment Method</label>
                                <div class="form-control-plaintext">
                                    <span class="badge badge-light badge-lg">{{ $payment->payment_method_display }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($payment->transaction_id)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">Transaction ID</label>
                                    <div class="form-control-plaintext">
                                        <code>{{ $payment->transaction_id }}</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($payment->refund_amount > 0)
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle mr-2"></i>Refund Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Refund Amount:</strong> {{ $payment->formatted_refund_amount }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Refunded Date:</strong> {{ $payment->refunded_at ? $payment->refunded_at->format('M d, Y') : 'N/A' }}
                                </div>
                            </div>
                            @if($payment->refundedBy)
                                <div class="mt-2">
                                    <strong>Processed by:</strong> {{ $payment->refundedBy->name }}
                                </div>
                            @endif
                            @if($payment->refund_reason)
                                <div class="mt-2">
                                    <strong>Reason:</strong> {{ $payment->refund_reason }}
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($payment->notes)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">Notes</label>
                                    <div class="form-control-plaintext bg-light p-3 rounded">
                                        {!! nl2br(e($payment->notes)) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Related Booking Information -->
            @if($payment->booking)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Related Booking</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Booking Reference</label>
                                    <div class="form-control-plaintext">
                                        <a href="{{ route('manager.bookings.show', $payment->booking) }}" class="text-decoration-none">
                                            {{ $payment->booking->booking_reference }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Room</label>
                                    <div class="form-control-plaintext">
                                        @if($payment->booking->room)
                                            {{ $payment->booking->room->name }} - Room {{ $payment->booking->room->room_number }}
                                        @else
                                            Not assigned yet
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Check-in Date</label>
                                    <div class="form-control-plaintext">
                                        {{ \Carbon\Carbon::parse($payment->booking->check_in_date)->format('M d, Y') }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Check-out Date</label>
                                    <div class="form-control-plaintext">
                                        {{ \Carbon\Carbon::parse($payment->booking->check_out_date)->format('M d, Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Booking Total</label>
                                    <div class="form-control-plaintext">
                                        ₱{{ number_format($payment->booking->total_amount, 2) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Booking Status</label>
                                    <div class="form-control-plaintext">
                                        <span class="badge badge-{{ $payment->booking->status === 'confirmed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($payment->booking->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Related Service Request -->
            @if($payment->serviceRequest)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Related Service Request</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Service Type</label>
                                    <div class="form-control-plaintext">{{ $payment->serviceRequest->service_type }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Status</label>
                                    <div class="form-control-plaintext">
                                        <span class="badge badge-info">{{ ucfirst($payment->serviceRequest->status) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($payment->serviceRequest->description)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Description</label>
                                        <div class="form-control-plaintext bg-light p-3 rounded">
                                            {{ $payment->serviceRequest->description }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Related Payments -->
            @if($related_payments->count() > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Related Payments</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($related_payments as $relatedPayment)
                                        <tr>
                                            <td>
                                                <a href="{{ route('manager.payments.show', $relatedPayment) }}">
                                                    {{ $relatedPayment->payment_reference }}
                                                </a>
                                            </td>
                                            <td>{{ $relatedPayment->formatted_amount }}</td>
                                            <td>
                                                <span class="badge badge-{{ $statusColors[$relatedPayment->status] ?? 'secondary' }} badge-sm">
                                                    {{ ucfirst($relatedPayment->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $relatedPayment->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Customer Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Customer Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="icon-circle bg-primary mx-auto mb-3" style="width: 60px; height: 60px; line-height: 60px;">
                            <i class="fas fa-user fa-2x text-white"></i>
                        </div>
                        <h5 class="mb-0">{{ $payment->user->name }}</h5>
                        <p class="text-muted">{{ $payment->user->email }}</p>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Customer Role</label>
                        <div class="form-control-plaintext">
                            <span class="badge badge-info">{{ ucfirst($payment->user->role) }}</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Member Since</label>
                        <div class="form-control-plaintext">
                            {{ $payment->user->created_at->format('M d, Y') }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Total Payments</label>
                        <div class="form-control-plaintext">
                            {{ $payment->user->payments->count() }} payments
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Status Actions -->
            @if(in_array($payment->status, ['pending', 'processing']))
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Update Payment Status</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('manager.payments.status', $payment) }}">
                            @csrf
                            @method('PATCH')
                            
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="">Select Status</option>
                                    <option value="processing" {{ $payment->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="completed">Completed</option>
                                    @if(auth()->user()->role === 'admin')
                                        <option value="failed">Failed</option>
                                    @endif
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="transaction_id">Transaction ID (Optional)</label>
                                <input type="text" name="transaction_id" id="transaction_id" class="form-control" 
                                       value="{{ $payment->transaction_id }}"
                                       placeholder="Enter transaction ID from payment gateway">
                            </div>

                            <div class="form-group">
                                <label for="notes">Notes (Optional)</label>
                                <textarea name="notes" id="notes" class="form-control" rows="3" 
                                          placeholder="Add any additional notes about this status update">{{ $payment->notes }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-save mr-2"></i> Update Status
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Payment Timeline -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Payment Created</h6>
                                <p class="timeline-text">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                                <p class="timeline-text small text-muted">By: {{ $payment->user->name }}</p>
                            </div>
                        </div>

                        @if($payment->payment_date)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Payment Processed</h6>
                                    <p class="timeline-text">{{ $payment->payment_date->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>
                        @endif

                        @if($payment->refunded_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Refund Processed</h6>
                                    <p class="timeline-text">{{ $payment->refunded_at->format('M d, Y h:i A') }}</p>
                                    @if($payment->refundedBy)
                                        <p class="timeline-text small text-muted">By: {{ $payment->refundedBy->name }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 3rem;
}

.timeline::before {
    content: '';
    position: absolute;
    top: 0;
    left: 1rem;
    height: 100%;
    width: 2px;
    background-color: #e3e6f0;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    top: 0;
    left: -2.5rem;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 3px #e3e6f0;
}

.timeline-content {
    background: #f8f9fc;
    padding: 1rem;
    border-radius: 0.375rem;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.timeline-title {
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
}

.timeline-text {
    margin: 0;
    font-size: 0.75rem;
    color: #858796;
}

.icon-circle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 100%;
}

.badge-lg {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
}
</style>
@endsection
