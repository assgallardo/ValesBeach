@extends('layouts.manager')

@section('title', 'Service Reports Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="mb-3 mb-md-0">
                    <h1 class="h2 mb-2 fw-bold text-dark">Service Reports Dashboard</h1>
                    <p class="text-muted mb-0" style="font-size: 0.95rem;">Service usage and performance analytics</p>
                </div>
                <div class="d-flex align-items-center" style="gap: 0.5rem;">
                    <button type="button" class="btn btn-outline-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#dateRangeModal">
                        <i class="fas fa-calendar-alt me-2"></i>
                        <span>Date Range</span>
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-success dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-2"></i>
                            <span>Export</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('manager.reports.export', ['type' => 'overview'] + request()->query()) }}">
                                <i class="fas fa-file-alt me-2"></i>Overview Report
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('manager.reports.export', ['type' => 'service-usage'] + request()->query()) }}">
                                <i class="fas fa-chart-bar me-2"></i>Service Usage
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('manager.reports.export', ['type' => 'staff-performance'] + request()->query()) }}">
                                <i class="fas fa-users me-2"></i>Staff Performance
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Display -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info d-flex align-items-center mb-0" style="background-color: #e7f3ff; border-color: #b3d9ff; color: #004085;">
                <i class="fas fa-info-circle me-3" style="font-size: 1.25rem;"></i>
                <span style="font-size: 0.95rem;">
                    Showing data from <strong>{{ $startDate->format('M d, Y') }}</strong> to <strong>{{ $endDate->format('M d, Y') }}</strong>
                </span>
            </div>
        </div>
    </div>

    <!-- Overview Statistics -->
    <div class="row mb-4 g-3">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="text-primary mb-3">
                        <i class="fas fa-clipboard-list" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="mb-2 fw-bold" style="font-size: 2rem; color: #212529;">{{ number_format($stats['total_requests']) }}</h2>
                    <p class="text-muted mb-0 text-uppercase" style="font-size: 0.85rem; font-weight: 600; letter-spacing: 0.5px;">Total Requests</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="text-success mb-3">
                        <i class="fas fa-check-circle" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="mb-2 fw-bold" style="font-size: 2rem; color: #212529;">{{ number_format($stats['completed_requests']) }}</h2>
                    <p class="text-muted mb-1 text-uppercase" style="font-size: 0.85rem; font-weight: 600; letter-spacing: 0.5px;">Completed</p>
                    @if($stats['total_requests'] > 0)
                        <small class="badge bg-success-subtle text-success px-2 py-1" style="font-size: 0.75rem;">
                            {{ round(($stats['completed_requests'] / $stats['total_requests']) * 100, 1) }}%
                        </small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="text-warning mb-3">
                        <i class="fas fa-clock" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="mb-2 fw-bold" style="font-size: 2rem; color: #212529;">{{ number_format($stats['pending_requests']) }}</h2>
                    <p class="text-muted mb-1 text-uppercase" style="font-size: 0.85rem; font-weight: 600; letter-spacing: 0.5px;">Pending</p>
                    @if($stats['total_requests'] > 0)
                        <small class="badge bg-warning-subtle text-warning px-2 py-1" style="font-size: 0.75rem;">
                            {{ round(($stats['pending_requests'] / $stats['total_requests']) * 100, 1) }}%
                        </small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="text-info mb-3">
                        <i class="fas fa-tachometer-alt" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="mb-2 fw-bold" style="font-size: 2rem; color: #212529;">{{ round($stats['avg_response_time'], 1) }}h</h2>
                    <p class="text-muted mb-0 text-uppercase" style="font-size: 0.85rem; font-weight: 600; letter-spacing: 0.5px;">Avg Response Time</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Service Usage Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold text-dark" style="font-size: 1.1rem;">
                        <i class="fas fa-chart-bar text-primary me-2"></i>Top Services by Usage
                    </h5>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px;">
                        <canvas id="serviceUsageChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Distribution Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold text-dark" style="font-size: 1.1rem;">
                        <i class="fas fa-chart-pie text-success me-2"></i>Request Status Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Trends -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold text-dark" style="font-size: 1.1rem;">
                        <i class="fas fa-chart-line text-info me-2"></i>Daily Request Trends
                    </h5>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 250px;">
                        <canvas id="dailyTrendsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Performance Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-semibold text-dark" style="font-size: 1.1rem;">
                        <i class="fas fa-users text-secondary me-2"></i>Staff Performance Overview
                    </h5>
                    <a href="{{ route('manager.reports.staff-performance', request()->query()) }}" class="btn btn-sm btn-outline-primary">
                        View Details <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th class="py-3 px-4 text-uppercase fw-semibold" style="font-size: 0.8rem; color: #6c757d; letter-spacing: 0.5px;">Staff Member</th>
                                    <th class="py-3 px-4 text-uppercase fw-semibold text-center" style="font-size: 0.8rem; color: #6c757d; letter-spacing: 0.5px;">Assigned Tasks</th>
                                    <th class="py-3 px-4 text-uppercase fw-semibold" style="font-size: 0.8rem; color: #6c757d; letter-spacing: 0.5px;">Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staffPerformance as $staff)
                                <tr>
                                    <td class="py-3 px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-muted"></i>
                                            </div>
                                            <span class="fw-medium" style="font-size: 0.95rem; color: #212529;">{{ $staff->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="badge bg-info px-3 py-2" style="font-size: 0.85rem; font-weight: 600;">{{ $staff->assigned_count }}</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 28px; background-color: #e9ecef;">
                                                <div class="progress-bar bg-success d-flex align-items-center justify-content-center" 
                                                     role="progressbar" 
                                                     style="width: {{ min($staff->assigned_count * 10, 100) }}%; font-size: 0.85rem; font-weight: 600;">
                                                    @if($staff->assigned_count > 0)
                                                        {{ $staff->assigned_count }} task{{ $staff->assigned_count != 1 ? 's' : '' }}
                                                    @endif
                                                </div>
                                            </div>
                                            <span class="ms-3 text-muted" style="font-size: 0.85rem; min-width: 40px;">{{ min($staff->assigned_count * 10, 100) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-5">
                                        <i class="fas fa-users fa-3x mb-3 d-block" style="opacity: 0.3;"></i>
                                        <p class="mb-0" style="font-size: 0.95rem;">No staff performance data available</p>
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
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold text-dark" style="font-size: 1.1rem;">
                        <i class="fas fa-file-chart-line text-primary me-2"></i>Detailed Reports
                    </h5>
                </div>
                <div class="card-body py-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('manager.reports.service-usage', request()->query()) }}" class="text-decoration-none">
                                <div class="d-flex align-items-center p-4 bg-light rounded border border-light hover-shadow" style="transition: all 0.3s ease;">
                                    <div class="me-3">
                                        <i class="fas fa-chart-bar text-primary" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-semibold text-dark" style="font-size: 1rem;">Service Usage Report</h6>
                                        <small class="text-muted" style="font-size: 0.85rem;">Detailed service utilization analysis</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('manager.reports.performance-metrics', request()->query()) }}" class="text-decoration-none">
                                <div class="d-flex align-items-center p-4 bg-light rounded border border-light hover-shadow" style="transition: all 0.3s ease;">
                                    <div class="me-3">
                                        <i class="fas fa-tachometer-alt text-success" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-semibold text-dark" style="font-size: 1rem;">Performance Metrics</h6>
                                        <small class="text-muted" style="font-size: 0.85rem;">Response times and efficiency metrics</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('manager.reports.staff-performance', request()->query()) }}" class="text-decoration-none">
                                <div class="d-flex align-items-center p-4 bg-light rounded border border-light hover-shadow" style="transition: all 0.3s ease;">
                                    <div class="me-3">
                                        <i class="fas fa-users text-info" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-semibold text-dark" style="font-size: 1rem;">Staff Performance</h6>
                                        <small class="text-muted" style="font-size: 0.85rem;">Individual staff productivity analysis</small>
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
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: {
                    size: 14
                },
                bodyFont: {
                    size: 13
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0,
                    font: {
                        size: 12
                    }
                },
                grid: {
                    display: true,
                    drawBorder: false
                }
            },
            x: {
                ticks: {
                    font: {
                        size: 11
                    }
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
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 205, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)'
            ],
            borderWidth: 2,
            borderColor: '#fff'
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
                    padding: 15,
                    font: {
                        size: 12
                    },
                    usePointStyle: true
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: {
                    size: 14
                },
                bodyFont: {
                    size: 13
                },
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
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 2,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: 'rgba(75, 192, 192, 1)',
            pointBorderColor: '#fff',
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
                    font: {
                        size: 12
                    },
                    usePointStyle: true
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: {
                    size: 14
                },
                bodyFont: {
                    size: 13
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0,
                    font: {
                        size: 12
                    }
                },
                grid: {
                    display: true,
                    drawBorder: false
                }
            },
            x: {
                ticks: {
                    font: {
                        size: 11
                    },
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

// Toggle custom date inputs
function toggleCustomDates(value) {
    const customDates = document.getElementById('customDates');
    customDates.style.display = value === 'custom' ? 'block' : 'none';
}
</script>
@endpush

@push('styles')
<style>
/* Avatar Styling */
.avatar-sm {
    width: 40px;
    height: 40px;
    flex-shrink: 0;
}

/* Card Enhancements */
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid rgba(0,0,0,.05);
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
}

/* Statistics Cards Special Hover */
.row.g-3 > div > .card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0.75rem 2rem rgba(0, 0, 0, 0.12) !important;
}

