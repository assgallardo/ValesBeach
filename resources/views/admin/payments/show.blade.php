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
                        <a href="{{ route('admin.payments.index') }}">Payment Management</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $payment->payment_reference }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">Payment Details</h1>
        </div>
        <div class="d-flex gap-2">
            @if($payment->canBeRefunded())
                <a href="{{ route('admin.payments.refund.form', $payment) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-undo"></i> Process Refund
                </a>
            @endif
            <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-sm">
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
                            'failed' => 'danger',
                            'refunded' => 'danger',
                            'partially_refunded' => 'warning',
                            'processing' => 'info'
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
                                <div class="form-control-plaintext">{{ $payment->payment_reference }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Payment Date</label>
                                <div class="form-control-plaintext">
                                    {{ $payment->payment_date ? $payment->payment_date->format('M d, Y h:i A') : 'Not set' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Amount</label>
                                <div class="form-control-plaintext">
                                    <span class="h5 text-success">{{ $payment->formatted_amount }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Payment Method</label>
                                <div class="form-control-plaintext">
                                    <span class="badge badge-light">{{ $payment->payment_method_display }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($payment->refund_amount > 0)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Refund Amount</label>
                                    <div class="form-control-plaintext">
                                        <span class="h6 text-danger">{{ $payment->formatted_refund_amount }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Refunded Date</label>
                                    <div class="form-control-plaintext">
                                        {{ $payment->refunded_at ? $payment->refunded_at->format('M d, Y h:i A') : 'Not set' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($payment->refundedBy)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Refunded By</label>
                                        <div class="form-control-plaintext">{{ $payment->refundedBy->name }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($payment->refund_reason)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Refund Reason</label>
                                        <div class="form-control-plaintext bg-light p-3 rounded">
                                            {{ $payment->refund_reason }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

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

            <!-- Related Information -->
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
                                        <a href="{{ route('admin.bookings.show', $payment->booking) }}">
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
                                            {{ $payment->booking->room->name }} ({{ $payment->booking->room->room_number }})
                                        @else
                                            Not assigned
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
                                    <label class="font-weight-bold">Total Amount</label>
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
                    </div>
                </div>
            @endif
        </div>

        <!-- User Information & Actions -->
        <div class="col-lg-4">
            <!-- User Information -->
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
                        <label class="font-weight-bold">User Role</label>
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
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    @if($payment->canBeRefunded())
                        <a href="{{ route('admin.payments.refund.form', $payment) }}" 
                           class="btn btn-warning btn-block mb-2">
                            <i class="fas fa-undo mr-2"></i> Process Refund
                        </a>
                    @endif

                    @if($payment->status === 'pending')
                        <form method="POST" action="{{ route('admin.payments.status', $payment) }}" class="mb-2">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-success btn-block"
                                    onclick="return confirm('Mark this payment as completed?')">
                                <i class="fas fa-check mr-2"></i> Mark Completed
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.payments.status', $payment) }}" class="mb-2">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="failed">
                            <button type="submit" class="btn btn-danger btn-block"
                                    onclick="return confirm('Mark this payment as failed?')">
                                <i class="fas fa-times mr-2"></i> Mark Failed
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Payments
                    </a>
                </div>
            </div>

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
