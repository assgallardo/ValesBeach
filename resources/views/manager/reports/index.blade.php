@extends('layouts.admin')

@section('title', 'Service Reports Dashboard')

@section('content')
<div class="min-h-screen bg-gray-900 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-green-50">Service Reports Dashboard</h1>
                    <p class="text-gray-400 mt-2">Service usage and performance analytics</p>
                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" 
                            onclick="toggleDateRangeModal()"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Date Range
                    </button>
                    <div class="relative">
                        <button type="button" 
                                onclick="toggleExportDropdown()"
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors w-full sm:w-auto">
                            <i class="fas fa-download mr-2"></i>
                            Export
                            <i class="fas fa-chevron-down ml-2"></i>
                        </button>
                        <div id="exportDropdown" class="absolute right-0 mt-2 w-56 bg-gray-800 border border-gray-700 rounded-lg shadow-lg z-10 hidden">
                            <div class="py-1">
                                <a href="{{ route('manager.reports.export', ['type' => 'overview'] + request()->query()) }}" 
                                   class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-green-400 transition-colors">
                                    <i class="fas fa-file-alt mr-3"></i>Overview Report
                                </a>
                                <a href="{{ route('manager.reports.export', ['type' => 'service-usage'] + request()->query()) }}" 
                                   class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-green-400 transition-colors">
                                    <i class="fas fa-chart-bar mr-3"></i>Service Usage
                                </a>
                                <a href="{{ route('manager.reports.export', ['type' => 'staff-performance'] + request()->query()) }}" 
                                   class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-green-400 transition-colors">
                                    <i class="fas fa-users mr-3"></i>Staff Performance
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access to Sales Reports -->
        <div class="mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('manager.reports.room-sales', request()->query()) }}" 
                   class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg p-6 hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-white font-bold text-lg mb-1">Room Sales Report</h3>
                            <p class="text-blue-100 text-sm">View booking revenue & analytics</p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-door-open text-white text-xl"></i>
                        </div>
                    </div>
                </a>

                <a href="{{ route('manager.reports.food-sales', request()->query()) }}" 
                   class="bg-gradient-to-r from-green-600 to-green-700 rounded-lg p-6 hover:from-green-700 hover:to-green-800 transition-all duration-200 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-white font-bold text-lg mb-1">Food Sales Report</h3>
                            <p class="text-green-100 text-sm">View F&B revenue & analytics</p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-utensils text-white text-xl"></i>
                        </div>
                    </div>
                </a>

                <a href="{{ route('manager.reports.service-sales', request()->query()) }}" 
                   class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-lg p-6 hover:from-purple-700 hover:to-purple-800 transition-all duration-200 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-white font-bold text-lg mb-1">Service Revenue Report</h3>
                            <p class="text-purple-100 text-sm">View service revenue & analytics</p>
                        </div>
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-concierge-bell text-white text-xl"></i>
                        </div>
                    </div>
                </a>
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clipboard-list text-2xl text-white"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-green-50 mb-2">{{ number_format($stats['total_requests']) }}</h2>
                    <p class="text-gray-400 text-sm uppercase tracking-wider font-medium">Total Requests</p>
                </div>
            </div>

            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-2xl text-white"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-green-50 mb-2">{{ number_format($stats['completed_requests']) }}</h2>
                    <p class="text-gray-400 text-sm uppercase tracking-wider font-medium mb-2">Completed</p>
                    @if($stats['total_requests'] > 0)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-600/20 text-green-400">
                            {{ round(($stats['completed_requests'] / $stats['total_requests']) * 100, 1) }}%
                        </span>
                    @endif
                </div>
            </div>

            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-2xl text-white"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-green-50 mb-2">{{ number_format($stats['pending_requests']) }}</h2>
                    <p class="text-gray-400 text-sm uppercase tracking-wider font-medium mb-2">Pending</p>
                    @if($stats['total_requests'] > 0)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-600/20 text-yellow-400">
                            {{ round(($stats['pending_requests'] / $stats['total_requests']) * 100, 1) }}%
                        </span>
                    @endif
                </div>
            </div>

            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tachometer-alt text-2xl text-white"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-green-50 mb-2">{{ round($stats['avg_response_time'], 1) }}h</h2>
                    <p class="text-gray-400 text-sm uppercase tracking-wider font-medium">Avg Response Time</p>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Service Usage Chart -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-green-100 flex items-center">
                        <i class="fas fa-chart-bar text-blue-400 mr-3"></i>
                        Top Services by Usage
                    </h3>
                </div>
                <div class="p-6">
                    <div class="relative" style="height: 300px;">
                        <canvas id="serviceUsageChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Status Distribution Chart -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-green-100 flex items-center">
                        <i class="fas fa-chart-pie text-green-400 mr-3"></i>
                        Request Status Distribution
                    </h3>
                </div>
                <div class="p-6">
                    <div class="relative" style="height: 300px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Trends -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden mb-8">
            <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-green-100 flex items-center">
                    <i class="fas fa-chart-line text-purple-400 mr-3"></i>
                    Daily Request Trends
                </h3>
            </div>
            <div class="p-6">
                <div class="relative" style="height: 250px;">
                    <canvas id="dailyTrendsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Staff Performance Overview -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden mb-8">
            <div class="bg-gray-750 px-6 py-4 border-b border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-green-100 flex items-center">
                    <i class="fas fa-users text-indigo-400 mr-3"></i>
                    Staff Performance Overview
                </h3>
                <a href="{{ route('manager.reports.staff-performance', request()->query()) }}" 
                   class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors">
                    View Details 
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            <div class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-750">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Staff Member
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Assigned Tasks
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Performance
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @forelse($staffPerformance as $staff)
                            <tr class="hover:bg-gray-750 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center mr-4">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                        <span class="text-green-100 font-medium">{{ $staff->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-600/20 text-blue-400">
                                        {{ $staff->assigned_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-1 bg-gray-700 rounded-full h-6 mr-4">
                                            <div class="bg-green-500 h-6 rounded-full flex items-center justify-center text-xs font-medium text-white" 
                                                 style="width: {{ min($staff->assigned_count * 10, 100) }}%;">
                                                @if($staff->assigned_count > 0)
                                                    {{ $staff->assigned_count }} task{{ $staff->assigned_count != 1 ? 's' : '' }}
                                                @endif
                                            </div>
                                        </div>
                                        <span class="text-gray-400 text-sm min-w-12">{{ min($staff->assigned_count * 10, 100) }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <i class="fas fa-users text-4xl text-gray-600 mb-4"></i>
                                    <p class="text-gray-500">No staff performance data available</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
            <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-green-100 flex items-center">
                    <i class="fas fa-file-chart-line text-green-400 mr-3"></i>
                    Detailed Reports
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('manager.reports.service-usage', request()->query()) }}" 
                       class="group block bg-gray-750 rounded-lg border border-gray-600 p-6 hover:bg-gray-700 hover:border-gray-500 transition-all duration-200">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center mr-4 group-hover:bg-blue-500 transition-colors">
                                <i class="fas fa-chart-bar text-white text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-green-100 font-semibold mb-1">Service Usage Report</h4>
                                <p class="text-gray-400 text-sm">Detailed service utilization analysis</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('manager.reports.performance-metrics', request()->query()) }}" 
                       class="group block bg-gray-750 rounded-lg border border-gray-600 p-6 hover:bg-gray-700 hover:border-gray-500 transition-all duration-200">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center mr-4 group-hover:bg-green-500 transition-colors">
                                <i class="fas fa-tachometer-alt text-white text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-green-100 font-semibold mb-1">Performance Metrics</h4>
                                <p class="text-gray-400 text-sm">Response times and efficiency metrics</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('manager.reports.staff-performance', request()->query()) }}" 
                       class="group block bg-gray-750 rounded-lg border border-gray-600 p-6 hover:bg-gray-700 hover:border-gray-500 transition-all duration-200">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center mr-4 group-hover:bg-purple-500 transition-colors">
                                <i class="fas fa-users text-white text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-green-100 font-semibold mb-1">Staff Performance</h4>
                                <p class="text-gray-400 text-sm">Individual staff productivity analysis</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Date Range Modal -->
