@extends('layouts.admin')

@section('title', isset($invoice) ? 'Edit Invoice' : 'Generate Invoice')

@section('head')
<!-- Prevent browser caching to ensure fresh data on back navigation -->
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
@endsection

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 py-8">
    <div class="max-w-6xl mx-auto px-4">
        <form action="{{ isset($invoice) ? route('admin.invoices.update', $invoice->id) : route('admin.payments.customer.invoice.save', $customer->id) }}" method="POST" id="invoiceForm" onsubmit="return confirmInvoiceSubmit()">
            @csrf
            @if(isset($invoice))
                @method('PATCH')
            @endif
            
            <!-- Hidden field to pass transaction_id to saveCustomerInvoice -->
            @if(!isset($invoice) && (request('transaction_id') || isset($transactionId)))
                <input type="hidden" name="transaction_id" value="{{ request('transaction_id') ?? $transactionId }}">
            @endif
            
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.payments.customer', ['user' => $customer->id, 'transaction_id' => request('transaction_id') ?? ($transactionId ?? null)]) }}" 
                       class="text-gray-400 hover:text-green-400 transition-colors"
                       title="Back to Customer Payments">
                        <i class="fas fa-arrow-left text-2xl"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-green-50 mb-2">{{ isset($invoice) ? 'Edit Invoice' : 'Generate Invoice' }}</h1>
                        <p class="text-gray-400">{{ isset($invoice) ? 'Modify invoice details and billings as needed' : 'Add extra charges and notes before generating the final invoice' }}</p>
                        @if(isset($invoice))
                            <p class="text-sm text-gray-500 mt-1">Invoice #: {{ $invoice->invoice_number }}</p>
                        @endif
                        
                        @if(session('error'))
                            <div class="mt-3 px-4 py-2 bg-red-600 bg-opacity-20 border border-red-600 rounded-lg text-red-400 text-sm">
                                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="flex gap-3">
                    @if(isset($invoice))
                        <a href="{{ route('invoices.show', $invoice->id) }}" 
                           class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            Cancel
                        </a>
                    @else
                        <a href="{{ route('admin.payments.customer', ['user' => $customer->id, 'transaction_id' => request('transaction_id') ?? ($transactionId ?? null)]) }}" 
                           class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            Cancel
                        </a>
                    @endif
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold">
                        <i class="fas fa-save mr-2"></i>{{ isset($invoice) ? 'Update Invoice' : 'Generate Invoice' }}
                    </button>
                </div>
            </div>

            <!-- Customer Info Card -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6 mb-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-400 mb-2">Bill To:</h3>
                        <p class="text-lg font-bold text-green-50">{{ $customer->name }}</p>
                        <p class="text-gray-400 text-sm">{{ $customer->email }}</p>
                        <p class="text-gray-500 text-sm">Member since {{ $customer->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-400 mb-2">Due Date (Optional)</label>
                        <input type="date" 
                               name="due_date" 
                               value="{{ isset($invoice) && $invoice->due_date ? $invoice->due_date->format('Y-m-d') : now()->addDays(7)->format('Y-m-d') }}"
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gray-750 border-b border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-green-50">Invoice Items</h2>
                            <p class="text-xs text-gray-400 mt-1">Extra charges will be saved as payment records and appear in Customer Payment Details</p>
                        </div>
                        <button type="button" 
                                onclick="confirmAndAddInvoiceItem()"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">
                            <i class="fas fa-plus mr-2"></i>Additionals
                        </button>
                    </div>
                </div>

                @if(empty($items))
                <div class="px-6 py-8 text-center">
                    <div class="mb-4">
                        <i class="fas fa-receipt text-gray-600 text-5xl mb-3"></i>
                        <p class="text-gray-400 text-lg mb-2">No invoice items found</p>
                        <p class="text-gray-500 text-sm">This customer has no bookings, services, or food orders yet.</p>
                        <p class="text-gray-500 text-sm mt-1">Click the "Additionals" button above to add manual charges.</p>
                    </div>
                </div>
                @endif

                <div class="overflow-x-auto" id="invoiceTableContainer" style="{{ empty($items) ? 'display: none;' : '' }}">
                    <table class="w-full" id="invoiceItemsTable">
                        <thead class="bg-gray-750">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Description</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Reference</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Details</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-300 uppercase">Amount</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-300 uppercase">Paid</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-300 uppercase">Balance</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-300 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="invoiceItemsBody" class="divide-y divide-gray-700">
                            @foreach($items as $index => $item)
                            <tr class="invoice-item-row" data-index="{{ $index }}" data-item-type="{{ $item['type'] }}" data-has-payment-id="{{ isset($item['payment_id']) ? 'true' : 'false' }}">
                                <td class="px-4 py-3">
                                    <select name="items[{{ $index }}][type]" 
                                            class="w-full px-2 py-1 text-xs bg-gray-700 border border-gray-600 rounded text-green-50 item-type"
                                            onchange="handleExtraChargeFieldChange(this)"
                                            required>
                                        <option value="booking" {{ $item['type'] == 'booking' ? 'selected' : '' }}>Booking</option>
                                        <option value="service" {{ $item['type'] == 'service' ? 'selected' : '' }}>Service</option>
                                        <option value="food" {{ $item['type'] == 'food' ? 'selected' : '' }}>Food</option>
                                        <option value="extra" {{ $item['type'] == 'extra' ? 'selected' : '' }}>Additionals</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" 
                                           name="items[{{ $index }}][description]" 
                                           value="{{ $item['description'] }}"
                                           class="w-full px-2 py-1 text-sm bg-gray-700 border border-gray-600 rounded text-green-50 item-description"
                                           oninput="handleExtraChargeFieldChange(this)"
                                           required>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" 
                                           name="items[{{ $index }}][reference]" 
                                           value="{{ $item['reference'] }}"
                                           class="w-full px-2 py-1 text-sm bg-gray-700 border border-gray-600 rounded text-green-50 item-reference"
                                           oninput="handleExtraChargeFieldChange(this)">
                                    @if(isset($item['payment_id']))
                                    <input type="hidden" 
                                           name="items[{{ $index }}][payment_id]" 
                                           value="{{ $item['payment_id'] }}">
                                    <input type="hidden" 
                                           name="items[{{ $index }}][payment_reference]" 
                                           value="{{ $item['payment_reference'] ?? '' }}">
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" 
                                           name="items[{{ $index }}][details]" 
                                           value="{{ $item['details'] }}"
                                           class="w-full px-2 py-1 text-sm bg-gray-700 border border-gray-600 rounded text-green-50 item-details"
                                           oninput="handleExtraChargeFieldChange(this)">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" 
                                           name="items[{{ $index }}][amount]" 
                                           value="{{ $item['amount'] }}"
                                           step="0.01"
                                           min="0"
                                           class="w-24 px-2 py-1 text-sm bg-gray-700 border border-gray-600 rounded text-right text-green-50 item-amount"
                                           oninput="calculateRowBalance(this); handleExtraChargeFieldChange(this);"
                                           required>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" 
                                           name="items[{{ $index }}][paid]" 
                                           value="{{ $item['paid'] }}"
                                           step="0.01"
                                           min="0"
                                           class="w-24 px-2 py-1 text-sm bg-gray-700 border border-gray-600 rounded text-right text-green-50 item-paid"
                                           oninput="calculateRowBalance(this); handleExtraChargeFieldChange(this);"
                                           required>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="text-sm font-medium item-balance {{ $item['balance'] > 0 ? 'text-red-400' : 'text-green-400' }}">
                                        ₱{{ number_format($item['balance'], 2) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($item['type'] == 'extra' && !isset($item['payment_id']))
                                        <button type="button" 
                                                onclick="saveExtraCharge(this)"
                                                class="text-green-400 hover:text-green-300 save-extra-btn"
                                                title="Save extra charge now">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        @elseif($item['type'] == 'extra' && isset($item['payment_id']))
                                        <button type="button" 
                                                onclick="saveExtraCharge(this)"
                                                class="text-green-400 hover:text-green-300 save-extra-btn hidden"
                                                title="Save changes"
                                                style="display: none;">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        @endif
                                        <button type="button" 
                                                onclick="removeInvoiceItem(this)"
                                                class="text-red-400 hover:text-red-300"
                                                title="Remove item">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="px-6 py-4 bg-gray-750 border-t border-gray-700">
                    <div class="flex justify-end">
                        <div class="w-full md:w-1/3">
                            <div class="flex justify-between py-2 text-gray-300">
                                <span>Total Amount:</span>
                                <span class="font-bold" id="totalAmount">₱{{ number_format($totalAmount, 2) }}</span>
                            </div>
                            <div class="flex justify-between py-2 text-green-400">
                                <span>Total Paid:</span>
                                <span class="font-bold" id="totalPaid">₱{{ number_format($totalPaid, 2) }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-t-2 border-gray-600 text-lg font-bold">
                                <span class="text-green-50">Balance Due:</span>
                                <span id="balanceDue" class="{{ $totalBalance > 0 ? 'text-red-400' : 'text-green-400' }}">
                                    ₱{{ number_format($totalBalance, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6 mb-6">
                <label class="block text-lg font-semibold text-green-50 mb-3">Invoice Notes (Optional)</label>
                <textarea name="notes" 
                          rows="4" 
                          class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-green-50 placeholder-gray-500 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                          placeholder="Add any additional notes, payment terms, or instructions for the customer...">{{ isset($invoice) ? $invoice->notes : '' }}</textarea>
                <p class="text-xs text-gray-500 mt-2">These notes will appear at the bottom of the invoice.</p>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-3">
                @if(isset($invoice))
                    <a href="{{ route('invoices.show', $invoice->id) }}" 
                       class="px-6 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Cancel
                    </a>
                @else
                    <a href="{{ route('admin.payments.customer', ['user' => $customer->id, 'transaction_id' => request('transaction_id') ?? ($transactionId ?? null)]) }}" 
                       class="px-6 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Cancel
                    </a>
                @endif
                <button type="submit" 
                        class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold text-lg">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>{{ isset($invoice) ? 'Update Invoice' : 'Generate Invoice' }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let itemIndex = {{ count($items) }};

function confirmAndAddInvoiceItem() {
    const confirmMessage = 'Add an extra charge?\n\n' +
        'This will create a new payment record that will appear in the Customer Payment Details. ' +
        'The extra charge will be saved when you click "Generate Invoice".\n\n' +
        'Do you want to continue?';
    
    if (confirm(confirmMessage)) {
        addInvoiceItem();
    }
}

function addInvoiceItem() {
    const tbody = document.getElementById('invoiceItemsBody');
    const tableContainer = document.getElementById('invoiceTableContainer');
    const row = document.createElement('tr');
    row.className = 'invoice-item-row';
    row.dataset.index = itemIndex;
    
    // Show the table if it's hidden
    if (tableContainer) {
        tableContainer.style.display = '';
    }
    
    row.innerHTML = `
        <td class="px-4 py-3">
            <select name="items[${itemIndex}][type]" 
                    class="w-full px-2 py-1 text-xs bg-gray-700 border border-gray-600 rounded text-green-50 item-type"
                    onchange="handleExtraChargeFieldChange(this)"
                    required>
                <option value="extra" selected>Extra Charge</option>
                <option value="booking">Booking</option>
                <option value="service">Service</option>
                <option value="food">Food</option>
            </select>
        </td>
        <td class="px-4 py-3">
            <input type="text" 
                   name="items[${itemIndex}][description]" 
                   placeholder="e.g., Late checkout fee"
                   class="w-full px-2 py-1 text-sm bg-gray-700 border border-gray-600 rounded text-green-50 item-description"
                   oninput="handleExtraChargeFieldChange(this)"
                   required>
        </td>
        <td class="px-4 py-3">
            <input type="text" 
                   name="items[${itemIndex}][reference]" 
                   class="w-full px-2 py-1 text-sm bg-gray-700 border border-gray-600 rounded text-green-50 item-reference"
                   oninput="handleExtraChargeFieldChange(this)">
        </td>
        <td class="px-4 py-3">
            <input type="text" 
                   name="items[${itemIndex}][details]" 
                   placeholder="Additional details"
                   class="w-full px-2 py-1 text-sm bg-gray-700 border border-gray-600 rounded text-green-50 item-details"
                   oninput="handleExtraChargeFieldChange(this)">
        </td>
        <td class="px-4 py-3">
            <input type="number" 
                   name="items[${itemIndex}][amount]" 
                   value="0"
                   step="0.01"
                   min="0"
                   class="w-24 px-2 py-1 text-sm bg-gray-700 border border-gray-600 rounded text-right text-green-50 item-amount"
                   oninput="calculateRowBalance(this); handleExtraChargeFieldChange(this);"
                   required>
        </td>
        <td class="px-4 py-3">
            <input type="number" 
                   name="items[${itemIndex}][paid]" 
                   value="0"
                   step="0.01"
                   min="0"
                   class="w-24 px-2 py-1 text-sm bg-gray-700 border border-gray-600 rounded text-right text-green-50 item-paid"
                   oninput="calculateRowBalance(this); handleExtraChargeFieldChange(this);"
                   required>
        </td>
        <td class="px-4 py-3 text-right">
            <span class="text-sm font-medium item-balance text-gray-400">
                ₱0.00
            </span>
        </td>
        <td class="px-4 py-3 text-center">
            <div class="flex items-center justify-center gap-2">
                <button type="button" 
                        onclick="saveExtraCharge(this)"
                        class="text-green-400 hover:text-green-300 save-extra-btn"
                        title="Save extra charge now">
                    <i class="fas fa-check"></i>
                </button>
                <button type="button" 
                        onclick="removeInvoiceItem(this)"
                        class="text-red-400 hover:text-red-300"
                        title="Remove item">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>
    `;
    
    tbody.appendChild(row);
    itemIndex++;
}

function removeInvoiceItem(button) {
    const row = button.closest('tr');
    const itemType = row.querySelector('.item-type')?.value || '';
    const hasPaymentId = row.querySelector('input[name*="[payment_id]"]')?.value;
    
    // Special confirmation for extra charges since they delete payment records
    let confirmMessage = 'Are you sure you want to remove this item?';
    if (itemType === 'extra' && hasPaymentId) {
        confirmMessage = 'Are you sure you want to remove this extra charge? This will permanently delete the payment record and it will disappear from Customer Payment Details.';
    }
    
    if (!confirm(confirmMessage)) {
        return;
    }
    
    // Remove the row
    row.remove();
    
    // Recalculate totals
    calculateTotals();
    
    // Show feedback for extra charges
    if (itemType === 'extra' && hasPaymentId) {
        console.log('Extra charge row removed. Payment will be deleted when invoice is saved.');
    }
}

function calculateRowBalance(input) {
    const row = input.closest('tr');
    const amount = parseFloat(row.querySelector('.item-amount').value) || 0;
    const paid = parseFloat(row.querySelector('.item-paid').value) || 0;
    const balance = amount - paid;
    
    const balanceSpan = row.querySelector('.item-balance');
    balanceSpan.textContent = '₱' + balance.toFixed(2);
    balanceSpan.className = 'text-sm font-medium item-balance ' + (balance > 0 ? 'text-red-400' : 'text-green-400');
    
    calculateTotals();
}

function calculateTotals() {
    let totalAmount = 0;
    let totalPaid = 0;
    
    document.querySelectorAll('.invoice-item-row').forEach(row => {
        const amount = parseFloat(row.querySelector('.item-amount').value) || 0;
        const paid = parseFloat(row.querySelector('.item-paid').value) || 0;
        totalAmount += amount;
        totalPaid += paid;
    });
    
    const balanceDue = totalAmount - totalPaid;
    
    document.getElementById('totalAmount').textContent = '₱' + totalAmount.toFixed(2);
    document.getElementById('totalPaid').textContent = '₱' + totalPaid.toFixed(2);
    
    const balanceDueElement = document.getElementById('balanceDue');
    balanceDueElement.textContent = '₱' + balanceDue.toFixed(2);
    balanceDueElement.className = balanceDue > 0 ? 'text-red-400' : 'text-green-400';
}

// Initialize calculations on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotals();
});

// Handle field changes for extra charges
function handleExtraChargeFieldChange(input) {
    const row = input.closest('tr');
    const typeSelect = row.querySelector('.item-type');
    
    // Only handle extra charge rows
    if (!typeSelect || typeSelect.value !== 'extra') {
        return;
    }
    
    // Check if this row has a payment_id (already saved)
    const hasPaymentId = row.querySelector('input[name*="[payment_id]"]');
    
    if (hasPaymentId) {
        // Show the save button if hidden (for previously saved extra charges)
        const saveBtn = row.querySelector('.save-extra-btn');
        if (saveBtn) {
            saveBtn.classList.remove('hidden');
            saveBtn.style.display = '';
            saveBtn.title = 'Save changes to extra charge';
            
            // Remove readonly from fields to allow editing
            const inputs = row.querySelectorAll('.item-description, .item-reference, .item-details, .item-amount, .item-paid');
            inputs.forEach(inp => {
                inp.readOnly = false;
                inp.classList.remove('bg-gray-600');
                inp.classList.add('bg-gray-700');
            });
        }
    }
}

// Save extra charge immediately
function saveExtraCharge(button) {
    const row = button.closest('tr');
    const typeSelect = row.querySelector('.item-type');
    const descriptionInput = row.querySelector('input[name*="[description]"]');
    const referenceInput = row.querySelector('input[name*="[reference]"]');
    const detailsInput = row.querySelector('input[name*="[details]"]');
    const amountInput = row.querySelector('.item-amount');
    const paidInput = row.querySelector('.item-paid');
    
    // Validate inputs
    if (!descriptionInput.value.trim()) {
        alert('Please enter a description for the extra charge.');
        descriptionInput.focus();
        return;
    }
    
    if (!amountInput.value || parseFloat(amountInput.value) <= 0) {
        alert('Please enter a valid amount greater than 0.');
        amountInput.focus();
        return;
    }
    
    const confirmMessage = `Save this extra charge?\n\n` +
        `Description: ${descriptionInput.value}\n` +
        `Amount: ₱${parseFloat(amountInput.value).toFixed(2)}\n` +
        `Paid: ₱${parseFloat(paidInput.value).toFixed(2)}\n\n` +
        `This will immediately create a payment record in Customer Payment Details.`;
    
    if (!confirm(confirmMessage)) {
        return;
    }
    
    // Disable button during save
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    // Get transaction ID
    const transactionId = '{{ request("transaction_id") ?? ($transactionId ?? "") }}';
    
    if (!transactionId) {
        alert('Error: No transaction ID found. Please refresh the page and try again.');
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-check"></i>';
        return;
    }
    
    // Prepare data
    const data = {
        type: typeSelect.value,
        description: descriptionInput.value,
        reference: referenceInput.value,
        details: detailsInput.value,
        amount: parseFloat(amountInput.value),
        paid: parseFloat(paidInput.value),
        transaction_id: transactionId
    };
    
    // Check if this is an update (payment_id exists)
    const paymentIdInput = row.querySelector('input[name*="[payment_id]"]');
    if (paymentIdInput && paymentIdInput.value) {
        data.payment_id = paymentIdInput.value;
    }
    
    // Send AJAX request
    fetch('{{ route("admin.payments.extraCharge.save", $customer->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // If this was a new save (not update), add payment ID to the row
            if (result.action === 'saved') {
                const existingPaymentIdInput = row.querySelector('input[name*="[payment_id]"]');
                if (!existingPaymentIdInput) {
                    const paymentIdInput = document.createElement('input');
                    paymentIdInput.type = 'hidden';
                    paymentIdInput.name = `items[${row.dataset.index}][payment_id]`;
                    paymentIdInput.value = result.payment.id;
                    row.querySelector('td:nth-child(3)').appendChild(paymentIdInput);
                    
                    const paymentRefInput = document.createElement('input');
                    paymentRefInput.type = 'hidden';
                    paymentRefInput.name = `items[${row.dataset.index}][payment_reference]`;
                    paymentRefInput.value = result.payment.payment_reference;
                    row.querySelector('td:nth-child(3)').appendChild(paymentRefInput);
                }
            }
            
            // Hide the check button temporarily (will reappear on next edit)
            button.style.display = 'none';
            button.classList.add('hidden');
            
            // Re-enable button for next save
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-check"></i>';
            
            // Show success message
            const actionText = result.action === 'saved' ? 'saved' : 'updated';
            alert(`✓ Extra charge ${actionText} successfully!\n\nPayment Reference: ${result.payment.payment_reference}\n\nYou can continue editing and click the check icon to save changes.`);
        } else {
            alert('Error: ' + (result.message || 'Failed to save extra charge'));
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-check"></i>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the extra charge. Please try again.');
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-check"></i>';
    });
}

// Confirm invoice submission with extra charges
function confirmInvoiceSubmit() {
    const rows = document.querySelectorAll('.invoice-item-row');
    const extraCharges = [];
    
    rows.forEach(row => {
        const typeSelect = row.querySelector('.item-type');
        if (typeSelect && typeSelect.value === 'extra') {
            const description = row.querySelector('input[name*="[description]"]')?.value || 'Unnamed charge';
            const amount = row.querySelector('.item-amount')?.value || '0';
            extraCharges.push({ description, amount });
        }
    });
    
    if (extraCharges.length > 0) {
        let message = '⚠️ CONFIRM EXTRA CHARGES ⚠️\n\n';
        message += 'You are about to add the following extra charge(s):\n\n';
        extraCharges.forEach((charge, index) => {
            message += `${index + 1}. ${charge.description} - ₱${parseFloat(charge.amount).toFixed(2)}\n`;
        });
        message += '\n✅ These charges will:\n';
        message += '  • Be saved as payment records\n';
        message += '  • Appear in Customer Payment Details\n';
        message += '  • Be included in this invoice\n';
        message += '\nDo you want to proceed?';
        
        return confirm(message);
    }
    
    return true;
}

// Handle browser back button and page visibility changes
window.addEventListener('pageshow', function(event) {
    // Force page reload if coming from cache (back/forward navigation)
    if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
        console.log('Page restored from cache, reloading...');
        window.location.reload();
    }
});

// Handle page visibility changes (tab switching, window focus)
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        // Re-enable all buttons when page becomes visible again
        document.querySelectorAll('button[type="submit"]').forEach(button => {
            if (button.disabled && !button.classList.contains('intentionally-disabled')) {
                button.disabled = false;
            }
        });
    }
});

// Ensure forms are enabled on page load
window.addEventListener('load', function() {
    // Re-enable all buttons
    document.querySelectorAll('button[type="submit"]').forEach(button => {
        button.disabled = false;
    });
});
</script>
@endsection

