@extends('layouts.app')

@section('title', 'Staff Performance Report')

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
                            <li class="breadcrumb-item active">Staff Performance</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0">Staff Performance Report</h1>
                    <p class="text-muted">Individual staff productivity and performance analysis</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('manager.reports.index', request()->query()) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                    <a href="{{ route('manager.reports.export', ['type' => 'staff-performance'] + request()->query()) }}" class="btn btn-success">
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

    <!-- Team Overview -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <h3 class="mb-1">{{ $staffMetrics->count() }}</h3>
                    <p class="text-muted mb-0">Active Staff Members</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="fas fa-tasks fa-2x"></i>
                    </div>
                    <h3 class="mb-1">{{ number_format($staffMetrics->sum('total_assigned')) }}</h3>
                    <p class="text-muted mb-0">Total Tasks Assigned</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="fas fa-check-double fa-2x"></i>
                    </div>
                    <h3 class="mb-1">{{ number_format($staffMetrics->sum('completed_tasks')) }}</h3>
                    <p class="text-muted mb-0">Tasks Completed</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="fas fa-percentage fa-2x"></i>
                    </div>
                    @php 
                        $totalAssigned = $staffMetrics->sum('total_assigned');
                        $totalCompleted = $staffMetrics->sum('completed_tasks');
                        $avgCompletionRate = $totalAssigned > 0 ? ($totalCompleted / $totalAssigned) * 100 : 0;
                    @endphp
                    <h3 class="mb-1">{{ round($avgCompletionRate, 1) }}%</h3>
                    <p class="text-muted mb-0">Average Completion Rate</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Performance Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="card-title mb-0">Individual Staff Performance</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="staffTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Staff Member</th>
                                    <th class="text-center">Total Assigned</th>
                                    <th class="text-center">Completed</th>
                                    <th class="text-center">Pending</th>
                                    <th class="text-center">Completion Rate</th>
                                    <th class="text-center">Avg Completion Time</th>
                                    <th class="text-center">Performance Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staffMetrics as $staff)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-3">
                                                <i class="fas fa-user text-muted"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $staff->name }}</h6>
                                                <small class="text-muted">{{ $staff->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ number_format($staff->total_assigned) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ number_format($staff->completed_tasks) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning">{{ number_format($staff->pending_tasks) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <span class="badge {{ $staff->completion_rate >= 80 ? 'bg-success' : ($staff->completion_rate >= 60 ? 'bg-warning' : 'bg-danger') }} me-2">
                                                {{ $staff->completion_rate }}%
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-muted">{{ $staff->avg_completion_time }}h</span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $rating = 'Poor';
                                            $ratingClass = 'danger';
                                            $stars = 1;
                                            
                                            if ($staff->completion_rate >= 90 && $staff->avg_completion_time <= 24) {
                                                $rating = 'Excellent';
                                                $ratingClass = 'success';
                                                $stars = 5;
                                            } elseif ($staff->completion_rate >= 80 && $staff->avg_completion_time <= 48) {
                                                $rating = 'Very Good';
                                                $ratingClass = 'success';
                                                $stars = 4;
                                            } elseif ($staff->completion_rate >= 70) {
                                                $rating = 'Good';
                                                $ratingClass = 'info';
                                                $stars = 3;
                                            } elseif ($staff->completion_rate >= 50) {
                                                $rating = 'Fair';
                                                $ratingClass = 'warning';
                                                $stars = 2;
                                            }
                                        @endphp
                                        <div class="text-center">
                                            <div class="mb-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $stars ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                            </div>
                                            <small class="text-{{ $ratingClass }}">{{ $rating }}</small>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="fas fa-users fa-3x mb-3 d-block"></i>
                                        <h5>No staff performance data found</h5>
                                        <p>No staff assignments were made during the selected period.</p>
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

    <!-- Workload Distribution -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="card-title mb-0">Workload Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="workloadChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="card-title mb-0">Performance Summary</h5>
                </div>
                <div class="card-body">
                    @if($staffMetrics->count() > 0)
                        @php
                            $topPerformer = $staffMetrics->sortByDesc('completion_rate')->first();
                            $mostProductive = $staffMetrics->sortByDesc('total_assigned')->first();
                            $fastest = $staffMetrics->where('avg_completion_time', '>', 0)->sortBy('avg_completion_time')->first();
                        @endphp
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-trophy text-warning me-2"></i>
                                <strong>Top Performer</strong>
                            </div>
                            <p class="mb-0">{{ $topPerformer->name }}</p>
                            <small class="text-muted">{{ $topPerformer->completion_rate }}% completion rate</small>
                        </div>
                        
                        <hr>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-chart-line text-success me-2"></i>
                                <strong>Most Productive</strong>
                            </div>
                            <p class="mb-0">{{ $mostProductive->name }}</p>
                            <small class="text-muted">{{ $mostProductive->total_assigned }} tasks assigned</small>
                        </div>
                        
                        @if($fastest)
                        <hr>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-bolt text-primary me-2"></i>
                                <strong>Fastest Completion</strong>
                            </div>
                            <p class="mb-0">{{ $fastest->name }}</p>
                            <small class="text-muted">{{ $fastest->avg_completion_time }}h average</small>
                        </div>
                        @endif
                    @else
                        <p class="text-muted text-center">No performance data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Insights -->
    @if($staffMetrics->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="card-title mb-0">Team Performance Insights</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Team Efficiency -->
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    @if($avgCompletionRate > 80)
                                        <i class="fas fa-check-circle text-success fa-2x"></i>
                                    @elseif($avgCompletionRate > 60)
                                        <i class="fas fa-exclamation-triangle text-warning fa-2x"></i>
                                    @else
                                        <i class="fas fa-times-circle text-danger fa-2x"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-1">Team Efficiency</h6>
                                    @if($avgCompletionRate > 80)
                                        <p class="text-success mb-0">Excellent team performance! The team is highly efficient and productive.</p>
                                    @elseif($avgCompletionRate > 60)
                                        <p class="text-warning mb-0">Good team performance with room for improvement. Focus on training and process optimization.</p>
                                    @else
                                        <p class="text-danger mb-0">Team performance needs attention. Review workload distribution and provide additional support.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Workload Balance -->
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    @php
                                        $maxTasks = $staffMetrics->max('total_assigned');
                                        $minTasks = $staffMetrics->min('total_assigned');
                                        $taskVariance = $maxTasks - $minTasks;
                                        $avgTasks = $staffMetrics->avg('total_assigned');
                                        $isBalanced = $taskVariance <= ($avgTasks * 0.5);
                                    @endphp
                                    @if($isBalanced)
                                        <i class="fas fa-balance-scale text-success fa-2x"></i>
                                    @else
                                        <i class="fas fa-balance-scale-right text-warning fa-2x"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-1">Workload Balance</h6>
                                    @if($isBalanced)
                                        <p class="text-success mb-0">Workload is well-balanced across the team. Good distribution of tasks.</p>
                                    @else
                                        <p class="text-warning mb-0">Workload distribution could be improved. Consider redistributing tasks more evenly.</p>
                                    @endif
                                    <small class="text-muted">Range: {{ $minTasks }} - {{ $maxTasks }} tasks per staff member</small>
                                </div>
                            </div>
                        </div>

                        <!-- Training Needs -->
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    @php $needsTraining = $staffMetrics->where('completion_rate', '<', 70)->count(); @endphp
                                    @if($needsTraining == 0)
                                        <i class="fas fa-graduation-cap text-success fa-2x"></i>
                                    @elseif($needsTraining <= 2)
                                        <i class="fas fa-graduation-cap text-warning fa-2x"></i>
                                    @else
                                        <i class="fas fa-graduation-cap text-danger fa-2x"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-1">Training Recommendations</h6>
                                    @if($needsTraining == 0)
                                        <p class="text-success mb-0">All staff members are performing well. Continue with current training programs.</p>
                                    @elseif($needsTraining <= 2)
                                        <p class="text-warning mb-0">{{ $needsTraining }} staff member(s) may benefit from additional training and support.</p>
                                    @else
                                        <p class="text-danger mb-0">{{ $needsTraining }} staff members need focused training and mentoring programs.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Recognition -->
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="fas fa-award text-info fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Recognition Opportunities</h6>
                                    @php $highPerformers = $staffMetrics->where('completion_rate', '>=', 85)->count(); @endphp
                                    @if($highPerformers > 0)
                                        <p class="text-info mb-0">{{ $highPerformers }} staff member(s) deserve recognition for outstanding performance.</p>
                                    @else
                                        <p class="text-muted mb-0">Focus on improving overall team performance before implementing recognition programs.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize DataTable
$(document).ready(function() {
    $('#staffTable').DataTable({
        "pageLength": 25,
        "order": [[ 4, "desc" ]], // Sort by completion rate descending
        "columnDefs": [
            { "orderable": false, "targets": [6] } // Disable sorting on performance rating column
        ]
    });
});

// Workload Distribution Chart
const workloadCtx = document.getElementById('workloadChart').getContext('2d');
new Chart(workloadCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($staffMetrics->pluck('name')) !!},
        datasets: [{
            label: 'Assigned Tasks',
            data: {!! json_encode($staffMetrics->pluck('total_assigned')) !!},
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }, {
            label: 'Completed Tasks',
            data: {!! json_encode($staffMetrics->pluck('completed_tasks')) !!},
            backgroundColor: 'rgba(40, 167, 69, 0.8)',
            borderColor: 'rgba(40, 167, 69, 1)',
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
                    text: 'Number of Tasks'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Staff Members'
                }
            }
        },
        plugins: {
            title: {
                display: true,
                text: 'Task Assignment vs Completion by Staff Member'
            }
        }
    }
});
</script>
@endpush

@push('styles')
<style>
.avatar-sm {
    width: 40px;
    height: 40px;
}
.badge {
    font-size: 0.85em;
}
.table td {
    vertical-align: middle;
}
</style>
@endpush
@endsection
