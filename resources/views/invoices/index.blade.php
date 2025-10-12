@extends('layouts.guest')

@section('title', 'Invoices')

@section('content')
<div class="min-h-screen bg-gray-900 py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-green-50">Invoices</h1>
                    <p class="text-gray-400 mt-2">View and manage your invoices</p>
                </div>
                
                <!-- Generate Invoice Button -->
                <div class="mt-4 sm:mt-0">
                    <button 
                        onclick="openGenerateInvoiceModal()" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors"
                    >
                        <i class="fas fa-plus mr-2"></i>
                        Generate Invoice
                    </button>
                </div>
            </div>
        </div>

        @if($invoices->isEmpty())
            <!-- Empty State -->
            <div class="bg-gray-800 rounded-lg p-8 text-center">
                <i class="fas fa-file-invoice text-6xl text-gray-600 mb-4"></i>
                <h3 class="text-xl font-semibold text-green-50 mb-2">No Invoices Yet</h3>
                <p class="text-gray-400 mb-6">You don't have any invoices yet. Invoices are automatically generated for your bookings, or you can generate one manually.</p>
                <div class="flex justify-center space-x-4">
                    <a 
                        href="{{ route('guest.rooms.browse') }}" 
                        class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors"
                    >
                        <i class="fas fa-search mr-2"></i>
                        Browse Rooms
                    </a>
                    <button 
                        onclick="openGenerateInvoiceModal()" 
                        class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors"
                    >
                        <i class="fas fa-plus mr-2"></i>
                        Generate Invoice
                    </button>
                </div>
            </div>
        @else
            <!-- Invoices List -->
            <div class="space-y-6">
                @foreach($invoices as $invoice)
                <div class="bg-gray-800 rounded-lg p-6 hover:bg-gray-750 transition-colors">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <!-- Invoice Info -->
                        <div class="flex-1">
                            <div class="flex items-center mb-3">
                                <h3 class="text-xl font-semibold text-green-50 mr-4">
                                    {{ $invoice->invoice_number }}
                                </h3>
                                <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $invoice->status_badge_class }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                                @if($invoice->isOverdue())
                                    <span class="ml-2 inline-block px-2 py-1 rounded text-xs bg-red-600 text-white">
                                        Overdue
                                    </span>
                                @endif
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm mb-4">
                                <div>
                                    <span class="text-gray-400 block">Amount</span>
                                    <span class="text-green-400 font-bold text-lg">{{ $invoice->formatted_total_amount }}</span>
                                </div>
                                
                                <div>
                                    <span class="text-gray-400 block">Issue Date</span>
                                    <span class="text-green-50">{{ $invoice->issue_date->format('M d, Y') }}</span>
                                </div>
                                
                                <div>
                                    <span class="text-gray-400 block">Due Date</span>
                                    <span class="text-green-50 {{ $invoice->isOverdue() ? 'text-red-400' : '' }}">
                                        {{ $invoice->due_date->format('M d, Y') }}
                                    </span>
                                </div>
                                
                                @if($invoice->paid_date)
                                <div>
                                    <span class="text-gray-400 block">Paid Date</span>
                                    <span class="text-green-400">{{ $invoice->paid_date->format('M d, Y') }}</span>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Booking Info -->
                            @if($invoice->booking && $invoice->booking->room)
                            <div class="pt-3 border-t border-gray-600">
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-bed text-green-400 mr-2"></i>
                                    <span class="text-gray-400 mr-2">Room:</span>
                                    <span class="text-green-50 font-medium mr-4">{{ $invoice->booking->room->name }}</span>
                                    
                                    <i class="fas fa-calendar text-green-400 mr-2"></i>
                                    <span class="text-gray-400 mr-2">Stay:</span>
                                    <span class="text-green-50">
                                        {{ $invoice->booking->check_in->format('M d') }} - 
                                        {{ $invoice->booking->check_out->format('M d, Y') }}
                                    </span>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-3 mt-4 lg:mt-0">
                            <a 
                                href="{{ route('invoices.show', $invoice) }}" 
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors"
                            >
                                <i class="fas fa-eye mr-2"></i>
                                View Invoice
                            </a>
                            
                            <a 
                                href="{{ route('invoices.download', $invoice) }}" 
                                target="_blank"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                <i class="fas fa-download mr-2"></i>
                                Download
                            </a>
                            
                            @if(!$invoice->isPaid() && $invoice->booking && $invoice->booking->remaining_balance > 0)
                            <a 
                                href="{{ route('payments.create', $invoice->booking) }}" 
                                class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700 transition-colors"
                            >
                                <i class="fas fa-credit-card mr-2"></i>
                                Pay Now
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($invoices->hasPages())
            <div class="mt-8">
                {{ $invoices->links() }}
            </div>
            @endif
        @endif

        <!-- Invoice Summary Stats -->
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-file-invoice text-green-400 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">Total Invoiced</p>
                        <p class="text-lg font-semibold text-green-50">
                            ₱{{ number_format($invoices->sum('total_amount'), 2) }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">Paid</p>
                        <p class="text-lg font-semibold text-green-50">
                            ₱{{ number_format($invoices->where('status', 'paid')->sum('total_amount'), 2) }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-yellow-400 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">Pending</p>
                        <p class="text-lg font-semibold text-green-50">
                            ₱{{ number_format($invoices->whereIn('status', ['draft', 'sent'])->sum('total_amount'), 2) }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-400">Overdue</p>
                        <p class="text-lg font-semibold text-green-50">
                            {{ $invoices->filter(function($invoice) { return $invoice->isOverdue(); })->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Dashboard -->
        <div class="mt-8 text-center">
            <a 
                href="{{ route('guest.dashboard') }}" 
                class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg font-medium hover:bg-gray-700 transition-colors"
            >
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Generate Invoice Modal -->
<div id="generateInvoiceModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-green-50">Generate Invoice</h3>
                <button onclick="closeGenerateInvoiceModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="generateInvoiceForm" method="POST">
                @csrf
                
                <!-- Booking Selection -->
                <div class="mb-4">
                    <label for="booking_id" class="block text-sm font-medium text-gray-300 mb-2">
                        Select Booking
                    </label>
                    <select 
                        name="booking_id" 
                        id="booking_id" 
                        required 
                        onchange="updateFormAction()"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500"
                    >
                        <option value="">Select a booking...</option>
                        @foreach(auth()->user()->bookings()->whereDoesntHave('invoice')->get() as $booking)
                        <option value="{{ $booking->id }}">
                            {{ $booking->booking_reference }} - {{ $booking->room->name }} 
                            ({{ $booking->check_in->format('M d') }} - {{ $booking->check_out->format('M d, Y') }})
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Only bookings without invoices are shown</p>
                </div>
                
                <!-- Due Date -->
                <div class="mb-4">
                    <label for="due_date" class="block text-sm font-medium text-gray-300 mb-2">
                        Due Date
                    </label>
                    <input 
                        type="date" 
                        name="due_date" 
                        id="due_date" 
                        value="{{ now()->addDays(7)->format('Y-m-d') }}"
                        min="{{ now()->format('Y-m-d') }}"
                        required 
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500"
                    >
                </div>
                
                <!-- Tax Rate -->
                <div class="mb-4">
                    <label for="tax_rate" class="block text-sm font-medium text-gray-300 mb-2">
                        Tax Rate (%)
                    </label>
                    <input 
                        type="number" 
                        name="tax_rate" 
                        id="tax_rate" 
                        value="12.00" 
                        min="0" 
                        max="100" 
                        step="0.01"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500"
                    >
                    <p class="text-xs text-gray-400 mt-1">Default VAT rate is 12%</p>
                </div>
                
                <!-- Notes -->
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-300 mb-2">
                        Notes (Optional)
                    </label>
                    <textarea 
                        name="notes" 
                        id="notes" 
                        rows="3" 
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-green-50 focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Additional notes for this invoice..."
                    ></textarea>
                </div>
                
                <!-- Actions -->
                <div class="flex space-x-3">
                    <button 
                        type="button" 
                        onclick="closeGenerateInvoiceModal()" 
                        class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        id="generateButton"
                        disabled
                        class="flex-1 px-4 py-2 bg-gray-500 text-white rounded-lg cursor-not-allowed"
                    >
                        <i class="fas fa-file-invoice mr-2"></i>
                        Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openGenerateInvoiceModal() {
    document.getElementById('generateInvoiceModal').classList.remove('hidden');
}

function closeGenerateInvoiceModal() {
    document.getElementById('generateInvoiceModal').classList.add('hidden');
    document.getElementById('generateInvoiceForm').reset();
    // Reset form action and button state
    document.getElementById('generateInvoiceForm').action = '';
    updateButtonState();
}

function updateFormAction() {
    const bookingSelect = document.getElementById('booking_id');
    const form = document.getElementById('generateInvoiceForm');
    
    if (bookingSelect.value) {
        // Use the existing route pattern that expects a booking parameter
        form.action = `/bookings/${bookingSelect.value}/invoice/generate`;
    } else {
        form.action = '';
    }
    updateButtonState();
}

function updateButtonState() {
    const bookingSelect = document.getElementById('booking_id');
    const button = document.getElementById('generateButton');
    
    if (bookingSelect.value) {
        button.disabled = false;
        button.className = 'flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors';
    } else {
        button.disabled = true;
        button.className = 'flex-1 px-4 py-2 bg-gray-500 text-white rounded-lg cursor-not-allowed';
    }
}

// Close modal when clicking outside
document.getElementById('generateInvoiceModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeGenerateInvoiceModal();
    }
});
</script>
@endsection
