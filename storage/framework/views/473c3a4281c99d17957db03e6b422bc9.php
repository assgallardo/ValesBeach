<?php $__env->startSection('title', isset($invoice) ? 'Edit Invoice' : 'Generate Invoice'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 py-8">
    <div class="max-w-6xl mx-auto px-4">
        <form action="<?php echo e(isset($invoice) ? route('admin.invoices.update', $invoice->id) : route('admin.payments.customer.invoice.save', $customer->id)); ?>" method="POST" id="invoiceForm" onsubmit="return confirmInvoiceSubmit()">
            <?php echo csrf_field(); ?>
            <?php if(isset($invoice)): ?>
                <?php echo method_field('PATCH'); ?>
            <?php endif; ?>
            
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-green-50 mb-2"><?php echo e(isset($invoice) ? 'Edit Invoice' : 'Generate Invoice'); ?></h1>
                    <p class="text-gray-400"><?php echo e(isset($invoice) ? 'Modify invoice details and billings as needed' : 'Add extra charges and notes before generating the final invoice'); ?></p>
                    <?php if(isset($invoice)): ?>
                        <p class="text-sm text-gray-500 mt-1">Invoice #: <?php echo e($invoice->invoice_number); ?></p>
                    <?php endif; ?>
                    
                    <?php if(session('error')): ?>
                        <div class="mt-3 px-4 py-2 bg-red-600 bg-opacity-20 border border-red-600 rounded-lg text-red-400 text-sm">
                            <i class="fas fa-exclamation-circle mr-2"></i><?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>
                </div>
                <div class="flex gap-3">
                    <?php if(isset($invoice)): ?>
                        <a href="<?php echo e(route('invoices.show', $invoice->id)); ?>" 
                           class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            Cancel
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e(route('admin.payments.customer', $customer->id)); ?>" 
                           class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            Cancel
                        </a>
                    <?php endif; ?>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold">
                        <i class="fas fa-save mr-2"></i><?php echo e(isset($invoice) ? 'Update Invoice' : 'Generate Invoice'); ?>

                    </button>
                </div>
            </div>

            <!-- Customer Info Card -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6 mb-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-400 mb-2">Bill To:</h3>
                        <p class="text-lg font-bold text-green-50"><?php echo e($customer->name); ?></p>
                        <p class="text-gray-400 text-sm"><?php echo e($customer->email); ?></p>
                        <p class="text-gray-500 text-sm">Member since <?php echo e($customer->created_at->format('M d, Y')); ?></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-400 mb-2">Due Date (Optional)</label>
                        <input type="date" 
                               name="due_date" 
                               value="<?php echo e(isset($invoice) && $invoice->due_date ? $invoice->due_date->format('Y-m-d') : now()->addDays(7)->format('Y-m-d')); ?>"
                               class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-green-50 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden mb-6">
                <div class="px-6 py-4 bg-gray-750 border-b border-gray-700 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-green-50">Invoice Items</h2>
                    <button type="button" 
                            onclick="addInvoiceItem()"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">
                        <i class="fas fa-plus mr-2"></i>Additionals
                    </button>
                </div>

                <?php if(empty($items)): ?>
                <div class="px-6 py-8 text-center">
                    <div class="mb-4">
                        <i class="fas fa-receipt text-gray-600 text-5xl mb-3"></i>
                        <p class="text-gray-400 text-lg mb-2">No invoice items found</p>
                        <p class="text-gray-500 text-sm">This customer has no bookings, services, or food orders yet.</p>
                        <p class="text-gray-500 text-sm mt-1">Click the "Additionals" button above to add manual charges.</p>
                    </div>
                </div>
                <?php endif; ?>

                <div class="overflow-x-auto" id="invoiceTableContainer" style="<?php echo e(empty($items) ? 'display: none;' : ''); ?>">
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
                            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="invoice-item-row" data-index="<?php echo e($index); ?>">
                                <td class="px-4 py-3">
                                    <select name="items[<?php echo e($index); ?>][type]" 
                                            class="w-full px-2 py-1 text-xs bg-gray-700 border border-gray-600 rounded text-green-50 item-type"
                                            required>
                                        <option value="booking" <?php echo e($item['type'] == 'booking' ? 'selected' : ''); ?>>Booking</option>
                                        <option value="service" <?php echo e($item['type'] == 'service' ? 'selected' : ''); ?>>Service</option>
                                        <option value="food" <?php echo e($item['type'] == 'food' ? 'selected' : ''); ?>>Food</option>
                                        <option value="extra" <?php echo e($item['type'] == 'extra' ? 'selected' : ''); ?>>Additionals</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" 
                                           name="items[<?php echo e($index); ?>][description]" 
                                           value="<?php echo e($item['description']); ?>"
                                           class="w-full px-2 py-1 text-sm bg-gray-700 border border-gray-600 rounded text-green-50"
                                           required>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" 
                                           name="items[<?php echo e($index); ?>][reference]" 
                                           value="<?php echo e($item['reference']); ?>"
                                           class="w-full px-2 py-1 text-sm bg-gray-700 border border-gray-600 rounded text-green-50">
                                    <?php if(isset($item['payment_id'])): ?>
                                    <input type="hidden" 
                                           name="items[<?php echo e($index); ?>][payment_id]" 
                                           value="<?php echo e($item['payment_id']); ?>">
                                    <input type="hidden" 
                                           name="items[<?php echo e($index); ?>][payment_reference]" 
                                           value="<?php echo e($item['payment_reference'] ?? ''); ?>">
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" 
                                           name="items[<?php echo e($index); ?>][details]" 
                                           value="<?php echo e($item['details']); ?>"
                                           class="w-full px-2 py-1 text-sm bg-gray-700 border border-gray-600 rounded text-green-50">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" 
                                           name="items[<?php echo e($index); ?>][amount]" 
                                           value="<?php echo e($item['amount']); ?>"
                                           step="0.01"
                                           min="0"
                                           class="w-24 px-2 py-1 text-sm bg-gray-700 border border-gray-600 rounded text-right text-green-50 item-amount"
                                           oninput="calculateRowBalance(this)"
                                           required>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" 
                                           name="items[<?php echo e($index); ?>][paid]" 
                                           value="<?php echo e($item['paid']); ?>"
                                           step="0.01"
                                           min="0"
                                           class="w-24 px-2 py-1 text-sm bg-gray-700 border border-gray-600 rounded text-right text-green-50 item-paid"
                                           oninput="calculateRowBalance(this)"
                                           required>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="text-sm font-medium item-balance <?php echo e($item['balance'] > 0 ? 'text-red-400' : 'text-green-400'); ?>">
                                        ₱<?php echo e(number_format($item['balance'], 2)); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" 
                                            onclick="removeInvoiceItem(this)"
                                            class="text-red-400 hover:text-red-300"
                                            title="Remove item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="px-6 py-4 bg-gray-750 border-t border-gray-700">
                    <div class="flex justify-end">
                        <div class="w-full md:w-1/3">
                            <div class="flex justify-between py-2 text-gray-300">
                                <span>Total Amount:</span>
                                <span class="font-bold" id="totalAmount">₱<?php echo e(number_format($totalAmount, 2)); ?></span>
                            </div>
                            <div class="flex justify-between py-2 text-green-400">
                                <span>Total Paid:</span>
                                <span class="font-bold" id="totalPaid">₱<?php echo e(number_format($totalPaid, 2)); ?></span>
                            </div>
                            <div class="flex justify-between py-2 border-t-2 border-gray-600 text-lg font-bold">
                                <span class="text-green-50">Balance Due:</span>
                                <span id="balanceDue" class="<?php echo e($totalBalance > 0 ? 'text-red-400' : 'text-green-400'); ?>">
                                    ₱<?php echo e(number_format($totalBalance, 2)); ?>

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
                          placeholder="Add any additional notes, payment terms, or instructions for the customer..."><?php echo e(isset($invoice) ? $invoice->notes : ''); ?></textarea>
                <p class="text-xs text-gray-500 mt-2">These notes will appear at the bottom of the invoice.</p>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-3">
                <?php if(isset($invoice)): ?>
                    <a href="<?php echo e(route('invoices.show', $invoice->id)); ?>" 
                       class="px-6 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Cancel
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('admin.payments.customer', $customer->id)); ?>" 
                       class="px-6 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Cancel
                    </a>
                <?php endif; ?>
                <button type="submit" 
                        class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold text-lg">
                    <i class="fas fa-file-invoice-dollar mr-2"></i><?php echo e(isset($invoice) ? 'Update Invoice' : 'Generate Invoice'); ?>

                </button>
            </div>
        </form>
    </div>
