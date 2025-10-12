@extends('layouts.app')

@section('title', 'Service Reports Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Service Reports Dashboard</h1>
                    <p class="text-muted">Service usage and performance analytics</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#dateRangeModal">
                        <i class="fas fa-calendar-alt me-2"></i>Date Range
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-2"></i>Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('manager.reports.export', ['type' => 'overview'] + request()->query()) }}">Overview Report</a></li>
                            <li><a class="dropdown-item" href="{{ route('manager.reports.export', ['type' => 'service-usage'] + request()->query()) }}">Service Usage</a></li>
                            <li><a class="dropdown-item" href="{{ route('manager.reports.export', ['type' => 'staff-performance'] + request()->query()) }}">Staff Performance</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Display -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info d-flex align-items-center">
                <i class="fas fa-info-circle me-2"></i>
                <span>Showing data from <strong>{{ $startDate->format('M d, Y') }}</strong> to <strong>{{ $endDate->format('M d, Y') }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Overview Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="fas fa-clipboard-list fa-2x"></i>
                    </div>
                    <h3 class="mb-1">{{ number_format($stats['total_requests']) }}</h3>
                    <p class="text-muted mb-0">Total Requests</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <h3 class="mb-1">{{ number_format($stats['completed_requests']) }}</h3>
                    <p class="text-muted mb-0">Completed</p>
                    @if($stats['total_requests'] > 0)
                        <small class="text-success">{{ round(($stats['completed_requests'] / $stats['total_requests']) * 100, 1) }}%</small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <h3 class="mb-1">{{ number_format($stats['pending_requests']) }}</h3>
                    <p class="text-muted mb-0">Pending</p>
                    @if($stats['total_requests'] > 0)
                        <small class="text-warning">{{ round(($stats['pending_requests'] / $stats['total_requests']) * 100, 1) }}%</small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="fas fa-tachometer-alt fa-2x"></i>
                    </div>
                    <h3 class="mb-1">{{ round($stats['avg_response_time'], 1) }}h</h3>
                    <p class="text-muted mb-0">Avg Response Time</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Service Usage Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="card-title mb-0">Top Services by Usage</h5>
                </div>
                <div class="card-body">
                    <canvas id="serviceUsageChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Distribution Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="card-title mb-0">Request Status Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Trends -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="card-title mb-0">Daily Request Trends</h5>
                </div>
                <div class="card-body">
                    <canvas id="dailyTrendsChart" width="400" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Performance Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Staff Performance Overview</h5>
                    <a href="{{ route('manager.reports.staff-performance', request()->query()) }}" class="btn btn-sm btn-outline-primary">
                        View Details <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Staff Member</th>
                                    <th>Assigned Tasks</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staffPerformance as $staff)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <i class="fas fa-user text-muted"></i>
                                            </div>
                                            {{ $staff->name }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $staff->assigned_count }}</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: {{ min($staff->assigned_count * 10, 100) }}%">
                                                {{ $staff->assigned_count }} tasks
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="fas fa-users fa-2x mb-2 d-block"></i>
                                        No staff performance data available
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="card-title mb-0">Detailed Reports</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('manager.reports.service-usage', request()->query()) }}" class="text-decoration-none">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <i class="fas fa-chart-bar text-primary fa-2x me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Service Usage Report</h6>
                                        <small class="text-muted">Detailed service utilization analysis</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('manager.reports.performance-metrics', request()->query()) }}" class="text-decoration-none">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <i class="fas fa-tachometer-alt text-success fa-2x me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Performance Metrics</h6>
                                        <small class="text-muted">Response times and efficiency metrics</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('manager.reports.staff-performance', request()->query()) }}" class="text-decoration-none">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <i class="fas fa-users text-info fa-2x me-3"></i>
                                    <div>
                                        <h6 class="mb-1">Staff Performance</h6>
                                        <small class="text-muted">Individual staff productivity analysis</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Date Range Modal -->
<div class="modal fade" id="dateRangeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="GET" action="{{ route('manager.reports.index') }}">
                <div class="modal-header">
                    <h5 class="modal-title">Select Date Range</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Quick Select</label>
                        <select name="period" class="form-select" onchange="toggleCustomDates(this.value)">
                            <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="yesterday" {{ request('period') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                            <option value="last_7_days" {{ request('period') == 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
                            <option value="last_30_days" {{ request('period', 'last_30_days') == 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                            <option value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>This Month</option>
                            <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                            <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>
                    <div id="customDates" style="display: {{ request('period') == 'custom' ? 'block' : 'none' }};">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Service Usage Chart
const serviceUsageCtx = document.getElementById('serviceUsageChart').getContext('2d');
new Chart(serviceUsageCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($serviceUsage->pluck('name')->take(10)) !!},
        datasets: [{
            label: 'Requests',
            data: {!! json_encode($serviceUsage->pluck('request_count')->take(10)) !!},
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
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
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 205, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
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
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Toggle custom date inputs
function toggleCustomDates(value) {
    const customDates = document.getElementById('customDates');
    customDates.style.display = value === 'custom' ? 'block' : 'none';
}
</script>
@endpush

@push('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
}
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
}
</style>
@endpush
@endsection
