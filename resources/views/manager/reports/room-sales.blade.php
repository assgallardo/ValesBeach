@extends('layouts.admin')

@section('title', 'Room Booking Sales Report')

@section('content')
<div class="min-h-screen bg-gray-900 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-green-50">Room Booking Sales Report</h1>
                    <p class="text-gray-400 mt-2">Revenue and booking analytics</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('manager.reports.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg font-medium hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Reports
                    </a>
                </div>
            </div>
        </div>

        <!-- Date Range Display -->
        <div class="mb-8">
            <div class="bg-blue-900/30 border border-blue-600/30 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-400 mr-3 text-lg"></i>
                    <span class="text-blue-100">
                        Showing data from <strong>{{ $startDate->format('M d, Y') }}</strong> to <strong>{{ $endDate->format('M d, Y') }}</strong>
                    </span>
                </div>
            </div>
        </div>

        <!-- Overview Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6 text-center">
                <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clipboard-list text-2xl text-white"></i>
                </div>
                <h2 class="text-3xl font-bold text-green-50 mb-2">{{ number_format($stats['total_bookings']) }}</h2>
                <p class="text-gray-400 text-sm uppercase tracking-wider font-medium">Total Bookings</p>
            </div>

            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6 text-center">
                <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-2xl text-white"></i>
                </div>
                <h2 class="text-3xl font-bold text-green-50 mb-2">{{ number_format($stats['completed_bookings']) }}</h2>
                <p class="text-gray-400 text-sm uppercase tracking-wider font-medium">Completed</p>
            </div>

            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6 text-center">
                <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-times-circle text-2xl text-white"></i>
                </div>
                <h2 class="text-3xl font-bold text-green-50 mb-2">{{ number_format($stats['cancelled_bookings']) }}</h2>
                <p class="text-gray-400 text-sm uppercase tracking-wider font-medium">Cancelled</p>
            </div>

            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6 text-center">
                <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-peso-sign text-2xl text-white"></i>
                </div>
                <h2 class="text-3xl font-bold text-green-50 mb-2">₱{{ number_format($stats['total_revenue'], 2) }}</h2>
                <p class="text-gray-400 text-sm uppercase tracking-wider font-medium">Total Revenue</p>
            </div>

            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6 text-center">
                <div class="w-16 h-16 bg-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-chart-line text-2xl text-white"></i>
                </div>
                <h2 class="text-3xl font-bold text-green-50 mb-2">₱{{ number_format($stats['avg_booking_value'] ?? 0, 2) }}</h2>
                <p class="text-gray-400 text-sm uppercase tracking-wider font-medium">Avg Booking Value</p>
            </div>
        </div>

        <!-- Revenue by Room -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden mb-8">
            <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-green-100 flex items-center">
                    <i class="fas fa-door-open text-blue-400 mr-3"></i>
                    Revenue by Room
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Room Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total Bookings</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total Revenue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Avg Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($revenueByRoom as $room)
                        <tr class="hover:bg-gray-700/50">
                            <td class="px-6 py-4 text-gray-200">{{ $room->name }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ number_format($room->booking_count) }}</td>
                            <td class="px-6 py-4 text-green-400 font-semibold">₱{{ number_format($room->total_revenue ?? 0, 2) }}</td>
                            <td class="px-6 py-4 text-gray-300">₱{{ number_format(($room->total_revenue ?? 0) / max($room->booking_count, 1), 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">No booking data available for this period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Status Breakdown -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden mb-8">
            <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-green-100 flex items-center">
                    <i class="fas fa-chart-pie text-green-400 mr-3"></i>
                    Booking Status Breakdown
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Count</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Revenue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Percentage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($statusBreakdown as $status)
                        <tr class="hover:bg-gray-700/50">
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    {{ $status->status === 'completed' ? 'bg-green-600/20 text-green-400' : '' }}
                                    {{ $status->status === 'pending' ? 'bg-yellow-600/20 text-yellow-400' : '' }}
                                    {{ $status->status === 'cancelled' ? 'bg-red-600/20 text-red-400' : '' }}
                                    {{ $status->status === 'confirmed' ? 'bg-blue-600/20 text-blue-400' : '' }}
                                ">
                                    {{ ucfirst($status->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-300">{{ number_format($status->count) }}</td>
                            <td class="px-6 py-4 text-green-400 font-semibold">₱{{ number_format($status->revenue ?? 0, 2) }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ number_format(($status->count / max($stats['total_bookings'], 1)) * 100, 1) }}%</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">No status data available.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
