@extends('layouts.manager')

@section('title', 'Service Usage Report')

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
                            <li class="breadcrumb-item active">Service Usage</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0">Service Usage Report</h1>
                    <p class="text-muted">Detailed service utilization analysis</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('manager.reports.index', request()->query()) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                    <a href="{{ route('manager.reports.export', ['type' => 'service-usage'] + request()->query()) }}" class="btn btn-success">
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

    <!-- Category Breakdown -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="card-title mb-0">Service Categories Breakdown</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($categoryBreakdown as $category)
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="mb-2">
                                    @switch($category->category)
                                        @case('spa')
                                            <i class="fas fa-spa text-primary fa-2x"></i>
                                            @break
                                        @case('dining')
                                            <i class="fas fa-utensils text-success fa-2x"></i>
                                            @break
                                        @case('activities')
                                            <i class="fas fa-volleyball-ball text-warning fa-2x"></i>
                                            @break
                                        @case('transportation')
                                            <i class="fas fa-car text-info fa-2x"></i>
                                            @break
                                        @case('room_service')
                                            <i class="fas fa-concierge-bell text-danger fa-2x"></i>
                                            @break
                                        @default
                                            <i class="fas fa-cog text-secondary fa-2x"></i>
                                    @endswitch
                                </div>
                                <h4 class="mb-1">{{ number_format($category->total_requests) }}</h4>
                                <p class="mb-0 text-muted">{{ ucfirst(str_replace('_', ' ', $category->category)) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Service Usage Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="card-title mb-0">Service Performance Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="servicesTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Service Name</th>
                                    <th>Category</th>
                                    <th class="text-center">Total Requests</th>
                                    <th class="text-center">Completed</th>
                                    <th class="text-center">Pending</th>
                                    <th class="text-center">Cancelled</th>
                                    <th class="text-center">Completion Rate</th>
                                    <th class="text-center">Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($serviceUsageDetails as $service)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @switch($service->category)
                                                    @case('spa')
                                                        <i class="fas fa-spa text-primary"></i>
                                                        @break
                                                    @case('dining')
                                                        <i class="fas fa-utensils text-success"></i>
                                                        @break
                                                    @case('activities')
                                                        <i class="fas fa-volleyball-ball text-warning"></i>
                                                        @break
                                                    @case('transportation')
                                                        <i class="fas fa-car text-info"></i>
                                                        @break
                                                    @case('room_service')
                                                        <i class="fas fa-concierge-bell text-danger"></i>
                                                        @break
                                                    @default
                                                        <i class="fas fa-cog text-secondary"></i>
                                                @endswitch
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $service->name }}</h6>
                                                @if($service->description)
                                                    <small class="text-muted">{{ Str::limit($service->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $service->category)) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ number_format($service->total_requests) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ number_format($service->completed_requests) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning">{{ number_format($service->pending_requests) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger">{{ number_format($service->cancelled_requests) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $service->completion_rate >= 80 ? 'bg-success' : ($service->completion_rate >= 60 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ $service->completion_rate }}%
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="progress" style="height: 20px; width: 120px;">
                                            <div class="progress-bar {{ $service->completion_rate >= 80 ? 'bg-success' : ($service->completion_rate >= 60 ? 'bg-warning' : 'bg-danger') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $service->completion_rate }}%"
                                                 title="{{ $service->completion_rate }}% completion rate">
                                                {{ $service->completion_rate }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <i class="fas fa-chart-bar fa-3x mb-3 d-block"></i>
                                        <h5>No service usage data found</h5>
                                        <p>No service requests were made during the selected period.</p>
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

    <!-- Service Performance Insights -->
    @if($serviceUsageDetails->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom-0">
                    <h5 class="card-title mb-0">Performance Insights</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                                <i class="fas fa-trophy text-success fa-2x mb-2"></i>
                                <h6 class="text-success">Top Performer</h6>
                                @php $topService = $serviceUsageDetails->sortByDesc('completion_rate')->first(); @endphp
                                <p class="mb-0"><strong>{{ $topService->name }}</strong></p>
                                <small class="text-muted">{{ $topService->completion_rate }}% completion rate</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                                <i class="fas fa-star text-primary fa-2x mb-2"></i>
                                <h6 class="text-primary">Most Popular</h6>
                                @php $popularService = $serviceUsageDetails->sortByDesc('total_requests')->first(); @endphp
                                <p class="mb-0"><strong>{{ $popularService->name }}</strong></p>
                                <small class="text-muted">{{ $popularService->total_requests }} requests</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                                <i class="fas fa-exclamation-triangle text-warning fa-2x mb-2"></i>
                                <h6 class="text-warning">Needs Attention</h6>
                                @php $needsAttention = $serviceUsageDetails->sortBy('completion_rate')->first(); @endphp
                                <p class="mb-0"><strong>{{ $needsAttention->name }}</strong></p>
                                <small class="text-muted">{{ $needsAttention->completion_rate }}% completion rate</small>
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
<script>
// Initialize DataTable for better sorting and searching
$(document).ready(function() {
    $('#servicesTable').DataTable({
        "pageLength": 25,
        "order": [[ 2, "desc" ]], // Sort by total requests descending
        "columnDefs": [
            { "orderable": false, "targets": [7] } // Disable sorting on progress bar column
        ]
    });
});
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
.table td {
    vertical-align: middle;
}
</style>
@endpush
@endsection
