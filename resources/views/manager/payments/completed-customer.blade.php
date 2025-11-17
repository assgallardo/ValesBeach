@extends('layouts.manager')

@section('content')
<div class="py-12 min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <a href="{{ route('manager.payments.completed') }}" 
                       class="text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-3xl font-bold text-green-50">Completed Payment Details</h1>
                </div>
                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('manager.payments.customer.revert', $customer->id) }}" 
                          onsubmit="return confirm('Are you sure you want to revert this payment transaction for {{ $customer->name }} back to confirmed status? This will move it back to the Payment Management view.')">
                        @csrf
                        <input type="hidden" name="transaction_id" value="{{ request('transaction_id') }}">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                            <i class="fas fa-undo mr-2"></i>
                            Revert to Active
                        </button>
                    </form>
                    @if(request('transaction_id'))
                    <a href="{{ route('manager.payments.customer.invoice', ['user' => $customer->id, 'transaction_id' => request('transaction_id')]) }}" 
                       class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors"
                       onclick="this.classList.add('opacity-50', 'pointer-events-none'); this.innerHTML = '<i class=\"fas fa-spinner fa-spin mr-2\"></i>Loading...';">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>
                        Generate Invoice
                    </a>
                    @else
                    <button type="button" 
                            onclick="alert('No payment transaction selected.');"
                            class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg cursor-not-allowed opacity-60">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>
                        Generate Invoice
                    </button>
                    @endif
                </div>
            </div>
            <p class="text-gray-400">View completed payment transactions for this customer</p>
        </div>

        <!-- Customer Info Card -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 p-6 mb-6">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-green-50 mb-2">{{ $customer->name }}</h2>
                    <p class="text-gray-400 mb-4">{{ $customer->email }}</p>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 text-xs rounded-full bg-blue-600 text-white">
                            {{ ucfirst($customer->role) }}
                        </span>
                        <span class="text-sm text-gray-400">
                            Member since {{ $customer->created_at->format('M d, Y') }}
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-400 mb-1">Total Completed Payments</div>
                    <div class="text-3xl font-bold text-green-400">
                        ₱{{ number_format($customer->payments->sum('amount'), 2) }}
                    </div>
                    <div class="text-sm text-gray-400 mt-1">
                        {{ $customer->payments->count() }} transaction{{ $customer->payments->count() !== 1 ? 's' : '' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Bookings -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-3 bg-blue-600 bg-opacity-20 rounded-lg">
                        <i class="fas fa-bed text-2xl text-blue-400"></i>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400">Bookings</div>
                        <div class="text-sm text-gray-300 font-medium">Room reservations</div>
                    </div>
                </div>
                <div class="text-2xl font-bold text-green-400">
                    ₱{{ number_format($customer->payments->where('booking_id', '!=', null)->sum('amount'), 2) }}
                </div>
                <div class="text-sm text-gray-400 mt-1">
                    {{ $customer->payments->where('booking_id', '!=', null)->count() }} payment{{ $customer->payments->where('booking_id', '!=', null)->count() !== 1 ? 's' : '' }}
                </div>
            </div>

            <!-- Services -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-3 bg-purple-600 bg-opacity-20 rounded-lg">
                        <i class="fas fa-concierge-bell text-2xl text-purple-400"></i>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400">Services</div>
                        <div class="text-sm text-gray-300 font-medium">Additional services</div>
                    </div>
                </div>
                <div class="text-2xl font-bold text-green-400">
                    ₱{{ number_format($customer->payments->where('service_request_id', '!=', null)->sum('amount'), 2) }}
                </div>
                <div class="text-sm text-gray-400 mt-1">
                    {{ $customer->payments->where('service_request_id', '!=', null)->count() }} payment{{ $customer->payments->where('service_request_id', '!=', null)->count() !== 1 ? 's' : '' }}
                </div>
            </div>

            <!-- Food Orders -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-3 bg-orange-600 bg-opacity-20 rounded-lg">
                        <i class="fas fa-utensils text-2xl text-orange-400"></i>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400">Food Orders</div>
                        <div class="text-sm text-gray-300 font-medium">Restaurant orders</div>
                    </div>
                </div>
                <div class="text-2xl font-bold text-green-400">
                    ₱{{ number_format($customer->payments->where('food_order_id', '!=', null)->sum('amount'), 2) }}
                </div>
                <div class="text-sm text-gray-400 mt-1">
                    {{ $customer->payments->where('food_order_id', '!=', null)->count() }} order{{ $customer->payments->where('food_order_id', '!=', null)->count() !== 1 ? 's' : '' }}
                </div>
            </div>

            <!-- Extra Charges -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="p-3 bg-yellow-600 bg-opacity-20 rounded-lg">
                        <i class="fas fa-plus-circle text-2xl text-yellow-400"></i>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400">Extra Charges</div>
                        <div class="text-sm text-gray-300 font-medium">Additional charges</div>
                    </div>
                </div>
                <div class="text-2xl font-bold text-green-400">
                    ₱{{ number_format($customer->payments->whereNull('booking_id')->whereNull('service_request_id')->whereNull('food_order_id')->sum('amount'), 2) }}
                </div>
                <div class="text-sm text-gray-400 mt-1">
                    {{ $customer->payments->whereNull('booking_id')->whereNull('service_request_id')->whereNull('food_order_id')->count() }} charge{{ $customer->payments->whereNull('booking_id')->whereNull('service_request_id')->whereNull('food_order_id')->count() !== 1 ? 's' : '' }}
                </div>
            </div>
        </div>

        <!-- All Payments Table -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-green-50">Completed Payment Transactions</h3>
            </div>

            @if($customer->payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-750">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Payment Ref</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Type & Details</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Method</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($customer->payments as $payment)
                            <tr class="hover:bg-gray-750 transition-colors">
                                <!-- Payment Reference -->
                                <td class="px-4 py-3">
                                    <div class="text-sm text-blue-400 font-mono">
                                        {{ $payment->payment_reference }}
                                    </div>
                                </td>

                                <!-- Type & Details -->
                                <td class="px-4 py-3">
                                    @if($payment->booking)
                                        <div class="flex items-start gap-2">
                                            <i class="fas fa-bed text-blue-400 mt-0.5"></i>
                                            <div>
                                                <div class="font-medium text-green-50 text-sm">Booking</div>
                                                <div class="text-xs text-gray-400">{{ $payment->booking->room->name ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $payment->booking->check_in_date ? \Carbon\Carbon::parse($payment->booking->check_in_date)->format('M d') : '' }} - 
                                                    {{ $payment->booking->check_out_date ? \Carbon\Carbon::parse($payment->booking->check_out_date)->format('M d, Y') : '' }}
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($payment->serviceRequest)
                                        <div class="flex items-start gap-2">
                                            <i class="fas fa-concierge-bell text-purple-400 mt-0.5"></i>
                                            <div>
                                                <div class="font-medium text-green-50 text-sm">Service</div>
                                                <div class="text-xs text-gray-400">{{ $payment->serviceRequest->service->name ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    @elseif($payment->foodOrder)
                                        <div class="flex items-start gap-2">
                                            <i class="fas fa-utensils text-orange-400 mt-0.5"></i>
                                            <div>
                                                <div class="font-medium text-green-50 text-sm">Food Order</div>
                                                <div class="text-xs text-gray-400">Order #{{ $payment->foodOrder->order_number }}</div>
                                                @if($payment->foodOrder->orderItems->count() > 0)
                                                    <div class="text-xs text-gray-500">
                                                        {{ $payment->foodOrder->orderItems->count() }} item{{ $payment->foodOrder->orderItems->count() > 1 ? 's' : '' }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif(!$payment->booking && !$payment->serviceRequest && !$payment->foodOrder)
                                        @php
                                            $paymentDetails = $payment->payment_details ?? [];
                                            $isExtraCharge = isset($paymentDetails['extra_charge']) && $paymentDetails['extra_charge'];
                                            $description = $paymentDetails['description'] ?? 'Extra Charge';
                                            $reference = $paymentDetails['reference'] ?? '';
                                            $details = $paymentDetails['details'] ?? '';
                                        @endphp
                                        <div class="flex items-start gap-2">
                                            <i class="fas fa-receipt text-yellow-400 mt-0.5"></i>
                                            <div>
                                                <div class="font-medium text-green-50 text-sm">Extra Charge</div>
                                                <div class="text-xs text-gray-400">{{ $description }}</div>
                                                @if($reference)
                                                    <div class="text-xs text-gray-500">Ref: {{ $reference }}</div>
                                                @endif
                                                @if($details)
                                                    <div class="text-xs text-gray-500">{{ $details }}</div>
                                                @endif
                                                @if(isset($paymentDetails['invoice_number']))
                                                    <div class="text-xs text-blue-400">Invoice: {{ $paymentDetails['invoice_number'] }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-question-circle text-gray-400"></i>
                                            <span class="text-sm text-gray-400">Other</span>
                                        </div>
                                    @endif
                                </td>

                                <!-- Amount -->
                                <td class="px-4 py-3">
                                    <div class="text-sm font-bold text-green-400">
                                        ₱{{ number_format($payment->amount, 2) }}
                                    </div>
                                    @if($payment->refund_amount > 0)
                                        <div class="text-xs text-red-400">
                                            -₱{{ number_format($payment->refund_amount, 2) }}
                                        </div>
                                        <div class="text-xs font-medium text-gray-300">
                                            Net: ₱{{ number_format($payment->amount - $payment->refund_amount, 2) }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Payment Method -->
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-300">
                                        {{ ucfirst($payment->payment_method ?? 'N/A') }}
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded bg-green-600 bg-opacity-20 text-white border border-green-600 border-opacity-50">
                                        <i class="fas fa-check-circle mr-1"></i>Completed
                                    </span>
                                </td>

                                <!-- Date -->
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-300">
                                        {{ $payment->created_at->format('M d, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $payment->created_at->format('h:i A') }}
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <i class="fas fa-receipt text-6xl text-gray-600 mb-4"></i>
                    <h3 class="text-xl font-semibold text-green-50 mb-2">No completed payments found</h3>
                    <p class="text-gray-400">This customer has no completed payment transactions.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
