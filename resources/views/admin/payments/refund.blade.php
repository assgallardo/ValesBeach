@extends('layouts.admin')

@section('title', 'Process Refund')

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
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.payments.show', $payment) }}">{{ $payment->payment_reference }}</a>
                    </li>
                    <li class="breadcrumb-item active">Process Refund</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">Process Refund</h1>
        </div>
    </div>

    <div class="row">
        <!-- Refund Form -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Refund Details</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.payments.refund.process', $payment) }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="refund_type" class="font-weight-bold">Refund Type <span class="text-danger">*</span></label>
                                    <select name="refund_type" id="refund_type" class="form-control @error('refund_type') is-invalid @enderror" required>
                                        <option value="">Select refund type</option>
                                        <option value="full" {{ old('refund_type') == 'full' ? 'selected' : '' }}>Full Refund</option>
                                        <option value="partial" {{ old('refund_type') == 'partial' ? 'selected' : '' }}>Partial Refund</option>
                                    </select>
                                    @error('refund_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="refund_amount" class="font-weight-bold">Refund Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">₱</span>
                                        </div>
                                        <input type="number" 
                                               name="refund_amount" 
                                               id="refund_amount" 
                                               class="form-control @error('refund_amount') is-invalid @enderror" 
                                               step="0.01" 
                                               min="0.01" 
                                               max="{{ $payment->refundable_amount }}" 
                                               value="{{ old('refund_amount', $payment->refundable_amount) }}" 
                                               required>
                                        @error('refund_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">
                                        Maximum refundable amount: ₱{{ number_format($payment->refundable_amount, 2) }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="refund_reason" class="font-weight-bold">Refund Reason <span class="text-danger">*</span></label>
                            <textarea name="refund_reason" 
                                      id="refund_reason" 
                                      class="form-control @error('refund_reason') is-invalid @enderror" 
                                      rows="4" 
                                      placeholder="Please provide a detailed reason for this refund..."
                                      required>{{ old('refund_reason') }}</textarea>
                            @error('refund_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirmation Checkbox -->
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="confirm_refund" required>
                                <label class="form-check-label" for="confirm_refund">
                                    <strong>I confirm that I want to process this refund. This action cannot be undone.</strong>
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-2"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-warning" id="refund_button" disabled>
                                <i class="fas fa-undo mr-2"></i> Process Refund
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Payment Information</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Payment Reference</label>
                        <div class="form-control-plaintext">{{ $payment->payment_reference }}</div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Customer</label>
                        <div class="form-control-plaintext">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-primary mr-3">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div>
                                    <div class="font-weight-bold">{{ $payment->user->name }}</div>
                                    <div class="small text-muted">{{ $payment->user->email }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Original Amount</label>
                        <div class="form-control-plaintext">
                            <span class="h5 text-success">{{ $payment->formatted_amount }}</span>
                        </div>
                    </div>

                    @if($payment->refund_amount > 0)
                        <div class="form-group">
                            <label class="font-weight-bold">Previously Refunded</label>
                            <div class="form-control-plaintext">
                                <span class="h6 text-danger">{{ $payment->formatted_refund_amount }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="font-weight-bold">Refundable Amount</label>
                        <div class="form-control-plaintext">
                            <span class="h5 text-warning">₱{{ number_format($payment->refundable_amount, 2) }}</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Payment Method</label>
                        <div class="form-control-plaintext">
                            <span class="badge badge-light">{{ $payment->payment_method_display }}</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Payment Date</label>
                        <div class="form-control-plaintext">
                            {{ $payment->payment_date ? $payment->payment_date->format('M d, Y h:i A') : 'Not set' }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Current Status</label>
                        <div class="form-control-plaintext">
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
                            <span class="badge badge-{{ $statusColors[$payment->status] ?? 'secondary' }}">
                                {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Refund Warning -->
            <div class="card border-left-warning shadow">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Important Notice
                            </div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800 mb-2">Refund Processing</div>
                            <div class="text-gray-900 small">
                                <ul class="mb-0">
                                    <li>Refunds cannot be undone once processed</li>
                                    <li>Full refunds will cancel related bookings/services</li>
                                    <li>Customer will be notified automatically</li>
                                    <li>Refund processing may take 3-5 business days</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const refundTypeSelect = document.getElementById('refund_type');
    const refundAmountInput = document.getElementById('refund_amount');
    const confirmCheckbox = document.getElementById('confirm_refund');
    const refundButton = document.getElementById('refund_button');
    const maxAmount = {{ $payment->refundable_amount }};

    // Handle refund type change
    refundTypeSelect.addEventListener('change', function() {
        if (this.value === 'full') {
            refundAmountInput.value = maxAmount;
            refundAmountInput.readOnly = true;
        } else {
            refundAmountInput.readOnly = false;
            if (refundAmountInput.value == maxAmount) {
                refundAmountInput.value = '';
            }
        }
        checkFormValidity();
    });

    // Handle confirmation checkbox
    confirmCheckbox.addEventListener('change', function() {
        checkFormValidity();
    });

    // Handle refund amount input
    refundAmountInput.addEventListener('input', function() {
        checkFormValidity();
    });

    function checkFormValidity() {
        const isTypeSelected = refundTypeSelect.value !== '';
        const isAmountValid = refundAmountInput.value > 0 && refundAmountInput.value <= maxAmount;
        const isConfirmed = confirmCheckbox.checked;
        
        refundButton.disabled = !(isTypeSelected && isAmountValid && isConfirmed);
    }

    // Initialize form state
    checkFormValidity();
});
</script>

<style>
.icon-circle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 100%;
}
</style>
@endsection
