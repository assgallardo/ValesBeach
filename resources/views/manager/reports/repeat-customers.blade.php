@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <a href="{{ route($routePrefix . '.reports.index', request()->query()) }}" class="text-cyan-400 hover:text-cyan-300 mb-2 inline-flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Reports
                    </a>
                    <h1 class="text-3xl font-bold text-white mt-2">
                        <i class="fas fa-users mr-3 text-cyan-400"></i>Customer Reports
                    </h1>
                    <p class="text-gray-400 mt-1">Customer data, transactions, and payment analytics</p>
                </div>
            </div>

            <!-- Date Range Display -->
            <div class="bg-cyan-900/30 border border-cyan-600/30 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-calendar text-cyan-400 mr-3 text-lg"></i>
                    <span class="text-cyan-100">
                        Showing data from <strong>{{ $startDate->format('M d, Y') }}</strong> to <strong>{{ $endDate->format('M d, Y') }}</strong>
                    </span>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-blue-600/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-400 text-xl"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-white mb-1">{{ number_format($stats['total_customers']) }}</h2>
                <p class="text-gray-400 text-xs uppercase tracking-wider">Total Customers</p>
            </div>

            <div class="bg-gray-800 rounded-lg border border-gray-700 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-green-600/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-check text-green-400 text-xl"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-white mb-1">{{ number_format($stats['repeat_customers']) }}</h2>
                <p class="text-gray-400 text-xs uppercase tracking-wider">Repeat Customers</p>
            </div>

            <div class="bg-gray-800 rounded-lg border border-gray-700 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-yellow-600/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user text-yellow-400 text-xl"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-white mb-1">{{ number_format($stats['one_time_customers']) }}</h2>
                <p class="text-gray-400 text-xs uppercase tracking-wider">One-Time Customers</p>
            </div>

            <div class="bg-gray-800 rounded-lg border border-gray-700 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-purple-600/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-purple-400 text-xl"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-white mb-1">{{ $stats['avg_bookings_per_customer'] }}</h2>
                <p class="text-gray-400 text-xs uppercase tracking-wider">Avg Bookings/Customer</p>
            </div>

            <div class="bg-gradient-to-br from-cyan-900/40 to-cyan-800/30 rounded-lg border-2 border-cyan-500/50 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-cyan-600/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-percentage text-cyan-400 text-xl"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-cyan-400 mb-1">{{ $stats['retention_rate'] }}%</h2>
                <p class="text-cyan-200 text-xs uppercase tracking-wider font-semibold">Retention Rate</p>
            </div>
        </div>

        <!-- All Customers Table -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-blue-900/30 to-blue-800/20 px-6 py-4 border-b border-gray-700">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-600/30 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-users text-blue-400 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">All Customers</h3>
                        <p class="text-gray-400 text-xs">Complete customer transaction history</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Total Bookings</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Total Spent</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Payment Methods</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Last Booking</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @php
                            $allCustomers = \App\Models\User::where('role', 'guest')
                                ->withCount(['bookings' => function($q) use ($startDate, $endDate) {
                                    $q->whereBetween('created_at', [$startDate, $endDate]);
                                }])
                                ->with(['bookings' => function($q) use ($startDate, $endDate) {
                                    $q->whereBetween('created_at', [$startDate, $endDate])
                                      ->with('payments')
                                      ->latest();
                                }])
                                ->having('bookings_count', '>', 0)
                                ->get();
                        @endphp

                        @forelse($allCustomers as $customer)
                        @php
                            $totalSpent = $customer->bookings->sum('total_price');
                            $paymentMethods = $customer->bookings->flatMap->payments->pluck('payment_method')->unique()->filter();
                            $lastBooking = $customer->bookings->first();
                            $isRepeatCustomer = $customer->bookings_count >= 2;
                        @endphp
                        <tr class="hover:bg-gray-700/50 transition-colors {{ $isRepeatCustomer ? 'bg-cyan-900/10 border-l-4 border-cyan-500' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 {{ $isRepeatCustomer ? 'bg-cyan-600/20' : 'bg-blue-600/20' }} rounded-full flex items-center justify-center mr-3">
                                        <i class="fas {{ $isRepeatCustomer ? 'fa-user-check text-cyan-400' : 'fa-user text-blue-400' }}"></i>
                                    </div>
                                    <div>
                                        <div class="text-white font-medium">{{ $customer->name }}</div>
                                        @if($isRepeatCustomer)
                                            <span class="text-xs text-cyan-400 font-semibold">Repeat Customer</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-400 text-sm">
                                {{ $customer->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 {{ $isRepeatCustomer ? 'bg-cyan-900/30 text-cyan-400 border-cyan-600/30' : 'bg-blue-900/30 text-blue-400 border-blue-600/30' }} text-sm font-semibold rounded-full border">
                                    {{ $customer->bookings_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-white font-semibold">₱{{ number_format($totalSpent, 2) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($paymentMethods as $method)
                                        <span class="px-2 py-1 bg-green-900/30 text-green-400 text-xs rounded border border-green-600/30">
                                            {{ ucfirst(str_replace('_', ' ', $method)) }}
                                        </span>
                                    @empty
                                        <span class="text-gray-500 text-xs">No payments</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-400 text-sm">
                                {{ $lastBooking ? $lastBooking->created_at->format('M d, Y') : 'N/A' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-users text-5xl mb-4 block"></i>
                                <p class="text-lg">No customers found for the selected period</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Repeat Customers Sub-Section -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-cyan-900/30 to-cyan-800/20 px-6 py-4 border-b border-gray-700">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-cyan-600/30 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user-check text-cyan-400 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Repeat Customers</h3>
                        <p class="text-gray-400 text-xs">Customers with 2 or more bookings</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Total Bookings</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Completed</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Total Spent</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Payment Methods</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($repeatCustomers as $customer)
                        @php
                            $customerPaymentMethods = \App\Models\Payment::where('user_id', $customer->id)
                                ->whereHas('booking', function($q) use ($startDate, $endDate) {
                                    $q->whereBetween('created_at', [$startDate, $endDate]);
                                })
                                ->pluck('payment_method')
                                ->unique()
                                ->filter();
                        @endphp
                        <tr class="hover:bg-gray-700/50 transition-colors bg-cyan-900/10 border-l-4 border-cyan-500">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-cyan-600/20 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user-check text-cyan-400"></i>
                                    </div>
                                    <div class="text-white font-medium">{{ $customer->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-400 text-sm">
                                {{ $customer->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 bg-cyan-900/30 text-cyan-400 text-sm font-semibold rounded-full border border-cyan-600/30">
                                    {{ $customer->total_bookings }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 bg-green-900/30 text-green-400 text-sm font-semibold rounded-full border border-green-600/30">
                                    {{ $customer->completed_bookings }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-white font-semibold">₱{{ number_format($customer->total_spent ?? 0, 2) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($customerPaymentMethods as $method)
                                        <span class="px-2 py-1 bg-purple-900/30 text-purple-400 text-xs rounded border border-purple-600/30">
                                            {{ ucfirst(str_replace('_', ' ', $method)) }}
                                        </span>
                                    @empty
                                        <span class="text-gray-500 text-xs">No payments</span>
                                    @endforelse
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-user-check text-5xl mb-4 block text-cyan-600/30"></i>
                                <p class="text-lg">No repeat customers found for the selected period</p>
                                <p class="text-sm mt-2">Repeat customers will appear here when a customer makes 2 or more bookings</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($repeatCustomers->hasPages())
            <div class="px-6 py-4 border-t border-gray-700">
                {{ $repeatCustomers->links() }}
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