<div id="dateRangeModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg border border-gray-700 w-full max-w-md">
            <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-green-100">Select Date Range</h3>
                    <button onclick="toggleDateRangeModal()" class="text-gray-400 hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <form method="GET" action="{{ route('manager.reports.index') }}">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Quick Select</label>
                        <select name="period" onchange="toggleCustomDates(this.value)" 
                                class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2 text-gray-300 focus:outline-none focus:border-green-500">
                            <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="yesterday" {{ request('period') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                            <option value="last_7_days" {{ request('period') == 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                            <option value="last_30_days" {{ request('period', 'last_30_days') == 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                            <option value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>This Month</option>
                            <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                            <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>
                    
                    <div id="customDates" class="space-y-4" style="display: {{ request('period') == 'custom' ? 'block' : 'none' }};">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Start Date</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                   class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2 text-gray-300 focus:outline-none focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">End Date</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                   class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2 text-gray-300 focus:outline-none focus:border-green-500">
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-750 px-6 py-4 border-t border-gray-700 flex justify-end space-x-3">
                    <button type="button" onclick="toggleDateRangeModal()"
                            class="bg-gray-700 text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        Apply Filter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart.js defaults for dark theme
Chart.defaults.color = '#9ca3af';
Chart.defaults.borderColor = '#374151';
Chart.defaults.backgroundColor = 'rgba(55, 65, 81, 0.1)';

// Service Usage Chart
const serviceUsageCtx = document.getElementById('serviceUsageChart').getContext('2d');
new Chart(serviceUsageCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($serviceUsage->pluck('name')->take(10)) !!},
        datasets: [{
            label: 'Requests',
            data: {!! json_encode($serviceUsage->pluck('request_count')->take(10)) !!},
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderColor: 'rgba(59, 130, 246, 1)',
            borderWidth: 1,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(17, 24, 39, 0.95)',
                titleColor: '#f3f4f6',
                bodyColor: '#f3f4f6',
                borderColor: '#374151',
                borderWidth: 1,
                cornerRadius: 8,
                padding: 12
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0,
                    color: '#9ca3af'
                },
                grid: {
                    color: '#374151',
                    drawBorder: false
                }
            },
            x: {
                ticks: {
                    color: '#9ca3af'
                },
                grid: {
                    display: false
                }
            }
        }
    }
});

// Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($performanceMetrics->pluck('status')) !!},
        datasets: [{
            data: {!! json_encode($performanceMetrics->pluck('count')) !!},
            backgroundColor: [
                'rgba(239, 68, 68, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(34, 197, 94, 0.8)',
                'rgba(168, 85, 247, 0.8)'
            ],
            borderColor: [
                'rgba(239, 68, 68, 1)',
                'rgba(59, 130, 246, 1)',
                'rgba(245, 158, 11, 1)',
                'rgba(34, 197, 94, 1)',
                'rgba(168, 85, 247, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    padding: 20,
                    color: '#9ca3af',
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(17, 24, 39, 0.95)',
                titleColor: '#f3f4f6',
                bodyColor: '#f3f4f6',
                borderColor: '#374151',
                borderWidth: 1,
                cornerRadius: 8,
                padding: 12,
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += context.parsed;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        label += ' (' + percentage + '%)';
                        return label;
                    }
                }
            }
        }
    }
});

