<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\Booking; // Add this import
use App\Models\Room;    // Add this import
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * Display service usage and performance reports dashboard
     */
    public function index(Request $request)
    {
        // Get date range
        $dateRange = $this->getDateRange($request);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];
        
        // Calculate statistics for the date range
        $stats = [
            'total_requests' => ServiceRequest::whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed_requests' => ServiceRequest::where('status', 'completed')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'pending_requests' => ServiceRequest::where('status', 'pending')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'in_progress_requests' => ServiceRequest::where('status', 'in_progress')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'cancelled_requests' => ServiceRequest::where('status', 'cancelled')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'avg_response_time' => ServiceRequest::whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('assigned_at')
                ->selectRaw('AVG((julianday(assigned_at) - julianday(created_at)) * 24) as avg_hours')
                ->first()->avg_hours ?? 0,
        ];

        // Service usage data for charts
        $serviceUsage = Service::withCount([
            'serviceRequests as request_count' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        ])
        ->get()
        ->filter(function ($service) {
            return $service->request_count > 0;
        })
        ->sortByDesc('request_count')
        ->take(10)
        ->values();

        // Performance metrics by status
        $performanceMetrics = ServiceRequest::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Staff performance data
        $staffPerformance = User::where('role', 'staff')
            ->withCount([
                'assignedServiceRequests as assigned_count' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            ])
            ->get()
            ->filter(function ($staff) {
                return $staff->assigned_count > 0;
            })
            ->sortByDesc('assigned_count')
            ->values();

        // Daily trends for the date range
        $dailyTrends = collect();
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $requestCount = ServiceRequest::whereDate('created_at', $currentDate)->count();
            $dailyTrends->push([
                'date' => $currentDate->format('M d'),
                'request_count' => $requestCount
            ]);
            $currentDate->addDay();
        }

        return view('manager.reports.index', compact(
            'stats',
            'startDate',
            'endDate',
            'serviceUsage',
            'performanceMetrics',
            'staffPerformance',
            'dailyTrends'
        ));
    }

    /**
     * Service usage detailed report
     */
    public function serviceUsage(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // Get detailed service usage with breakdown by status
        $serviceUsageDetails = Service::withCount([
            'serviceRequests as total_requests' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            },
            'serviceRequests as completed_requests' => function ($query) use ($startDate, $endDate) {
                $query->where('status', 'completed')
                      ->whereBetween('created_at', [$startDate, $endDate]);
            },
            'serviceRequests as pending_requests' => function ($query) use ($startDate, $endDate) {
                $query->where('status', 'pending')
                      ->whereBetween('created_at', [$startDate, $endDate]);
            },
            'serviceRequests as cancelled_requests' => function ($query) use ($startDate, $endDate) {
                $query->where('status', 'cancelled')
                      ->whereBetween('created_at', [$startDate, $endDate]);
            }
        ])->get();

        // Calculate additional metrics
        $serviceUsageDetails = $serviceUsageDetails->map(function ($service) {
            $service->completion_rate = $service->total_requests > 0 
                ? round(($service->completed_requests / $service->total_requests) * 100, 1)
                : 0;
            $service->cancellation_rate = $service->total_requests > 0 
                ? round(($service->cancelled_requests / $service->total_requests) * 100, 1)
                : 0;
            return $service;
        });

        // Get category breakdown
        $categoryBreakdown = Service::select('category')
            ->withCount([
                'serviceRequests as total_requests' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            ])
            ->groupBy('category')
            ->get();

        return view('manager.reports.service-usage', compact(
            'serviceUsageDetails',
            'categoryBreakdown',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Performance metrics detailed report
     */
    public function performanceMetrics(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // Average response time (time from creation to assignment)
        $avgResponseTime = ServiceRequest::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('assigned_at')
            ->selectRaw('AVG((julianday(assigned_at) - julianday(created_at)) * 24) as avg_hours')
            ->first()->avg_hours ?? 0;

        // Average completion time (time from assignment to completion)
        $avgCompletionTime = ServiceRequest::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('completed_at')
            ->whereNotNull('assigned_at')
            ->selectRaw('AVG((julianday(completed_at) - julianday(assigned_at)) * 24) as avg_hours')
            ->first()->avg_hours ?? 0;

        // Service level metrics by status
        $statusMetrics = ServiceRequest::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count, AVG((julianday(COALESCE(completed_at, datetime("now"))) - julianday(created_at)) * 24) as avg_duration')
            ->groupBy('status')
            ->get();

        // Peak hours analysis
        $peakHours = ServiceRequest::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('strftime("%H", created_at) as hour, COUNT(*) as request_count')
            ->groupByRaw('strftime("%H", created_at)')
            ->orderBy('request_count', 'desc')
            ->get();

        // Monthly trends if date range is large enough
        $monthlyTrends = [];
        if ($startDate->diffInMonths($endDate) >= 1) {
            $monthlyTrends = ServiceRequest::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('strftime("%Y", created_at) as year, strftime("%m", created_at) as month, COUNT(*) as total_requests,
                            SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_requests')
                ->groupByRaw('strftime("%Y", created_at), strftime("%m", created_at)')
                ->orderByRaw('strftime("%Y", created_at) ASC, strftime("%m", created_at) ASC')
                ->get();
        }

        return view('manager.reports.performance-metrics', compact(
            'avgResponseTime',
            'avgCompletionTime',
            'statusMetrics',
            'peakHours',
            'monthlyTrends',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Staff performance report
     */
    public function staffPerformance(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // Staff performance metrics
        $staffMetrics = User::where('role', 'staff')
            ->withCount([
                'assignedServiceRequests as total_assigned' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('assigned_at', [$startDate, $endDate]);
                },
                'assignedServiceRequests as completed_tasks' => function ($query) use ($startDate, $endDate) {
                    $query->where('status', 'completed')
                          ->whereBetween('assigned_at', [$startDate, $endDate]);
                },
                'assignedServiceRequests as pending_tasks' => function ($query) use ($startDate, $endDate) {
                    $query->whereIn('status', ['assigned', 'in_progress'])
                          ->whereBetween('assigned_at', [$startDate, $endDate]);
                }
            ])
            ->get()
            ->map(function ($staff) {
                $staff->completion_rate = $staff->total_assigned > 0 
                    ? round(($staff->completed_tasks / $staff->total_assigned) * 100, 1)
                    : 0;
                
                // Calculate average completion time for this staff member
                $avgTime = ServiceRequest::where('assigned_to', $staff->id)
                    ->whereNotNull('completed_at')
                    ->whereNotNull('assigned_at')
                    ->selectRaw('AVG((julianday(completed_at) - julianday(assigned_at)) * 24) as avg_hours')
                    ->first()->avg_hours ?? 0;
                
                $staff->avg_completion_time = round($avgTime, 1);
                
                return $staff;
            });

        // Workload distribution
        $workloadDistribution = ServiceRequest::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('assigned_to')
            ->select('assigned_to')
            ->selectRaw('COUNT(*) as task_count')
            ->groupBy('assigned_to')
            ->with('assignedStaff:id,name')
            ->get();

        return view('manager.reports.staff-performance', compact(
            'staffMetrics',
            'workloadDistribution',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export reports to CSV
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'overview');
        $dateRange = $this->getDateRange($request);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        $filename = "service_reports_{$type}_" . $startDate->format('Y-m-d') . "_to_" . $endDate->format('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream(function () use ($type, $startDate, $endDate) {
            $handle = fopen('php://output', 'w');
            
            switch ($type) {
                case 'service-usage':
                    $this->exportServiceUsage($handle, $startDate, $endDate);
                    break;
                case 'staff-performance':
                    $this->exportStaffPerformance($handle, $startDate, $endDate);
                    break;
                default:
                    $this->exportOverview($handle, $startDate, $endDate);
            }
            
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Get date range from request
     */
    private function getDateRange(Request $request)
    {
        $period = $request->get('period', 'last_30_days');
        
        switch ($period) {
            case 'today':
                $start = Carbon::today();
                $end = Carbon::tomorrow();
                break;
            case 'yesterday':
                $start = Carbon::yesterday();
                $end = Carbon::today();
                break;
            case 'last_7_days':
                $start = Carbon::now()->subDays(7);
                $end = Carbon::now();
                break;
            case 'last_30_days':
                $start = Carbon::now()->subDays(30);
                $end = Carbon::now();
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                break;
            case 'last_month':
                $start = Carbon::now()->subMonth()->startOfMonth();
                $end = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'custom':
                $start = $request->get('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
                $end = $request->get('end_date') ? Carbon::parse($request->end_date) : Carbon::now();
                break;
            default:
                $start = Carbon::now()->subDays(30);
                $end = Carbon::now();
        }
        
        return ['start' => $start, 'end' => $end];
    }

    /**
     * Get overview statistics
     */
    private function getOverviewStats($startDate, $endDate)
    {
        return [
            'total_requests' => ServiceRequest::whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed_requests' => ServiceRequest::where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])->count(),
            'pending_requests' => ServiceRequest::where('status', 'pending')
                ->whereBetween('created_at', [$startDate, $endDate])->count(),
            'active_services' => Service::where('is_available', true)->count(),
            'avg_response_time' => ServiceRequest::whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('assigned_at')
                ->selectRaw('AVG((julianday(assigned_at) - julianday(created_at)) * 24) as avg_hours')
                ->first()->avg_hours ?? 0,
        ];
    }

    /**
     * Get service usage data
     */
    private function getServiceUsageData($startDate, $endDate)
    {
        return Service::withCount([
            'serviceRequests as request_count' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        ])->orderBy('request_count', 'desc')->take(10)->get();
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics($startDate, $endDate)
    {
        return ServiceRequest::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
    }

    /**
     * Get staff performance data  
     */
    private function getStaffPerformanceData($startDate, $endDate)
    {
        return User::where('role', 'staff')
            ->withCount([
                'assignedServiceRequests as assigned_count' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('assigned_at', [$startDate, $endDate]);
                }
            ])
            ->orderBy('assigned_count', 'desc')
            ->take(10)
            ->get();
    }

    /**
     * Get daily trends
     */
    private function getDailyTrends($startDate, $endDate)
    {
        return ServiceRequest::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as request_count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
    }

    /**
     * Export service usage data
     */
    private function exportServiceUsage($handle, $startDate, $endDate)
    {
        fputcsv($handle, ['Service Name', 'Category', 'Total Requests', 'Completed', 'Pending', 'Cancelled', 'Completion Rate %']);
        
        $services = Service::withCount([
            'serviceRequests as total_requests' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            },
            'serviceRequests as completed_requests' => function ($query) use ($startDate, $endDate) {
                $query->where('status', 'completed')->whereBetween('created_at', [$startDate, $endDate]);
            },
            'serviceRequests as pending_requests' => function ($query) use ($startDate, $endDate) {
                $query->where('status', 'pending')->whereBetween('created_at', [$startDate, $endDate]);
            },
            'serviceRequests as cancelled_requests' => function ($query) use ($startDate, $endDate) {
                $query->where('status', 'cancelled')->whereBetween('created_at', [$startDate, $endDate]);
            }
        ])->get();

        foreach ($services as $service) {
            $completionRate = $service->total_requests > 0 
                ? round(($service->completed_requests / $service->total_requests) * 100, 1)
                : 0;
                
            fputcsv($handle, [
                $service->name,
                $service->category,
                $service->total_requests,
                $service->completed_requests,
                $service->pending_requests,
                $service->cancelled_requests,
                $completionRate
            ]);
        }
    }

    /**
     * Export staff performance data
     */
    private function exportStaffPerformance($handle, $startDate, $endDate)
    {
        fputcsv($handle, ['Staff Name', 'Total Assigned', 'Completed Tasks', 'Pending Tasks', 'Completion Rate %', 'Avg Completion Time (Hours)']);
        
        $staff = User::where('role', 'staff')->get();
        
        foreach ($staff as $member) {
            $assigned = ServiceRequest::where('assigned_to', $member->id)
                ->whereBetween('assigned_at', [$startDate, $endDate])->count();
            $completed = ServiceRequest::where('assigned_to', $member->id)
                ->where('status', 'completed')
                ->whereBetween('assigned_at', [$startDate, $endDate])->count();
            $pending = ServiceRequest::where('assigned_to', $member->id)
                ->whereIn('status', ['assigned', 'in_progress'])
                ->whereBetween('assigned_at', [$startDate, $endDate])->count();
            
            $completionRate = $assigned > 0 ? round(($completed / $assigned) * 100, 1) : 0;
            
            $avgTime = ServiceRequest::where('assigned_to', $member->id)
                ->whereNotNull('completed_at')
                ->whereNotNull('assigned_at')
                ->whereBetween('assigned_at', [$startDate, $endDate])
                ->selectRaw('AVG((julianday(completed_at) - julianday(assigned_at)) * 24) as avg_hours')
                ->first()->avg_hours ?? 0;
            
            fputcsv($handle, [
                $member->name,
                $assigned,
                $completed,
                $pending,
                $completionRate,
                round($avgTime, 1)
            ]);
        }
    }

    /**
     * Export overview data
     */
    private function exportOverview($handle, $startDate, $endDate)
    {
        fputcsv($handle, ['Metric', 'Value']);
        
        $stats = $this->getOverviewStats($startDate, $endDate);
        
        foreach ($stats as $key => $value) {
            $label = ucwords(str_replace('_', ' ', $key));
            fputcsv($handle, [$label, $value]);
        }
    }
}
