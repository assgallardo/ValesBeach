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

        <!-- Main Content Grid: Tables + Award Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Left Column: Revenue Tables (2/3 width) -->
            <div class="lg:col-span-2 space-y-8">
                
        <!-- Revenue by Rooms -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
            <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-green-100 flex items-center">
                    <i class="fas fa-door-open text-blue-400 mr-3"></i>
                    Revenue by Rooms
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
                        @forelse($revenueByRooms as $room)
                        <tr class="hover:bg-gray-700/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-600/20 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-bed text-blue-400 text-sm"></i>
                                    </div>
                                    <span class="text-gray-200">{{ $room->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-300">{{ number_format($room->booking_count) }}</td>
                            <td class="px-6 py-4 text-green-400 font-semibold">₱{{ number_format($room->total_revenue ?? 0, 2) }}</td>
                            <td class="px-6 py-4 text-gray-300">₱{{ number_format(($room->total_revenue ?? 0) / max($room->booking_count, 1), 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                <i class="fas fa-door-open text-4xl text-gray-600 mb-3"></i>
                                <p>No room booking data available for this period.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Revenue by Cottages -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden mb-8">
            <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-green-100 flex items-center">
                    <i class="fas fa-home text-amber-400 mr-3"></i>
                    Revenue by Cottages
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Cottage Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total Bookings</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total Revenue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Avg Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($revenueByCottages as $cottage)
                        <tr class="hover:bg-gray-700/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-amber-600/20 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-umbrella-beach text-amber-400 text-sm"></i>
                                    </div>
                                    <span class="text-gray-200">{{ $cottage->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-300">{{ number_format($cottage->booking_count) }}</td>
                            <td class="px-6 py-4 text-green-400 font-semibold">₱{{ number_format($cottage->total_revenue ?? 0, 2) }}</td>
                            <td class="px-6 py-4 text-gray-300">₱{{ number_format(($cottage->total_revenue ?? 0) / max($cottage->booking_count, 1), 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                <i class="fas fa-home text-4xl text-gray-600 mb-3"></i>
                                <p>No cottage booking data available for this period.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Revenue by Event and Dining -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden mb-8">
            <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-green-100 flex items-center">
                    <i class="fas fa-utensils text-purple-400 mr-3"></i>
                    Revenue by Event & Dining
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Facility Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total Bookings</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total Revenue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Avg Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($revenueByEventDining as $facility)
                        <tr class="hover:bg-gray-700/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-purple-600/20 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-glass-cheers text-purple-400 text-sm"></i>
                                    </div>
                                    <span class="text-gray-200">{{ $facility->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-300">{{ number_format($facility->booking_count) }}</td>
                            <td class="px-6 py-4 text-green-400 font-semibold">₱{{ number_format($facility->total_revenue ?? 0, 2) }}</td>
                            <td class="px-6 py-4 text-gray-300">₱{{ number_format(($facility->total_revenue ?? 0) / max($facility->booking_count, 1), 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                <i class="fas fa-utensils text-4xl text-gray-600 mb-3"></i>
                                <p>No event & dining booking data available for this period.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

            </div>
            <!-- End of Left Column -->

            <!-- Right Column: Top Performers Award (1/3 width) -->
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-gray-800 via-gray-800 to-gray-900 rounded-2xl border border-yellow-600/30 overflow-hidden shadow-2xl sticky top-6">
                    <!-- Award Header -->
                    <div class="bg-gradient-to-r from-yellow-600 via-amber-500 to-yellow-600 px-6 py-5 text-center relative overflow-hidden">
                        <div class="absolute inset-0 bg-yellow-400/10 animate-pulse"></div>
                        <div class="relative z-10">
                            <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-3 ring-4 ring-white/30">
                                <i class="fas fa-trophy text-3xl text-white"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-1">Top Booked Facilities</h3>
                            <p class="text-yellow-100 text-xs">Most Booked Facilities</p>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Top Rooms -->
                        <div>
                            <div class="flex items-center mb-4 pb-2 border-b border-blue-500/30">
                                <div class="w-8 h-8 bg-blue-600/20 rounded-lg flex items-center justify-center mr-2">
                                    <i class="fas fa-door-open text-blue-400 text-sm"></i>
                                </div>
                                <h4 class="text-sm font-bold text-blue-300 uppercase tracking-wider">Rooms</h4>
                            </div>
                            @forelse($topRooms as $index => $room)
                                <div class="flex items-center mb-3 group hover:bg-gray-700/30 rounded-lg p-2 transition-all duration-200 {{ $index === 0 ? 'bg-yellow-500/10' : '' }}">
                                    <div class="relative">
                                        @if($index === 0)
                                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center mr-3 ring-4 ring-yellow-400/30 shadow-lg">
                                                <i class="fas fa-crown text-white text-lg"></i>
                                            </div>
                                        @elseif($index === 1)
                                            <div class="w-10 h-10 bg-gradient-to-br from-gray-300 to-gray-500 rounded-full flex items-center justify-center mr-3 ring-4 ring-gray-400/30">
                                                <i class="fas fa-medal text-white"></i>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 bg-gradient-to-br from-amber-600 to-amber-800 rounded-full flex items-center justify-center mr-3 ring-4 ring-amber-600/30">
                                                <i class="fas fa-award text-white"></i>
                                            </div>
                                        @endif
                                        <div class="absolute -top-1 -right-1 w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                            {{ $index + 1 }}
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-gray-200 font-medium text-sm truncate">{{ $room->name }}</p>
                                        <div class="flex items-center text-xs text-gray-400 mt-0.5">
                                            <i class="fas fa-calendar-check mr-1"></i>
                                            <span>{{ number_format($room->booking_count) }} booking{{ $room->booking_count != 1 ? 's' : '' }}</span>
                                        </div>
                                    </div>
                                    @if($index === 0)
                                        <i class="fas fa-sparkles text-yellow-400 text-lg ml-2"></i>
                                    @endif
                                </div>
                            @empty
                                <p class="text-gray-500 text-xs text-center py-3">No data available</p>
                            @endforelse
                        </div>

                        <!-- Top Cottages -->
                        <div>
                            <div class="flex items-center mb-4 pb-2 border-b border-amber-500/30">
                                <div class="w-8 h-8 bg-amber-600/20 rounded-lg flex items-center justify-center mr-2">
                                    <i class="fas fa-home text-amber-400 text-sm"></i>
                                </div>
                                <h4 class="text-sm font-bold text-amber-300 uppercase tracking-wider">Cottages</h4>
                            </div>
                            @forelse($topCottages as $index => $cottage)
                                <div class="flex items-center mb-3 group hover:bg-gray-700/30 rounded-lg p-2 transition-all duration-200 {{ $index === 0 ? 'bg-yellow-500/10' : '' }}">
                                    <div class="relative">
                                        @if($index === 0)
                                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center mr-3 ring-4 ring-yellow-400/30 shadow-lg">
                                                <i class="fas fa-crown text-white text-lg"></i>
                                            </div>
                                        @elseif($index === 1)
                                            <div class="w-10 h-10 bg-gradient-to-br from-gray-300 to-gray-500 rounded-full flex items-center justify-center mr-3 ring-4 ring-gray-400/30">
                                                <i class="fas fa-medal text-white"></i>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 bg-gradient-to-br from-amber-600 to-amber-800 rounded-full flex items-center justify-center mr-3 ring-4 ring-amber-600/30">
                                                <i class="fas fa-award text-white"></i>
                                            </div>
                                        @endif
                                        <div class="absolute -top-1 -right-1 w-5 h-5 bg-amber-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                            {{ $index + 1 }}
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-gray-200 font-medium text-sm truncate">{{ $cottage->name }}</p>
                                        <div class="flex items-center text-xs text-gray-400 mt-0.5">
                                            <i class="fas fa-calendar-check mr-1"></i>
                                            <span>{{ number_format($cottage->booking_count) }} booking{{ $cottage->booking_count != 1 ? 's' : '' }}</span>
                                        </div>
                                    </div>
                                    @if($index === 0)
                                        <i class="fas fa-sparkles text-yellow-400 text-lg ml-2"></i>
                                    @endif
                                </div>
                            @empty
                                <p class="text-gray-500 text-xs text-center py-3">No data available</p>
                            @endforelse
                        </div>

                        <!-- Top Event & Dining -->
                        <div>
                            <div class="flex items-center mb-4 pb-2 border-b border-purple-500/30">
                                <div class="w-8 h-8 bg-purple-600/20 rounded-lg flex items-center justify-center mr-2">
                                    <i class="fas fa-utensils text-purple-400 text-sm"></i>
                                </div>
                                <h4 class="text-sm font-bold text-purple-300 uppercase tracking-wider">Event & Dining</h4>
                            </div>
                            @forelse($topEventDining as $index => $facility)
                                <div class="flex items-center mb-3 group hover:bg-gray-700/30 rounded-lg p-2 transition-all duration-200 {{ $index === 0 ? 'bg-yellow-500/10' : '' }}">
                                    <div class="relative">
                                        @if($index === 0)
                                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center mr-3 ring-4 ring-yellow-400/30 shadow-lg">
                                                <i class="fas fa-crown text-white text-lg"></i>
                                            </div>
                                        @elseif($index === 1)
                                            <div class="w-10 h-10 bg-gradient-to-br from-gray-300 to-gray-500 rounded-full flex items-center justify-center mr-3 ring-4 ring-gray-400/30">
                                                <i class="fas fa-medal text-white"></i>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 bg-gradient-to-br from-amber-600 to-amber-800 rounded-full flex items-center justify-center mr-3 ring-4 ring-amber-600/30">
                                                <i class="fas fa-award text-white"></i>
                                            </div>
                                        @endif
                                        <div class="absolute -top-1 -right-1 w-5 h-5 bg-purple-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                            {{ $index + 1 }}
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-gray-200 font-medium text-sm truncate">{{ $facility->name }}</p>
                                        <div class="flex items-center text-xs text-gray-400 mt-0.5">
                                            <i class="fas fa-calendar-check mr-1"></i>
                                            <span>{{ number_format($facility->booking_count) }} booking{{ $facility->booking_count != 1 ? 's' : '' }}</span>
                                        </div>
                                    </div>
                                    @if($index === 0)
                                        <i class="fas fa-sparkles text-yellow-400 text-lg ml-2"></i>
                                    @endif
                                </div>
                            @empty
                                <p class="text-gray-500 text-xs text-center py-3">No data available</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Award Footer -->
                    <div class="bg-gradient-to-r from-gray-900 to-gray-800 px-6 py-3 border-t border-gray-700">
                        <div class="flex items-center justify-center text-xs text-gray-400">
                            <i class="fas fa-star text-yellow-500 mr-2"></i>
                            <span>Based on total bookings in period</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Right Column -->
        </div>
        <!-- End of Grid -->

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
                                    {{ $status->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $status->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $status->status === 'checked_in' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $status->status === 'checked_out' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $status->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $status->status === 'completed' ? 'bg-purple-100 text-purple-800' : '' }}
                                ">
                                    {{ ucfirst(str_replace('_', ' ', $status->status)) }}
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
