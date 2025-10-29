@extends('layouts.manager')

@section('title', 'Payment Tracking')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-credit-card text-primary mr-2"></i>
            Payment Tracking
        </h1>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-purple btn-sm" onclick="openGenerateInvoiceModal()">
                <i class="fas fa-file-invoice-dollar"></i> Generate Invoice
            </button>
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#filterModal">
                <i class="fas fa-filter"></i> Filter
            </button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="togglePaymentHistory()">
                <i class="fas fa-history"></i> History
            </button>
            <a href="{{ route('manager.payments.analytics') }}" class="btn btn-info btn-sm">
                <i class="fas fa-chart-bar"></i> Analytics
            </a>
            <a href="{{ route('manager.payments.export', request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-download"></i> Export
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₱{{ number_format($stats['total_payments'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-peso-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₱{{ number_format($stats['pending_payments'], 2) }}
                            </div>
                            <div class="text-xs text-muted">{{ $stats['pending_count'] }} payments</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Today's Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₱{{ number_format($stats['today_payments'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Completed
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['completed_count'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Failed
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['failed_count'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Total Transactions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_transactions'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Search Bar -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body py-2">
                    <form method="GET" action="{{ route('manager.payments.index') }}" id="managerSearchForm">
                        @foreach(request()->except('search') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                            </div>
                            <input type="text" 
                                   name="search" 
                                   id="managerSearchInput"
                                   class="form-control border-left-0" 
                                   placeholder="Search by payment reference, guest name, or email..."
                                   value="{{ request('search') }}">
                            @if(request('search'))
                                <div class="input-group-append">
                                    <a href="{{ route('manager.payments.index', request()->except('search')) }}" 
                                       class="btn btn-outline-secondary"
                                       title="Clear search">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            @endif
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Booking Payments (Grouped) -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users mr-2"></i>Customer Payments
                    </h6>
                    <span class="badge badge-primary badge-pill">{{ $customers->total() }} Customers</span>
                </div>
                <div class="card-body">
                    @if($customers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Guest</th>
                                        <th>Payment Types</th>
                                        <th>Total Amount</th>
                                        <th>Transactions</th>
                                        <th>Status</th>
                                        <th>Latest Payment</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                            @foreach($customers as $customer)
                                @php
                                    $bookingPayments = $customer->payments->filter(fn($p) => $p->booking_id);
                                    $servicePayments = $customer->payments->filter(fn($p) => $p->service_request_id);
                                    $foodPayments = $customer->payments->filter(fn($p) => $p->food_order_id);
                                    $totalAmount = $customer->payments->sum('amount');
                                    $latestPayment = $customer->payments->first();
                                    
                                    // Group payments by status
                                    $statusGroups = $customer->payments->groupBy('status');
                                    $pendingCount = $statusGroups->get('pending', collect())->count();
                                    $confirmedCount = $statusGroups->get('confirmed', collect())->count();
                                    $completedCount = $statusGroups->get('completed', collect())->count();
                                    $overdueCount = $statusGroups->get('overdue', collect())->count();
                                    $refundedCount = $statusGroups->get('refunded', collect())->count();
                                @endphp
                                <tr>
                                    <!-- Guest Info -->
                                    <td>
                                        <div class="font-weight-bold">{{ $customer->name }}</div>
                                        <div class="small text-muted">{{ $customer->email }}</div>
                                    </td>
                                    
                                    <!-- Payment Types -->
                                    <td>
                                        @if($bookingPayments->count() > 0)
                                            <div><i class="fas fa-bed text-primary"></i> {{ $bookingPayments->count() }} Booking{{ $bookingPayments->count() > 1 ? 's' : '' }}</div>
                                        @endif
                                        @if($servicePayments->count() > 0)
                                            <div><i class="fas fa-concierge-bell text-purple"></i> {{ $servicePayments->count() }} Service{{ $servicePayments->count() > 1 ? 's' : '' }}</div>
                                        @endif
                                        @if($foodPayments->count() > 0)
                                            <div><i class="fas fa-utensils text-warning"></i> {{ $foodPayments->count() }} Food</div>
                                        @endif
                                    </td>
                                    
                                    <!-- Total Amount -->
                                    <td>
                                        <strong class="text-success">₱{{ number_format($totalAmount, 2) }}</strong>
                                    </td>
                                    
                                    <!-- Transactions -->
                                    <td>
                                        <span class="badge badge-info">{{ $customer->payments->count() }} payment{{ $customer->payments->count() > 1 ? 's' : '' }}</span>
                                    </td>
                                    
                                    <!-- Status -->
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            @if($completedCount > 0)
                                                <span class="badge badge-success badge-sm">{{ $completedCount }} Completed</span>
                                            @endif
                                            @if($confirmedCount > 0)
                                                <span class="badge badge-primary badge-sm">{{ $confirmedCount }} Confirmed</span>
                                            @endif
                                            @if($pendingCount > 0)
                                                <span class="badge badge-warning badge-sm">{{ $pendingCount }} Pending</span>
                                            @endif
                                            @if($overdueCount > 0)
                                                <span class="badge badge-danger badge-sm">{{ $overdueCount }} Overdue</span>
                                            @endif
                                            @if($refundedCount > 0)
                                                <span class="badge badge-secondary badge-sm">{{ $refundedCount }} Refunded</span>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <!-- Latest Payment -->
                                    <td>
                                        @if($latestPayment)
                                            <div>{{ $latestPayment->created_at->format('M d, Y') }}</div>
                                            <div class="small text-muted">{{ $latestPayment->created_at->format('h:i A') }}</div>
                                        @endif
                                    </td>
                                    
                                    <!-- Action -->
                                    <td>
                                        <a href="{{ route('manager.payments.customer', $customer->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View All
                                        </a>
                                    </td>
                                </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $customers->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-users fa-3x text-gray-300"></i>
                            </div>
                            <h5 class="text-gray-600">No Customers Found</h5>
                            <p class="text-muted">There are no customers with payments matching your criteria.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-lg-4">
            @if($recent_payments->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-history mr-2"></i>Recent Activity
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($recent_payments as $payment)
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-info h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <h6 class="font-weight-bold mb-1">
                                                <i class="fas fa-concierge-bell text-info mr-1"></i>
                                                Service Payment
                                            </h6>
                                            <div class="small text-muted">{{ $payment->payment_reference }}</div>
                                        </div>
                                        @php
                                            $statusColors = [
                                                'completed' => 'success',
                                                'pending' => 'warning',
                                                'failed' => 'danger',
                                            ];
                                        @endphp
                                        <span class="badge badge-{{ $statusColors[$payment->status] ?? 'secondary' }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </div>

                                    <div class="mb-2 pb-2 border-bottom">
                                        <div class="small">
                                            <i class="fas fa-user text-muted mr-1"></i>
                                            <strong>{{ $payment->user->name }}</strong>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <div class="h5 mb-0 font-weight-bold text-success">
                                            ₱{{ number_format($payment->amount, 2) }}
                                        </div>
                                        <div class="small text-muted">
                                            <i class="fas fa-{{ $payment->payment_method === 'cash' ? 'money-bill-wave' : ($payment->payment_method === 'card' ? 'credit-card' : 'mobile-alt') }} mr-1"></i>
                                            {{ $payment->payment_method_display }}
                                        </div>
                                    </div>

                                    <div class="small text-muted mb-3">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $payment->created_at->format('M d, Y h:i A') }}
                                    </div>

                                    <a href="{{ route('manager.payments.show', $payment) }}" 
                                       class="btn btn-sm btn-outline-info btn-block">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination for Service Payments -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $servicePayments->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Recent Payment Activity -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock mr-2"></i>Recent Activity
                    </h6>
                </div>
                <div class="card-body">
                    @if($recent_payments->count() > 0)
                        <div class="list-group list-group-flush">
                        @foreach($recent_payments as $payment)
                            <div class="list-group-item px-0 py-2">
                                <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                        <div class="font-weight-bold small">₱{{ number_format($payment->amount, 2) }}</div>
                                        <div class="text-xs text-muted">{{ $payment->user->name }}</div>
                                        <div class="text-xs text-muted">
                                            <i class="fas fa-clock mr-1"></i>{{ $payment->created_at->diffForHumans() }}
                                    </div>
                                    </div>
                                    @php
                                        $statusColors = [
                                            'completed' => 'success',
                                            'pending' => 'warning',
                                            'failed' => 'danger',
                                        ];
                                    @endphp
                                    <span class="badge badge-{{ $statusColors[$payment->status] ?? 'secondary' }} badge-sm">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No recent activity</p>
                    @endif
                </div>
            </div>

            <!-- Payment Trends Chart -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line mr-2"></i>7-Day Payment Trends
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="paymentTrendsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Payments</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="GET" action="{{ route('manager.payments.index') }}">
                <div class="modal-body">
                            <div class="form-group">
                        <label>Booking Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                    
                            <div class="form-group">
                        <label>Payment Status</label>
                        <select name="payment_status" class="form-control">
                            <option value="">All Payment Statuses</option>
                            <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Fully Paid</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Date From</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>

                            <div class="form-group">
                                <label>Date To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>

                    <div class="form-group">
                        <label>Search</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Payment ref, guest name, email, room name..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <a href="{{ route('manager.payments.index') }}" class="btn btn-outline-secondary">Clear</a>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Booking Refund Modal -->
<div class="modal fade" id="bookingRefundModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">
                    <i class="fas fa-undo mr-2"></i>Process Booking Refund
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="bookingRefundForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Warning:</strong> This will refund the booking payment and update the booking status.
                    </div>
                    
                    <div class="form-group">
                        <label>Booking Reference</label>
                        <input type="text" id="refundBookingRef" class="form-control" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Total Amount Paid</label>
                        <input type="text" id="refundTotalPaid" class="form-control" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Refund Amount <span class="text-danger">*</span></label>
                        <input type="number" 
                               name="refund_amount" 
                               id="refundAmount" 
                               class="form-control" 
                               step="0.01" 
                               min="0" 
                               required>
                        <small class="form-text text-muted">Enter the amount to refund (max: <span id="maxRefundAmount"></span>)</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Refund Reason <span class="text-danger">*</span></label>
                        <textarea name="refund_reason" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="Enter reason for refund"
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-undo mr-1"></i>Process Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Manager Search functionality with debounce
let managerSearchTimeout;
const managerSearchInput = document.getElementById('managerSearchInput');
const managerSearchForm = document.getElementById('managerSearchForm');

if (managerSearchInput) {
    // Submit on Enter key
    managerSearchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            managerSearchForm.submit();
        }
    });

    // Optional: Auto-submit after user stops typing (1 second delay)
    managerSearchInput.addEventListener('input', function() {
        clearTimeout(managerSearchTimeout);
        managerSearchTimeout = setTimeout(function() {
            if (managerSearchInput.value.length >= 2 || managerSearchInput.value.length === 0) {
                managerSearchForm.submit();
            }
        }, 1000); // Wait 1 second after user stops typing
    });
}

// Payment Trends Chart
const ctx = document.getElementById('paymentTrendsChart');
if (ctx) {
    const trendsData = @json($payment_trends);
    
    new Chart(ctx, {
    type: 'line',
    data: {
            labels: trendsData.map(d => d.date),
        datasets: [{
                label: 'Daily Revenue',
                data: trendsData.map(d => d.amount),
                borderColor: 'rgb(78, 115, 223)',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.3,
                fill: true
        }]
    },
    options: {
        responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₱' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
}

function togglePaymentHistory() {
    // Implement payment history toggle functionality
    alert('Payment history feature coming soon!');
}

// Show booking refund modal
function showBookingRefundModal(bookingId, totalPaid, bookingRef) {
    const modal = $('#bookingRefundModal');
    const form = $('#bookingRefundForm');
    
    // Set form action
    form.attr('action', `/manager/bookings/${bookingId}/refund`);
    
    // Populate modal fields
    $('#refundBookingRef').val(bookingRef);
    $('#refundTotalPaid').val('₱' + parseFloat(totalPaid).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    $('#maxRefundAmount').text('₱' + parseFloat(totalPaid).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    $('#refundAmount').attr('max', totalPaid);
    $('#refundAmount').val(totalPaid); // Default to full refund
    
    // Clear previous values
    $('textarea[name="refund_reason"]').val('');
    
    // Show modal
    modal.modal('show');
}

// Handle refund form submission
$('#bookingRefundForm').on('submit', function(e) {
    e.preventDefault();
    
    const form = $(this);
    const submitBtn = form.find('button[type="submit"]');
    const refundAmount = parseFloat($('#refundAmount').val());
    const maxAmount = parseFloat($('#refundAmount').attr('max'));
    
    // Validate refund amount
    if (refundAmount <= 0) {
        alert('Refund amount must be greater than zero.');
        return;
    }
    
    if (refundAmount > maxAmount) {
        alert('Refund amount cannot exceed the total amount paid.');
        return;
    }
    
    // Confirm refund
    if (!confirm('Are you sure you want to process this refund? This action cannot be undone.')) {
        return;
    }
    
    // Disable submit button
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Processing...');
    
    // Submit form
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        success: function(response) {
            $('#bookingRefundModal').modal('hide');
            alert('Refund processed successfully!');
            location.reload();
        },
        error: function(xhr) {
            submitBtn.prop('disabled', false).html('<i class="fas fa-undo mr-1"></i>Process Refund');
            
            let errorMessage = 'An error occurred while processing the refund.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            alert(errorMessage);
        }
    });
});

// Generate Invoice Modal Functions
function openGenerateInvoiceModal() {
    $('#generateInvoiceModal').modal('show');
}

function closeGenerateInvoiceModal() {
    $('#generateInvoiceModal').modal('hide');
    $('#generateInvoiceForm')[0].reset();
    $('#generateInvoiceForm').attr('action', '');
    updateButtonState();
}

function updateFormAction() {
    const bookingId = $('#booking_id').val();
    const form = $('#generateInvoiceForm');
    
    if (bookingId) {
        form.attr('action', `/bookings/${bookingId}/invoice/generate`);
    } else {
        form.attr('action', '');
    }
    updateButtonState();
}

function updateButtonState() {
    const bookingId = $('#booking_id').val();
    const button = $('#generateButton');
    
    if (bookingId) {
        button.prop('disabled', false);
        button.removeClass('btn-secondary').addClass('btn-success');
    } else {
        button.prop('disabled', true);
        button.removeClass('btn-success').addClass('btn-secondary');
    }
}
</script>
@endpush

<!-- Generate Invoice Modal -->
<div class="modal fade" id="generateInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="generateInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-purple text-white">
                <h5 class="modal-title" id="generateInvoiceModalLabel">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>Generate Invoice
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                    </div>
            <form id="generateInvoiceForm" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Booking Selection -->
                    <div class="form-group">
                        <label for="booking_id">Select Booking</label>
                        <select 
                            name="booking_id" 
                            id="booking_id" 
                            required 
                            onchange="updateFormAction()"
                            class="form-control"
                        >
                            <option value="">Select a booking...</option>
                            @foreach(\App\Models\Booking::with('room', 'user')->whereDoesntHave('invoice')->whereHas('payments')->orderBy('created_at', 'desc')->get() as $booking)
                            <option value="{{ $booking->id }}">
                                {{ $booking->booking_reference }} - {{ $booking->room->name }} - {{ $booking->user->name }}
                                ({{ $booking->check_in->format('M d') }} - {{ $booking->check_out->format('M d, Y') }})
                            </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Only bookings with payments and without invoices are shown</small>
                    </div>
                    
                    <!-- Due Date -->
                    <div class="form-group">
                        <label for="due_date">Due Date</label>
                        <input 
                            type="date" 
                            name="due_date" 
                            id="due_date" 
                            value="{{ now()->addDays(7)->format('Y-m-d') }}"
                            min="{{ now()->format('Y-m-d') }}"
                            required 
                            class="form-control"
                        >
                </div>
                    
                    <!-- Tax Rate -->
                    <div class="form-group">
                        <label for="tax_rate">Tax Rate (%)</label>
                        <input 
                            type="number" 
                            name="tax_rate" 
                            id="tax_rate" 
                            value="0" 
                            min="0" 
                            max="100" 
                            step="0.01"
                            class="form-control"
                        >
                        <small class="form-text text-muted">Enter tax rate (e.g., 12 for 12% VAT)</small>
                    </div>
                    
                    <!-- Notes -->
                    <div class="form-group">
                        <label for="notes">Notes (Optional)</label>
                        <textarea 
                            name="notes" 
                            id="notes" 
                            rows="3" 
                            class="form-control"
                            placeholder="Additional notes for this invoice..."
                        ></textarea>
                    </div>
                        </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="generateButton" class="btn btn-secondary" disabled>
                        <i class="fas fa-file-invoice mr-2"></i>Generate Invoice
                    </button>
                    </div>
            </form>
                </div>
                </div>
            </div>
@endsection
