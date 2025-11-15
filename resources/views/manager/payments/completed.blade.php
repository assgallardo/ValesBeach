@extends('layouts.manager')

@section('content')
<div class="py-12 min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <a href="{{ route('manager.payments.index') }}" 
                       class="text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-3xl font-bold text-green-50">Completed Transactions</h1>
                </div>
            </div>
            <p class="text-gray-400">View all completed payment transactions</p>
        </div>

        @if($customers->count() > 0)
            <!-- Customer Payments Table -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-lg font-semibold text-green-50">Completed Payment Transactions</h3>
                    <p class="text-sm text-gray-400 mt-2 sm:mt-0">
                        Showing 1 to {{ $customers->count() }} of {{ $customers->count() }} customers
                    </p>
                </div>

                <div>
                    <table class="w-full table-fixed">
                        <thead class="bg-gray-750">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase w-[20%]">Guest</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase w-[15%]">Types</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase w-[12%]">Total Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase w-[10%]">Payments</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase w-[15%]">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase w-[13%]">Latest Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase w-[15%]">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($customers as $customer)
                                @php
                                    $bookingPayments = $customer->payments->filter(fn($p) => $p->booking_id);
                                    $servicePayments = $customer->payments->filter(fn($p) => $p->service_request_id);
                                    $foodPayments = $customer->payments->filter(fn($p) => $p->food_order_id);
                                    $totalAmount = $customer->payments->sum('amount');
                                    $latestPayment = $customer->payments->first();
                                    
                                    // Group payments by status
                                    $statusGroups = $customer->payments->groupBy('status');
                                    $completedCount = $statusGroups->get('completed', collect())->count();
                                @endphp
                            <tr class="hover:bg-gray-750 transition-colors">
                                <!-- Guest Info -->
                                <td class="px-4 py-3">
                                    <div class="font-medium text-green-50 text-sm truncate" title="{{ $customer->name }}">
                                        {{ $customer->name }}
                                    </div>
                                    <div class="text-xs text-gray-400 truncate" title="{{ $customer->email }}">
                                        {{ $customer->email }}
                                    </div>
                                </td>

                                <!-- Payment Types -->
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        @if($bookingPayments->count() > 0)
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-bed text-blue-400 text-xs"></i>
                                                <span class="text-xs text-gray-300">{{ $bookingPayments->count() }} Booking{{ $bookingPayments->count() > 1 ? 's' : '' }}</span>
                                            </div>
                                        @endif
                                        @if($servicePayments->count() > 0)
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-concierge-bell text-purple-400 text-xs"></i>
                                                <span class="text-xs text-gray-300">{{ $servicePayments->count() }} Service{{ $servicePayments->count() > 1 ? 's' : '' }}</span>
                                            </div>
                                        @endif
                                        @if($foodPayments->count() > 0)
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-utensils text-orange-400 text-xs"></i>
                                                <span class="text-xs text-gray-300">{{ $foodPayments->count() }} Food Order{{ $foodPayments->count() > 1 ? 's' : '' }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Total Amount -->
                                <td class="px-4 py-3">
                                    <div class="text-sm font-bold text-green-400">
                                        ₱{{ number_format($totalAmount, 2) }}
                                    </div>
                                </td>

                                <!-- Number of Payments -->
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-300">
                                        {{ $customer->payments->count() }} payment{{ $customer->payments->count() > 1 ? 's' : '' }}
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        @if($completedCount > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-600 text-white">
                                                {{ $completedCount }} Completed
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Latest Date -->
                                <td class="px-4 py-3">
                                    @if($latestPayment)
                                        <div class="text-sm text-green-50">
                                            {{ $latestPayment->created_at->format('M d, Y') }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ $latestPayment->created_at->format('h:i A') }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-3">
                                    <a href="{{ route('manager.payments.completed.customer', ['user' => $customer->id, 'transaction_id' => $customer->payment_transaction_id]) }}" 
                                       class="inline-flex items-center px-3 py-1.5 text-xs bg-green-600 text-white rounded hover:bg-green-700 transition-colors" 
                                       title="View All Payments">
                                        <i class="fas fa-eye mr-1"></i> View Details
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-12 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-green-600/20 rounded-full mb-4">
                    <i class="fas fa-check-circle text-4xl text-green-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-green-50 mb-2">No Completed Transactions</h3>
                <p class="text-gray-400 mb-6">Completed payment transactions will appear here.</p>
                <a href="{{ route('manager.payments.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-gray-700 text-white rounded-lg font-medium hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Payments
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
