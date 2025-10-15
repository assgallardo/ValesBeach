@extends('layouts.manager')

@section('title', 'Performance Metrics')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manager.reports.index') }}">Reports</a></li>
                            <li class="breadcrumb-item active">Performance Metrics</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0">Performance Metrics Report</h1>
                    <p class="text-muted">Response times and efficiency analysis</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('manager.reports.index', request()->query()) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                    <a href="{{ route('manager.reports.export', ['type' => 'performance-metrics'] + request()->query()) }}" class="btn btn-success">
                        <i class="fas fa-download me-2"></i>Export CSV
                    </a>
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

    <!-- Key Performance Indicators -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <h3 class="mb-1">{{ round($avgResponseTime, 1) }}h</h3>
                    <p class="text-muted mb-0">Average Response Time</p>
                    <small class="text-muted">Time to assign request</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="fas fa-stopwatch fa-2x"></i>
                    </div>
                    <h3 class="mb-1">{{ round($avgCompletionTime, 1) }}h</h3>
                    <p class="text-muted mb-0">Average Completion Time</p>
                    <small class="text-muted">Time from assignment to completion</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="fas fa-tachometer-alt fa-2x"></i>
                    </div>
                    @php 
                        $totalTime = $avgResponseTime + $avgCompletionTime;
                        $efficiency = $totalTime > 0 ? min(100, max(0, 100 - ($totalTime * 2))) : 0;
                    @endphp
                    <h3 class="mb-1">{{ round($efficiency, 1) }}%</h3>
                    <p class="text-muted mb-0">Service Efficiency</p>
                    <small class="text-muted">Based on response + completion time</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </div>
                    @php 
                        $completedCount = $statusMetrics->where('status', 'completed')->first()->count ?? 0;
                        $totalCount = $statusMetrics->sum('count');
                        $completionRate = $totalCount > 0 ? ($completedCount / $totalCount) * 100 : 0;
                    @endphp
                    <h3 class="mb-1">{{ round($completionRate, 1) }}%</h3>
                    <p class="text-muted mb-0">Overall Completion Rate</p>
                    <small class="text-muted">{{ $completedCount }} of {{ $totalCount }} requests</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Breakdown -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="card-title mb-0">Request Status Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Status</th>
                                    <th class="text-center">Count</th>
                                    <th class="text-center">Percentage</th>
                                    <th class="text-center">Avg Duration (Hours)</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($statusMetrics as $status)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @switch($status->status)
                                                @case('completed')
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    @break
                                                @case('pending')
                                                    <i class="fas fa-clock text-warning me-2"></i>
                                                    @break
                                                @case('in_progress')
                                                    <i class="fas fa-spinner text-info me-2"></i>
                                                    @break
                                                @case('cancelled')
                                                    <i class="fas fa-times-circle text-danger me-2"></i>
                                                    @break
                                                @default
                                                    <i class="fas fa-circle text-secondary me-2"></i>
                                            @endswitch
                                            <span class="text-capitalize">{{ str_replace('_', ' ', $status->status) }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ number_format($status->count) }}</span>
                                    </td>
                                    <td class="text-center">
                                        {{ round(($status->count / $statusMetrics->sum('count')) * 100, 1) }}%
                                    </td>
                                    <td class="text-center">
                                        {{ round($status->avg_duration, 1) }}h
                                    </td>
                                    <td>
                                        @php
                                            $percentage = ($status->count / $statusMetrics->sum('count')) * 100;
                                            $barClass = match($status->status) {
                                                'completed' => 'bg-success',
                                                'pending' => 'bg-warning',
                                                'in_progress' => 'bg-info',
                                                'cancelled' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar {{ $barClass }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $percentage }}%"
                                                 title="{{ round($percentage, 1) }}%">
                                                {{ round($percentage, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="card-title mb-0">Status Overview</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Peak Hours Analysis -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="card-title mb-0">Peak Hours Analysis</h5>
                </div>
                <div class="card-body">
                    <canvas id="peakHoursChart" width="400" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trends (if applicable) -->
    @if(!empty($monthlyTrends) && count($monthlyTrends) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="card-title mb-0">Monthly Performance Trends</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendsChart" width="400" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Performance Insights -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="card-title mb-0">Performance Insights & Recommendations</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Response Time Insight -->
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    @if($avgResponseTime < 2)
                                        <i class="fas fa-check-circle text-success fa-2x"></i>
                                    @elseif($avgResponseTime < 6)
                                        <i class="fas fa-exclamation-triangle text-warning fa-2x"></i>
                                    @else
                                        <i class="fas fa-times-circle text-danger fa-2x"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-1">Response Time Analysis</h6>
                                    @if($avgResponseTime < 2)
                                        <p class="text-success mb-0">Excellent response time! Requests are being assigned quickly.</p>
                                    @elseif($avgResponseTime < 6)
                                        <p class="text-warning mb-0">Response time is acceptable but could be improved. Consider optimizing staff allocation.</p>
                                    @else
                                        <p class="text-danger mb-0">Response time needs improvement. Review staffing levels and assignment processes.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Completion Rate Insight -->
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    @if($completionRate > 85)
                                        <i class="fas fa-trophy text-success fa-2x"></i>
                                    @elseif($completionRate > 70)
                                        <i class="fas fa-star text-warning fa-2x"></i>
                                    @else
                                        <i class="fas fa-flag text-danger fa-2x"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-1">Completion Rate Analysis</h6>
                                    @if($completionRate > 85)
                                        <p class="text-success mb-0">Outstanding completion rate! Service delivery is highly effective.</p>
                                    @elseif($completionRate > 70)
                                        <p class="text-warning mb-0">Good completion rate. Monitor cancelled requests to identify improvement areas.</p>
                                    @else
                                        <p class="text-danger mb-0">Completion rate needs attention. Review service processes and staff training.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Peak Hours Insight -->
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="fas fa-chart-line text-info fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Peak Hours Optimization</h6>
                                    @if($peakHours->count() > 0)
                                        @php $peakHour = $peakHours->first(); @endphp
                                        <p class="text-info mb-0">
                                            Peak activity at {{ $peakHour->hour }}:00 with {{ $peakHour->request_count }} requests. 
                                            Consider additional staffing during peak hours.
                                        </p>
                                    @else
                                        <p class="text-muted mb-0">No peak hour data available for the selected period.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Overall Efficiency -->
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    @if($efficiency > 80)
                                        <i class="fas fa-rocket text-success fa-2x"></i>
                                    @elseif($efficiency > 60)
                                        <i class="fas fa-cogs text-warning fa-2x"></i>
                                    @else
                                        <i class="fas fa-wrench text-danger fa-2x"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-1">Service Efficiency</h6>
                                    @if($efficiency > 80)
                                        <p class="text-success mb-0">Highly efficient service delivery. Maintain current processes and standards.</p>
                                    @elseif($efficiency > 60)
                                        <p class="text-warning mb-0">Moderate efficiency. Focus on reducing response and completion times.</p>
                                    @else
                                        <p class="text-danger mb-0">Efficiency needs improvement. Review entire service delivery workflow.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($statusMetrics->pluck('status')->map(function($status) { return ucfirst(str_replace('_', ' ', $status)); })) !!},
        datasets: [{
            data: {!! json_encode($statusMetrics->pluck('count')) !!},
            backgroundColor: [
                'rgba(40, 167, 69, 0.8)',   // completed - green
                'rgba(255, 193, 7, 0.8)',    // pending - yellow
                'rgba(23, 162, 184, 0.8)',   // in_progress - cyan
                'rgba(220, 53, 69, 0.8)',    // cancelled - red
                'rgba(108, 117, 125, 0.8)'   // other - gray
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
                position: 'bottom'
            }
        }
    }
});

// Peak Hours Chart
const peakHoursCtx = document.getElementById('peakHoursChart').getContext('2d');
new Chart(peakHoursCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($peakHours->pluck('hour')->map(function($hour) { return $hour . ':00'; })) !!},
        datasets: [{
            label: 'Requests',
            data: {!! json_encode($peakHours->pluck('request_count')) !!},
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
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Number of Requests'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Hour of Day'
                }
            }
        },
        plugins: {
            title: {
                display: true,
                text: 'Request Distribution by Hour'
            }
        }
    }
});

@if(!empty($monthlyTrends) && count($monthlyTrends) > 0)
// Monthly Trends Chart
const monthlyTrendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
new Chart(monthlyTrendsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyTrends->map(function($trend) { return date('M Y', mktime(0, 0, 0, $trend->month, 1, $trend->year)); })) !!},
        datasets: [{
            label: 'Total Requests',
            data: {!! json_encode($monthlyTrends->pluck('total_requests')) !!},
            fill: false,
            borderColor: 'rgba(54, 162, 235, 1)',
            backgroundColor: 'rgba(54, 162, 235, 0.1)',
            tension: 0.1
        }, {
            label: 'Completed Requests',
            data: {!! json_encode($monthlyTrends->pluck('completed_requests')) !!},
            fill: false,
            borderColor: 'rgba(40, 167, 69, 1)',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
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
@endif
</script>
@endpush

@push('styles')
<style>
.progress {
    margin: 0 auto;
}
.badge {
    font-size: 0.85em;
}
</style>
@endpush
@endsection