</div>

<script>
let itemIndex = <?php echo e(count($items)); ?>;

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
                   class="w-full px-2 py-1 text-sm bg-gray-700 border border-gray-600 rounded text-green-50"
                   required>
        </td>
        <td class="px-4 py-3">
            <input type="text" 
                   name="items[${itemIndex}][reference]" 
                   class="w-full px-2 py-1 text-sm bg-gray-700 border border-gray-600 rounded text-green-50">
        </td>
        <td class="px-4 py-3">
            <input type="text" 
                   name="items[${itemIndex}][details]" 
                   placeholder="Additional details"
                   class="w-full px-2 py-1 text-sm bg-gray-700 border border-gray-600 rounded text-green-50">
        </td>
        <td class="px-4 py-3">
            <input type="number" 
                   name="items[${itemIndex}][amount]" 
                   value="0"
                   step="0.01"
                   min="0"
                   class="w-24 px-2 py-1 text-sm bg-gray-700 border border-gray-600 rounded text-right text-green-50 item-amount"
                   oninput="calculateRowBalance(this)"
                   required>
        </td>
        <td class="px-4 py-3">
            <input type="number" 
                   name="items[${itemIndex}][paid]" 
                   value="0"
                   step="0.01"
                   min="0"
                   class="w-24 px-2 py-1 text-sm bg-gray-700 border border-gray-600 rounded text-right text-green-50 item-paid"
                   oninput="calculateRowBalance(this)"
                   required>
        </td>
        <td class="px-4 py-3 text-right">
            <span class="text-sm font-medium item-balance text-gray-400">
                ₱0.00
            </span>
        </td>
        <td class="px-4 py-3 text-center">
            <button type="button" 
                    onclick="removeInvoiceItem(this)"
                    class="text-red-400 hover:text-red-300"
                    title="Remove item">
                <i class="fas fa-trash"></i>
            </button>
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
        let message = 'You are about to add the following extra charge(s) to this invoice:\n\n';
        extraCharges.forEach((charge, index) => {
            message += `${index + 1}. ${charge.description} - ₱${parseFloat(charge.amount).toFixed(2)}\n`;
        });
        message += '\nThese charges will be added to the customer payment details table and included in the invoice.\n\nDo you want to proceed?';
        
        return confirm(message);
    }
    
    return true;
}
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\invoices\customer-invoice-edit.blade.php ENDPATH**/ ?>