/* Table Improvements */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table > :not(caption) > * > * {
    padding: 0.75rem 1rem;
}

.table-hover tbody tr {
    transition: background-color 0.15s ease;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

/* Progress Bar */
.progress {
    background-color: #e9ecef;
    border-radius: 0.5rem;
}

.progress-bar {
    border-radius: 0.5rem;
    transition: width 0.6s ease;
}

/* Typography */
h1, h2, h3, h4, h5, h6 {
    font-weight: 600;
    line-height: 1.3;
}

.text-muted {
    color: #6c757d !important;
}

/* Button Improvements */
.btn {
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

/* Badge Improvements */
.badge {
    font-weight: 600;
    padding: 0.35em 0.65em;
}

/* Quick Actions Hover */
.hover-shadow {
    transition: all 0.3s ease !important;
}

.hover-shadow:hover {
    background-color: #ffffff !important;
    box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1) !important;
    border-color: rgba(0, 0, 0, 0.1) !important;
    transform: translateY(-2px);
}

/* Alert Box */
.alert {
    border-radius: 0.5rem;
    font-weight: 500;
}

/* Card Header */
.card-header {
    background-color: #ffffff;
    border-bottom: 1px solid rgba(0,0,0,.05);
}

/* Dropdown Menu */
.dropdown-menu {
    border: 1px solid rgba(0,0,0,.1);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.dropdown-item {
    padding: 0.5rem 1.25rem;
    transition: background-color 0.15s ease;
}

.dropdown-item:hover {
    background-color: rgba(0, 0, 0, 0.04);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    h1.h2 {
        font-size: 1.5rem !important;
    }
    
    h2 {
        font-size: 1.5rem !important;
    }
}

/* Smooth Scrolling */
html {
    scroll-behavior: smooth;
}
</style>
@endpush
@endsection
