@extends('layouts.guest')

@section('title', 'Payment History')

@section('content')
<div class="min-h-screen bg-gray-900 py-6">
    <!-- Decorative Background -->
    <div class="absolute w-96 h-96 bg-green-800 opacity-30 rounded-full blur-3xl -top-48 -left-48"></div>
    <div class="absolute w-80 h-80 bg-green-700 opacity-20 rounded-full blur-3xl top-1/3 right-1/4"></div>
    <div class="absolute w-72 h-72 bg-green-800 opacity-25 rounded-full blur-3xl bottom-1/4 left-1/3"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-600 rounded-full mb-4">
                <i class="fas fa-history text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-green-50 mb-2">Payment History</h1>
            <p class="text-gray-400">View all your payment transactions grouped by booking</p>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-wrap gap-3 mb-6">
            <a href="{{ route('guest.bookings') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                <i class="fas fa-calendar mr-2"></i>My Bookings
            </a>
            <a href="{{ route('invoices.index') }}"
               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors">
                <i class="fas fa-file-invoice mr-2"></i>My Invoices
            </a>
            <a href="{{ route('guest.dashboard') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg font-medium hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Dashboard
            </a>
        </div>

        @if($bookings->isEmpty() && $servicePayments->isEmpty())
            <!-- Empty State -->
            <div class="bg-gray-800 rounded-lg p-8 text-center">
                <i class="fas fa-receipt text-6xl text-gray-600 mb-4"></i>
                <h3 class="text-xl font-semibold text-green-50 mb-2">No Payment History</h3>
                <p class="text-gray-400 mb-6">You haven't made any payments yet. Make a booking to get started!</p>
                <a href="{{ route('guest.rooms.browse') }}"
                   class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors">
                    <i class="fas fa-bed mr-2"></i>Browse Rooms
                </a>
            </div>
        @else
            <!-- Booking Payments (Grouped) -->
            <div class="space-y-6">
                @foreach($bookings as $booking)
                <div class="bg-gray-800 rounded-lg overflow-hidden">
                    <!-- Booking Header -->
                    <div class="bg-gradient-to-r from-gray-700 to-gray-800 p-6 border-b border-gray-700">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <i class="fas fa-bed text-green-400 text-xl"></i>
                                    <h2 class="text-2xl font-bold text-green-50">{{ $booking->room->name }}</h2>
                                </div>
                                <div class="flex flex-wrap gap-4 text-sm text-gray-300">
                                    <div class="flex items-center">
                                        <i class="fas fa-hashtag text-gray-400 mr-2"></i>
                                        <span>{{ $booking->booking_reference }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                        <span>{{ $booking->check_in->format('M d') }} - {{ $booking->check_out->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Payment Status Badge -->
                            <div class="flex flex-col items-end gap-2">
                                @if($booking->remaining_balance <= 0 || $booking->payment_status === 'paid')
                                    <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-green-600 text-white">
                                        <i class="fas fa-check-circle mr-2"></i>FULLY PAID
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold bg-yellow-500 text-black">
                                        <i class="fas fa-exclamation-circle mr-2"></i>PARTIAL PAYMENT
                                    </span>
                                @endif
                                
                                @php
                                    $statusConfig = [
                                        'completed' => ['color' => 'bg-green-600 text-white', 'label' => 'Completed'],
                                        'confirmed' => ['color' => 'bg-blue-600 text-white', 'label' => 'Confirmed'],
                                        'pending' => ['color' => 'bg-gray-500 text-white', 'label' => 'Pending'],
                                        'cancelled' => ['color' => 'bg-red-600 text-white', 'label' => 'Cancelled']
                                    ];
                                    $config = $statusConfig[$booking->status] ?? ['color' => 'bg-gray-500 text-white', 'label' => ucfirst($booking->status)];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded text-xs font-medium {{ $config['color'] }}">
                                    {{ $config['label'] }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="bg-gray-900/50 p-4 border-b border-gray-700">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <p class="text-gray-400 text-xs mb-1">Total Booking</p>
                                <p class="text-green-50 font-bold text-lg">₱{{ number_format($booking->total_price, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 text-xs mb-1">Total Paid</p>
                                <p class="text-green-400 font-bold text-lg">₱{{ number_format($booking->amount_paid, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 text-xs mb-1">Remaining Balance</p>
                                <p class="font-bold text-lg {{ $booking->remaining_balance > 0 ? 'text-yellow-400' : 'text-green-400' }}">
                                    ₱{{ number_format($booking->remaining_balance, 2) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-400 text-xs mb-1">Number of Payments</p>
                                <p class="text-blue-400 font-bold text-lg">{{ $booking->payments->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Individual Payments List -->
                    <div class="p-6">
                        <h3 class="text-sm font-semibold text-gray-400 mb-4 uppercase tracking-wide">
                            <i class="fas fa-list mr-2"></i>Payment Transactions ({{ $booking->payments->count() }})
                        </h3>
                        <div class="space-y-3">
                            @foreach($booking->payments as $payment)
                            <div class="bg-gray-700/50 rounded-lg p-4 hover:bg-gray-700 transition-colors">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <!-- Payment Amount -->
                                            <span class="text-green-400 font-bold text-xl">₱{{ number_format($payment->amount, 2) }}</span>
                                            
                                            <!-- Payment Status -->
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                                {{ $payment->status === 'completed' ? 'bg-green-500 text-white' : 
                                                   ($payment->status === 'pending' ? 'bg-yellow-500 text-black' : 'bg-gray-500 text-white') }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-sm">
                                            <div>
                                                <span class="text-gray-400">Reference:</span>
                                                <span class="text-green-50 ml-1 font-medium">{{ $payment->payment_reference }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-400">Method:</span>
                                                <span class="text-green-50 ml-1">
                                                    @php
                                                        $methodIcons = [
                                                            'cash' => 'money-bill-wave',
                                                            'card' => 'credit-card',
                                                            'gcash' => 'mobile-alt',
                                                            'bank_transfer' => 'university',
                                                            'paymaya' => 'mobile-alt',
                                                            'online' => 'globe'
                                                        ];
                                                        $icon = $methodIcons[$payment->payment_method] ?? 'money-bill';
                                                    @endphp
                                                    <i class="fas fa-{{ $icon }} mr-1"></i>
                                                    {{ $payment->payment_method_display }}
                                                </span>
                                            </div>
                                            <div>
                                                <span class="text-gray-400">Date:</span>
                                                <span class="text-green-50 ml-1">{{ $payment->created_at->format('M d, Y g:i A') }}</span>
                                            </div>
                                        </div>

                                        @if($payment->notes)
                                        <div class="mt-2 text-sm">
                                            <span class="text-gray-400">Notes:</span>
                                            <span class="text-gray-300 ml-1">{{ $payment->notes }}</span>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Payment Actions -->
                                    <div class="flex gap-2">
                                        <a href="{{ route('payments.show', $payment) }}" 
                                           class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors"
                                           title="View Payment Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Booking Action -->
                    <div class="bg-gray-700/30 p-4 border-t border-gray-700">
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('guest.bookings.show', $booking) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-eye mr-2"></i>View Booking Details
                            </a>
                            
                            @if($booking->remaining_balance > 0 && $booking->status !== 'cancelled')
                            <a href="{{ route('payments.create', $booking) }}" 
                               class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700 transition-colors">
                                <i class="fas fa-credit-card mr-2"></i>Pay Remaining Balance
                            </a>
                            @endif
                            
                            @if($booking->invoice)
                            <!-- Invoice exists (ID: {{ $booking->invoice->id }}) -->
                            <a href="{{ route('invoices.show', $booking->invoice) }}" 
                               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition-colors">
                                <i class="fas fa-file-invoice mr-2"></i>View Invoice
                            </a>
                            <a href="{{ route('invoices.download', $booking->invoice) }}" 
                               class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-download mr-2"></i>Download Invoice
                            </a>
                            @else
                            <!-- No invoice yet - showing generate button -->
                            <form action="{{ route('invoices.generate', $booking) }}" method="POST" class="inline" 
                                  onsubmit="return confirm('Generate invoice for booking {{ $booking->booking_reference }}?\n\nBooking ID: {{ $booking->id }}\nTotal Paid: ₱{{ number_format($booking->amount_paid, 2) }}');">
                                @csrf
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition-colors cursor-pointer"
                                        title="Generate invoice for this booking">
                                    <i class="fas fa-file-invoice-dollar mr-2"></i>Generate Invoice
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Service Payments (if any) -->
                @if($servicePayments->isNotEmpty())
                <div class="bg-gray-800 rounded-lg p-6">
                    <h2 class="text-xl font-bold text-green-50 mb-4 flex items-center">
                        <i class="fas fa-concierge-bell text-blue-400 mr-3"></i>
                        Service Payments
                    </h2>
                    <div class="space-y-3">
                        @foreach($servicePayments as $payment)
                        <div class="bg-gray-700/50 rounded-lg p-4">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="text-blue-400 font-bold text-xl">₱{{ number_format($payment->amount, 2) }}</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                            {{ $payment->status === 'completed' ? 'bg-green-500 text-white' : 'bg-gray-500 text-white' }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-sm">
                                        <div>
                                            <span class="text-gray-400">Reference:</span>
                                            <span class="text-green-50 ml-1">{{ $payment->payment_reference }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-400">Method:</span>
                                            <span class="text-green-50 ml-1">{{ $payment->payment_method_display }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-400">Date:</span>
                                            <span class="text-green-50 ml-1">{{ $payment->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <a href="{{ route('payments.show', $payment) }}" 
                                   class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-eye mr-2"></i>View
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Summary Stats -->
            @php
                $totalPaid = $bookings->sum('amount_paid') + $servicePayments->where('status', 'completed')->sum('amount');
                $totalBookings = $bookings->count();
                $fullyPaidBookings = $bookings->where('remaining_balance', '<=', 0)->count();
            @endphp
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-gray-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-money-bill-wave text-green-400 text-2xl mr-4"></i>
                        <div>
                            <p class="text-sm text-gray-400">Total Paid</p>
                            <p class="text-xl font-bold text-green-50">₱{{ number_format($totalPaid, 2) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-bed text-blue-400 text-2xl mr-4"></i>
                        <div>
                            <p class="text-sm text-gray-400">Total Bookings</p>
                            <p class="text-xl font-bold text-green-50">{{ $totalBookings }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-800 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-400 text-2xl mr-4"></i>
                        <div>
                            <p class="text-sm text-gray-400">Fully Paid</p>
                            <p class="text-xl font-bold text-green-50">{{ $fullyPaidBookings }} / {{ $totalBookings }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