// Daily Trends Chart
const dailyTrendsCtx = document.getElementById('dailyTrendsChart').getContext('2d');
new Chart(dailyTrendsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($dailyTrends->pluck('date')) !!},
        datasets: [{
            label: 'Daily Requests',
            data: {!! json_encode($dailyTrends->pluck('request_count')) !!},
            fill: true,
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            borderColor: 'rgba(34, 197, 94, 1)',
            borderWidth: 3,
            tension: 0.4,
            pointRadius: 6,
            pointHoverRadius: 8,
            pointBackgroundColor: 'rgba(34, 197, 94, 1)',
            pointBorderColor: '#111827',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    color: '#9ca3af',
                    usePointStyle: true
                }
            },
            tooltip: {
                backgroundColor: 'rgba(17, 24, 39, 0.95)',
                titleColor: '#f3f4f6',
                bodyColor: '#f3f4f6',
                borderColor: '#374151',
                borderWidth: 1,
                cornerRadius: 8,
                padding: 12
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0,
                    color: '#9ca3af'
                },
                grid: {
                    color: '#374151',
                    drawBorder: false
                }
            },
            x: {
                ticks: {
                    color: '#9ca3af',
                    maxRotation: 45,
                    minRotation: 0
                },
                grid: {
                    display: false
                }
            }
        }
    }
});

// Modal and dropdown functions
function toggleDateRangeModal() {
    const modal = document.getElementById('dateRangeModal');
    modal.classList.toggle('hidden');
}

function toggleExportDropdown() {
    const dropdown = document.getElementById('exportDropdown');
    dropdown.classList.toggle('hidden');
}

function toggleCustomDates(value) {
    const customDates = document.getElementById('customDates');
    customDates.style.display = value === 'custom' ? 'block' : 'none';
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const exportButton = event.target.closest('[onclick="toggleExportDropdown()"]');
    const exportDropdown = document.getElementById('exportDropdown');
    
    if (!exportButton && !exportDropdown.contains(event.target)) {
        exportDropdown.classList.add('hidden');
    }
});

// Close modal when clicking outside
document.getElementById('dateRangeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        toggleDateRangeModal();
    }
});
</script>
@endpush
@endsection
