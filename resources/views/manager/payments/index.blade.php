@extends('layouts.admin')

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
            <a href="{{ route('manager.payments.completed') }}" class="btn btn-success btn-sm">
                <i class="fas fa-check-circle"></i> Completed
            </a>
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#filterModal">
                <i class="fas fa-filter"></i> Filter Payments
            </button>
            <button type="button" class="btn btn-purple btn-sm" onclick="togglePaymentHistory()">
                <i class="fas fa-history"></i> Payment History
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

    <div class="row">
        <!-- Payments Table -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Transactions</h6>
                </div>
                <div class="card-body">
                    @if($payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>
                                                <a href="{{ route('manager.payments.show', $payment) }}" class="text-decoration-none">
                                                    <strong>{{ $payment->payment_reference }}</strong>
                                                </a>
                                                <div class="small text-muted">
                                                    {{ $payment->payment_category }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="font-weight-bold">{{ $payment->user->name }}</div>
                                                <div class="small text-muted">{{ $payment->user->email }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-green-400">
                                                    ₱{{ number_format($payment->calculated_amount, 2) }}
                                                </div>
                                                
                                                <!-- Show breakdown for services -->
                                                @if($payment->serviceRequest && $payment->serviceRequest->service)
                                                    @php
                                                        $service = $payment->serviceRequest->service;
                                                        $quantity = $payment->serviceRequest->quantity ?? 1;
                                                        $serviceTotal = $service->price * $quantity;
                                                    @endphp
                                                    <div class="text-xs text-gray-400 mt-1">
                                                        {{ $service->name }}
                                                    </div>
                                                    <div class="text-xs text-blue-400">
                                                        ₱{{ number_format($service->price, 2) }}
                                                        @if($quantity > 1)
                                                            × {{ $quantity }}
                                                        @endif
                                                    </div>
                                                    @if($quantity > 1)
                                                        <div class="text-xs font-medium text-green-300">
                                                            Total: ₱{{ number_format($serviceTotal, 2) }}
                                                        </div>
                                                    @endif
                                                    @if($service->duration)
                                                        <div class="text-xs text-gray-500">
                                                            {{ $service->duration }} min
                                                        </div>
                                                    @endif
                                                @endif
                                                
                                                <!-- Show breakdown for bookings -->
                                                @if($payment->booking && $payment->booking->room)
                                                    @php
                                                        $checkIn = \Carbon\Carbon::parse($payment->booking->check_in_date);
                                                        $checkOut = \Carbon\Carbon::parse($payment->booking->check_out_date);
                                                        $nights = $checkIn->diffInDays($checkOut);
                                                        $roomCost = $payment->booking->room->price * $nights;
                                                    @endphp
                                                    <div class="text-xs text-gray-400 mt-1">
                                                        {{ $payment->booking->room->name }}
                                                    </div>
                                                    <div class="text-xs text-blue-400">
                                                        ₱{{ number_format($payment->booking->room->price, 2) }} × {{ $nights }} nights
                                                    </div>
                                                @endif
                                                
                                                <!-- Show refund information -->
                                                @if($payment->refund_amount > 0)
                                                    <div class="text-sm text-red-400 mt-1">
                                                        Refunded: ₱{{ number_format($payment->refund_amount, 2) }}
                                                    </div>
                                                    <div class="text-sm font-medium text-green-400">
                                                        Net: ₱{{ number_format($payment->calculated_amount - ($payment->refund_amount ?? 0), 2) }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-light">
                                                    {{ $payment->payment_method_display }}
                                                </span>
                                            </td>
                                            <td>
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
                                                <span class="badge badge-{{ $statusColors[$payment->status] ?? 'secondary' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="small">
                                                    {{ $payment->created_at->format('M d, Y') }}
                                                    <div class="text-muted">
                                                        {{ $payment->created_at->format('h:i A') }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" 
                                                            data-toggle="dropdown">
                                                        Actions
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <!-- View Payment Details -->
                                                        <a class="dropdown-item" href="{{ route('manager.payments.show', $payment) }}">
                                                            <i class="fas fa-eye mr-2"></i> View Payment Details
                                                        </a>
                                                        
                                                        <!-- View Related Record -->
                                                        @if($payment->booking)
                                                            <a class="dropdown-item" href="{{ route('manager.bookings.show', $payment->booking) }}">
                                                                <i class="fas fa-bed mr-2"></i> View Booking
                                                            </a>
                                                        @elseif($payment->serviceRequest)
                                                            <a class="dropdown-item" href="{{ route('manager.service-requests.show', $payment->serviceRequest) }}">
                                                                <i class="fas fa-concierge-bell mr-2"></i> View Service Request
                                                            </a>
                                                        @endif
                                                        
                                                        <div class="dropdown-divider"></div>
                                                        
                                                        <!-- Refund Action -->
                                                        @if($payment->canBeRefunded())
                                                            <button class="dropdown-item text-warning" 
                                                                    onclick="showRefundModal({{ $payment->id }}, {{ $payment->getRemainingRefundableAmount() }})">
                                                                <i class="fas fa-undo mr-2"></i> Process Refund
                                                            </button>
                                                        @endif
                                                        
                                                        <!-- Status Update Actions -->
                                                        @if(in_array($payment->status, ['pending', 'processing']))
                                                            <button class="dropdown-item text-success" 
                                                                    onclick="updatePaymentStatus({{ $payment->id }}, 'completed')">
                                                                <i class="fas fa-check mr-2"></i> Mark Completed
                                                            </button>
                                                            
                                                            @if(auth()->user()->role === 'admin')
                                                                <button class="dropdown-item text-danger" 
                                                                        onclick="updatePaymentStatus({{ $payment->id }}, 'failed')">
                                                                    <i class="fas fa-times mr-2"></i> Mark Failed
                                                                </button>
                                                            @endif
                                                        @endif
                                                        
                                                        <!-- Reprocess Failed Payments -->
                                                        @if($payment->status === 'failed')
                                                            <button class="dropdown-item text-info" 
                                                                    onclick="updatePaymentStatus({{ $payment->id }}, 'pending')">
                                                                <i class="fas fa-redo mr-2"></i> Reprocess Payment
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $payments->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-credit-card fa-3x text-gray-300"></i>
                            </div>
                            <h5 class="text-gray-500">No payments found</h5>
                            <p class="text-gray-400">No payment records match your current filters.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Recent Activity -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                </div>
                <div class="card-body">
                    @if($recent_payments->count() > 0)
                        @foreach($recent_payments as $payment)
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-circle bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }} mr-3">
                                    <i class="fas fa-{{ $payment->status === 'completed' ? 'check' : 'clock' }} text-white"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small font-weight-bold">
                                        {{ $payment->formatted_amount }} - {{ $payment->user->name }}
                                    </div>
                                    <div class="small text-muted">
                                        {{ $payment->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <div>
                                    <span class="badge badge-{{ $payment->status === 'completed' ? 'success' : 'warning' }} badge-sm">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">No recent activity</p>
                    @endif
                </div>
            </div>

            <!-- Payment Trends Chart -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">7-Day Payment Trends</h6>
                </div>
                <div class="card-body">
                    <canvas id="paymentTrendsChart" width="100%" height="200"></canvas>
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Method</label>
                                <select name="payment_method" class="form-control">
                                    <option value="">All Methods</option>
                                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                                    <option value="gcash" {{ request('payment_method') == 'gcash' ? 'selected' : '' }}>GCash</option>
                                    <option value="paymaya" {{ request('payment_method') == 'paymaya' ? 'selected' : '' }}>PayMaya</option>
                                    <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date From</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Search</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search by payment reference, customer name, or email" 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <a href="{{ route('manager.payments.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Refund Modal -->
<div class="modal fade" id="refundModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Process Refund</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="refundForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Refund Amount</label>
                        <input type="number" name="refund_amount" class="form-control" 
                               step="0.01" required>
                        <small class="form-text text-muted">
                            Maximum refundable: <span id="maxRefund"></span>
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label>Refund Reason</label>
                        <textarea name="refund_reason" class="form-control" rows="3" 
                                  placeholder="Please provide a reason for the refund..." required></textarea>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Warning:</strong> This action cannot be undone. Please ensure the refund amount and reason are correct.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-undo mr-2"></i>Process Refund
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Payment trends chart
const ctx = document.getElementById('paymentTrendsChart').getContext('2d');
const paymentTrendsChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode(collect($payment_trends)->pluck('date')->toArray()) !!},
        datasets: [{
            label: 'Revenue (₱)',
            data: {!! json_encode(collect($payment_trends)->pluck('amount')->toArray()) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₱' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Update payment status function
function updatePaymentStatus(paymentId, status) {
    if (confirm('Are you sure you want to update this payment status to ' + status + '?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/manager/payments/${paymentId}/status`;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PATCH';
        form.appendChild(methodInput);
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = status;
        form.appendChild(statusInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Show refund modal function
function showRefundModal(paymentId, maxAmount) {
    document.getElementById('refundForm').action = `/manager/payments/${paymentId}/refund`;
    document.getElementById('maxRefund').textContent = `₱${maxAmount.toFixed(2)}`;
    document.querySelector('input[name="refund_amount"]').max = maxAmount;
    $('#refundModal').modal('show');
}

// Close refund modal function
function closeRefundModal() {
    $('#refundModal').modal('hide');
    document.getElementById('refundForm').reset();
}

// Payment history toggle function
function togglePaymentHistory() {
    const panel = document.getElementById('paymentHistoryPanel');
    const isVisible = panel.style.display !== 'none';
    panel.style.display = isVisible ? 'none' : 'block';
    
    if (!isVisible) {
        updatePaymentHistory();
    }
}

// Update payment history function
function updatePaymentHistory() {
    const period = document.getElementById('historyPeriod').value;
    // Fetch and update payment history data via AJAX
    // ...
}

// Export payment history function
function exportPaymentHistory() {
    const period = document.getElementById('historyPeriod').value;
    // Implement export functionality
    // ...
}

// Chart type toggle function
function toggleChartType(type) {
    const chart = paymentHistoryChart;
    const revenueBtn = document.getElementById('revenueBtn');
    const countBtn = document.getElementById('countBtn');
    const methodsBtn = document.getElementById('methodsBtn');
    
    // Update button active states
    revenueBtn.classList.toggle('active', type === 'revenue');
    countBtn.classList.toggle('active', type === 'count');
    methodsBtn.classList.toggle('active', type === 'methods');
    
    // Update chart data and type
    if (type === 'revenue') {
        chart.config.type = 'line';
        // Update line chart data
        // ...
    } else if (type === 'count') {
        chart.config.type = 'bar';
        // Update bar chart data
        // ...
    } else if (type === 'methods') {
        chart.config.type = 'pie';
        // Update pie chart data
        // ...
    }
    chart.update();
}

// Initial setup for payment history chart
const paymentHistoryChart = new Chart(document.getElementById('paymentHistoryChart'), {
    type: 'line',
    data: {
        labels: [],
        datasets: [{
            label: 'Amount',
            data: [],
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgb(75, 192, 192)',
            borderWidth: 2,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                type: 'time',
                time: {
                    unit: 'day'
                },
                title: {
                    display: true,
                    text: 'Date'
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Amount (₱)'
                },
                ticks: {
                    callback: function(value) {
                        return '₱' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        return '₱' + tooltipItem.raw.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
@endsection
