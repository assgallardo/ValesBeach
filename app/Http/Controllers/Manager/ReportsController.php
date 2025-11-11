<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Payment;
use App\Models\FoodOrder;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use App\Models\Task;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * Get the route prefix based on the current request
     */
    protected function getRoutePrefix()
    {
        return str_contains(request()->route()->getName(), 'admin.') ? 'admin' : 'manager';
    }

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
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, assigned_at)) as avg_hours')
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

        // Room Sales Overview - Include soft-deleted bookings for historical accuracy
        $roomSalesOverview = [
            'total_bookings' => Booking::withTrashed()->whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed_bookings' => Booking::withTrashed()
                ->whereIn('status', ['completed', 'checked_out'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'total_revenue' => Payment::whereNotNull('booking_id')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
            'avg_booking_value' => Payment::whereNotNull('booking_id')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->avg('amount'),
        ];

        // Calculate Revenue Totals
        $revenueStats = [
            // Rooms/Bookings Revenue - using completed payments
            'rooms_revenue' => Payment::whereNotNull('booking_id')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
            
            // Food Revenue - using completed payments
            'food_revenue' => Payment::whereNotNull('food_order_id')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
            
            // Services Revenue - using completed payments
            'services_revenue' => Payment::whereNotNull('service_request_id')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
        ];
        
        // Calculate Overall Total Revenue
        $revenueStats['total_revenue'] = $revenueStats['rooms_revenue'] + 
                                         $revenueStats['food_revenue'] + 
                                         $revenueStats['services_revenue'];

        // Food Sales Overview for Dashboard
        $foodSalesOverview = [
            'total_orders' => FoodOrder::whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed_orders' => FoodOrder::whereIn('status', ['delivered', 'completed'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'total_revenue' => Payment::whereNotNull('food_order_id')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
            'avg_order_value' => Payment::whereNotNull('food_order_id')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->avg('amount'),
        ];

        // Top 5 Menu Items (for dashboard quick view)
        $topMenuItems = MenuItem::join('order_items', 'menu_items.id', '=', 'order_items.menu_item_id')
            ->join('food_orders', 'order_items.food_order_id', '=', 'food_orders.id')
            ->whereIn('food_orders.status', ['delivered', 'completed'])
            ->whereBetween('food_orders.created_at', [$startDate, $endDate])
            ->selectRaw('
                menu_items.name,
                SUM(order_items.quantity) as total_quantity,
                SUM(order_items.total_price) as total_revenue
            ')
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        // Revenue by category for quick overview - using Payment model
        $revenueByCategory = Room::join('bookings', 'rooms.id', '=', 'bookings.room_id')
            ->leftJoin('payments', function($join) {
                $join->on('bookings.id', '=', 'payments.booking_id')
                     ->where('payments.status', '=', 'completed');
            })
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->selectRaw('
                rooms.category,
                COUNT(DISTINCT bookings.id) as booking_count,
                COALESCE(SUM(payments.amount), 0) as total_revenue
            ')
            ->groupBy('rooms.category')
            ->orderByDesc('total_revenue')
            ->get();

        $routePrefix = $this->getRoutePrefix();

        return view('manager.reports.index', compact(
            'stats',
            'startDate',
            'endDate',
            'serviceUsage',
            'performanceMetrics',
            'staffPerformance',
            'dailyTrends',
            'roomSalesOverview',
            'revenueByCategory',
            'revenueStats',
            'foodSalesOverview',
            'topMenuItems',
            'routePrefix'
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

        // Get all distinct service categories from services table
        $allCategories = DB::table('services')
            ->select('category')
            ->distinct()
            ->pluck('category');

        // Get category breakdown with actual request counts
        $categoryRequestCounts = DB::table('services')
            ->leftJoin('service_requests', function($join) use ($startDate, $endDate) {
                $join->on('services.id', '=', 'service_requests.service_id')
                     ->whereBetween('service_requests.created_at', [$startDate, $endDate]);
            })
            ->select('services.category')
            ->selectRaw('COUNT(service_requests.id) as total_requests')
            ->groupBy('services.category')
            ->get()
            ->keyBy('category');

        // Ensure all categories are present, even with 0 requests
        $categoryBreakdown = $allCategories->map(function($category) use ($categoryRequestCounts) {
            return (object)[
                'category' => $category,
                'total_requests' => $categoryRequestCounts->has($category) 
                    ? $categoryRequestCounts->get($category)->total_requests 
                    : 0
            ];
        });

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

        // Average response time (time from creation to assignment) - FIXED FOR MYSQL
        $avgResponseTime = ServiceRequest::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('assigned_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, assigned_at)) as avg_hours')
            ->first()->avg_hours ?? 0;

        // Average completion time (time from assignment to completion) - FIXED FOR MYSQL
        $avgCompletionTime = ServiceRequest::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('completed_at')
            ->whereNotNull('assigned_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, assigned_at, completed_at)) as avg_hours')
            ->first()->avg_hours ?? 0;

        // Service level metrics by status - FIXED FOR MYSQL
        $statusMetrics = ServiceRequest::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                status, 
                COUNT(*) as count, 
                AVG(TIMESTAMPDIFF(HOUR, created_at, COALESCE(completed_at, NOW()))) as avg_duration
            ')
            ->groupBy('status')
            ->get();

        // Peak hours analysis - FIXED FOR MYSQL
        $peakHours = ServiceRequest::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as request_count')
            ->groupByRaw('HOUR(created_at)')
            ->orderBy('request_count', 'desc')
            ->get();

        // Monthly trends if date range is large enough - FIXED FOR MYSQL
        $monthlyTrends = collect();
        if ($startDate->diffInMonths($endDate) >= 1) {
            $monthlyTrends = ServiceRequest::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('
                    YEAR(created_at) as year, 
                    MONTH(created_at) as month, 
                    COUNT(*) as total_requests,
                    SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_requests
                ')
                ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                ->orderByRaw('YEAR(created_at) ASC, MONTH(created_at) ASC')
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
                
                // Calculate average completion time for this staff member - FIXED FOR MYSQL
                $avgTime = ServiceRequest::where('assigned_to', $staff->id)
                    ->whereNotNull('completed_at')
                    ->whereNotNull('assigned_at')
                    ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, assigned_at, completed_at)) as avg_hours')
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
        try {
            $type = $request->get('type', 'overview');
            $dateRange = $this->getDateRange($request);
            $startDate = $dateRange['start'];
            $endDate = $dateRange['end'];

            $filename = "valesbeach_reports_{$type}_" . $startDate->format('Y-m-d') . "_to_" . $endDate->format('Y-m-d') . ".csv";

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            return response()->stream(function () use ($type, $startDate, $endDate) {
                $handle = fopen('php://output', 'w');
                
                // Add BOM for proper Excel UTF-8 support
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                
                try {
                    switch ($type) {
                        case 'room-sales':
                            $this->exportRoomSales($handle, $startDate, $endDate);
                            break;
                        case 'food-sales':
                            $this->exportFoodSales($handle, $startDate, $endDate);
                            break;
                        case 'service-sales':
                            $this->exportServiceSales($handle, $startDate, $endDate);
                            break;
                        case 'service-usage':
                            $this->exportServiceUsage($handle, $startDate, $endDate);
                            break;
                        case 'staff-performance':
                            $this->exportStaffPerformance($handle, $startDate, $endDate);
                            break;
                        case 'repeat-customers':
                            $this->exportRepeatCustomers($handle, $startDate, $endDate);
                            break;
                        case 'customer-analytics':
                            $this->exportCustomerAnalytics($handle, $startDate, $endDate);
                            break;
                        case 'customer-preferences':
                            $this->exportCustomerPreferences($handle, $startDate, $endDate);
                            break;
                        case 'payment-methods':
                            $this->exportPaymentMethods($handle, $startDate, $endDate);
                            break;
                        case 'overview':
                        default:
                            $this->exportOverview($handle, $startDate, $endDate);
                    }
                } catch (\Exception $e) {
                    fputcsv($handle, ['ERROR']);
                    fputcsv($handle, ['An error occurred while generating the report:']);
                    fputcsv($handle, [$e->getMessage()]);
                    fputcsv($handle, ['File: ' . $e->getFile()]);
                    fputcsv($handle, ['Line: ' . $e->getLine()]);
                }
                
                fclose($handle);
            }, 200, $headers);
        } catch (\Exception $e) {
            \Log::error('Export error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to export report: ' . $e->getMessage());
        }
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
     * Export staff performance data - FIXED FOR MYSQL
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
            
            // FIXED: Replace julianday with TIMESTAMPDIFF for MySQL
            $avgTime = ServiceRequest::where('assigned_to', $member->id)
                ->whereNotNull('completed_at')
                ->whereNotNull('assigned_at')
                ->whereBetween('assigned_at', [$startDate, $endDate])
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, assigned_at, completed_at)) as avg_hours')
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
        // Header
        fputcsv($handle, ['VALESBEACH RESORT - COMPREHENSIVE OVERVIEW REPORT']);
        fputcsv($handle, ['Report Period:', $startDate->format('M d, Y') . ' to ' . $endDate->format('M d, Y')]);
        fputcsv($handle, ['Generated:', now()->format('M d, Y H:i:s')]);
        fputcsv($handle, []);

        // === REVENUE SUMMARY ===
        fputcsv($handle, ['=== REVENUE SUMMARY ===']);
        fputcsv($handle, ['Metric', 'Amount']);
        
        $roomsRevenue = Payment::whereNotNull('booking_id')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
        
        $foodRevenue = Payment::whereNotNull('food_order_id')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
        
        $servicesRevenue = Payment::whereNotNull('service_request_id')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
        
        $totalRevenue = $roomsRevenue + $foodRevenue + $servicesRevenue;
        
        fputcsv($handle, ['Rooms Revenue', '₱' . number_format($roomsRevenue, 2)]);
        fputcsv($handle, ['Food & Beverage Revenue', '₱' . number_format($foodRevenue, 2)]);
        fputcsv($handle, ['Services Revenue', '₱' . number_format($servicesRevenue, 2)]);
        fputcsv($handle, ['TOTAL REVENUE', '₱' . number_format($totalRevenue, 2)]);
        fputcsv($handle, []);

        // === BOOKING STATISTICS ===
        fputcsv($handle, ['=== BOOKING STATISTICS ===']);
        fputcsv($handle, ['Metric', 'Count']);
        
        $totalBookings = Booking::whereBetween('created_at', [$startDate, $endDate])->count();
        $completedBookings = Booking::whereIn('status', ['completed', 'checked_out'])
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        $cancelledBookings = Booking::where('status', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        
        fputcsv($handle, ['Total Bookings', $totalBookings]);
        fputcsv($handle, ['Completed Bookings', $completedBookings]);
        fputcsv($handle, ['Cancelled Bookings', $cancelledBookings]);
        fputcsv($handle, ['Completion Rate', $totalBookings > 0 ? round(($completedBookings / $totalBookings) * 100, 1) . '%' : '0%']);
        fputcsv($handle, []);

        // === FOOD & BEVERAGE STATISTICS ===
        fputcsv($handle, ['=== FOOD & BEVERAGE STATISTICS ===']);
        fputcsv($handle, ['Metric', 'Count/Amount']);
        
        $totalOrders = FoodOrder::whereBetween('created_at', [$startDate, $endDate])->count();
        $completedOrders = FoodOrder::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        
        fputcsv($handle, ['Total Orders', $totalOrders]);
        fputcsv($handle, ['Completed Orders', $completedOrders]);
        fputcsv($handle, ['Average Order Value', $totalOrders > 0 ? '₱' . number_format($foodRevenue / $totalOrders, 2) : '₱0.00']);
        fputcsv($handle, []);

        // === SERVICE REQUEST STATISTICS ===
        fputcsv($handle, ['=== SERVICE REQUEST STATISTICS ===']);
        fputcsv($handle, ['Metric', 'Count']);
        
        $stats = $this->getStats($startDate, $endDate);
        fputcsv($handle, ['Total Service Requests', $stats['total_requests']]);
        fputcsv($handle, ['Completed Requests', $stats['completed_requests']]);
        fputcsv($handle, ['Pending Requests', $stats['pending_requests']]);
        fputcsv($handle, ['Average Response Time (hours)', $stats['avg_response_time']]);
        fputcsv($handle, []);

        // === TOP PERFORMING CATEGORIES ===
        fputcsv($handle, ['=== TOP PERFORMING ROOM CATEGORIES ===']);
        fputcsv($handle, ['Category', 'Bookings', 'Revenue']);
        
        $revenueByCategory = Room::leftJoin('bookings', 'rooms.id', '=', 'bookings.room_id')
            ->leftJoin('payments', function($join) {
                $join->on('bookings.id', '=', 'payments.booking_id')
                     ->where('payments.status', '=', 'completed');
            })
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->selectRaw('
                rooms.category,
                COUNT(DISTINCT bookings.id) as booking_count,
                COALESCE(SUM(payments.amount), 0) as total_revenue
            ')
            ->groupBy('rooms.category')
            ->orderByDesc('total_revenue')
            ->get();
        
        foreach ($revenueByCategory as $cat) {
            fputcsv($handle, [$cat->category, $cat->booking_count, '₱' . number_format($cat->total_revenue, 2)]);
        }
        fputcsv($handle, []);

        // === STAFF PERFORMANCE SUMMARY ===
        fputcsv($handle, ['=== STAFF PERFORMANCE SUMMARY ===']);
        fputcsv($handle, ['Staff Member', 'Assigned Tasks']);
        
        $staffMembers = User::where('role', 'staff')->get();
        
        foreach ($staffMembers as $staff) {
            $taskCount = Task::where('assigned_to', $staff->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            
            if ($taskCount > 0) {
                fputcsv($handle, [$staff->name, $taskCount]);
            }
        }
        fputcsv($handle, []);

        // === PAYMENT METHOD DISTRIBUTION ===
        fputcsv($handle, ['=== PAYMENT METHOD DISTRIBUTION ===']);
        fputcsv($handle, ['Payment Method', 'Count', 'Total Amount']);
        
        $paymentMethods = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                payment_method,
                COUNT(*) as count,
                SUM(amount) as total_amount
            ')
            ->groupBy('payment_method')
            ->orderByDesc('total_amount')
            ->get();
        
        foreach ($paymentMethods as $method) {
            fputcsv($handle, [
                ucfirst(str_replace('_', ' ', $method->payment_method)),
                $method->count,
                '₱' . number_format($method->total_amount, 2)
            ]);
        }
        fputcsv($handle, []);

        // === CUSTOMER ANALYTICS SUMMARY ===
        fputcsv($handle, ['=== CUSTOMER ANALYTICS SUMMARY ===']);
        // Total Customers
        $totalCustomers = User::whereHas('bookings', function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })->count();
        // Repeat Customers
        $repeatCustomers = User::whereHas('bookings', function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->withCount(['bookings' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
        ->get()
        ->filter(function($user) {
            return $user->bookings_count >= 2;
        })
        ->count();
        // Retention Rate
        $retentionRate = $totalCustomers > 0 ? round(($repeatCustomers / $totalCustomers) * 100, 1) : 0;
        // Top Payment Method
        $topPaymentMethod = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('payment_method, COUNT(*) as count')
            ->groupBy('payment_method')
            ->orderByDesc('count')
            ->first();
        // Top Room Type
        $topRoomType = Room::join('bookings', 'rooms.id', '=', 'bookings.room_id')
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->selectRaw('rooms.type, COUNT(*) as bookings')
            ->groupBy('rooms.type')
            ->orderByDesc('bookings')
            ->first();
        // Top Service
        $topService = Service::withCount(['serviceRequests' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
        ->get()
        ->sortByDesc('service_requests_count')
        ->first();
        // Top Food Item
        $topFoodItem = MenuItem::join('order_items', 'menu_items.id', '=', 'order_items.menu_item_id')
            ->join('food_orders', 'order_items.food_order_id', '=', 'food_orders.id')
            ->whereBetween('food_orders.created_at', [$startDate, $endDate])
            ->selectRaw('menu_items.name, SUM(order_items.quantity) as total_orders')
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderByDesc('total_orders')
            ->first();

        fputcsv($handle, ['Metric', 'Value']);
        fputcsv($handle, ['Total Customers', $totalCustomers]);
        fputcsv($handle, ['Repeat Customers', $repeatCustomers]);
        fputcsv($handle, ['Retention Rate', $retentionRate . '%']);
        fputcsv($handle, ['Top Payment Method', $topPaymentMethod ? ucwords(str_replace('_', ' ', $topPaymentMethod->payment_method)) : 'N/A']);
        fputcsv($handle, ['Top Room Type', $topRoomType ? $topRoomType->type . ' (' . $topRoomType->bookings . ' bookings)' : 'N/A']);
        fputcsv($handle, ['Top Service', ($topService && $topService->service_requests_count > 0) ? $topService->name . ' (' . $topService->service_requests_count . ' requests)' : 'N/A']);
        fputcsv($handle, ['Top Food Item', $topFoodItem ? $topFoodItem->name . ' (' . $topFoodItem->total_orders . ' orders)' : 'N/A']);
        fputcsv($handle, []);

        // === FOOTER ===
        fputcsv($handle, ['--- End of Report ---']);
    }

    /**
     * Export Room Sales Report
     */
    private function exportRoomSales($handle, $startDate, $endDate)
    {
        fputcsv($handle, ['ROOM SALES REPORT']);
        fputcsv($handle, ['Period:', $startDate->format('M d, Y') . ' to ' . $endDate->format('M d, Y')]);
        fputcsv($handle, []);
        
        fputcsv($handle, ['Room/Cottage Name', 'Category', 'Bookings', 'Revenue']);
        
        $roomSales = Room::leftJoin('bookings', 'rooms.id', '=', 'bookings.room_id')
            ->leftJoin('payments', function($join) {
                $join->on('bookings.id', '=', 'payments.booking_id')
                     ->where('payments.status', '=', 'completed');
            })
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->selectRaw('
                rooms.name,
                rooms.category,
                COUNT(DISTINCT bookings.id) as booking_count,
                COALESCE(SUM(payments.amount), 0) as total_revenue
            ')
            ->groupBy('rooms.id', 'rooms.name', 'rooms.category')
            ->having('booking_count', '>', 0)
            ->orderByDesc('total_revenue')
            ->get();
        
        foreach ($roomSales as $room) {
            fputcsv($handle, [
                $room->name,
                $room->category,
                $room->booking_count,
                '₱' . number_format($room->total_revenue, 2)
            ]);
        }
    }

    /**
     * Export Food Sales Report
     */
    private function exportFoodSales($handle, $startDate, $endDate)
    {
        fputcsv($handle, ['FOOD & BEVERAGE SALES REPORT']);
        fputcsv($handle, ['Period:', $startDate->format('M d, Y') . ' to ' . $endDate->format('M d, Y')]);
        fputcsv($handle, []);
        
        fputcsv($handle, ['Item Name', 'Category', 'Quantity Sold', 'Revenue']);
        
        $foodSales = MenuItem::join('order_items', 'menu_items.id', '=', 'order_items.menu_item_id')
            ->join('food_orders', 'order_items.food_order_id', '=', 'food_orders.id')
            ->join('menu_categories', 'menu_items.menu_category_id', '=', 'menu_categories.id')
            ->whereBetween('food_orders.created_at', [$startDate, $endDate])
            ->selectRaw('
                menu_items.name,
                menu_categories.name as category,
                SUM(order_items.quantity) as total_quantity,
                SUM(order_items.total_price) as total_revenue
            ')
            ->groupBy('menu_items.id', 'menu_items.name', 'menu_categories.name')
            ->orderByDesc('total_revenue')
            ->get();
        
        foreach ($foodSales as $item) {
            fputcsv($handle, [
                $item->name,
                $item->category,
                $item->total_quantity,
                '₱' . number_format($item->total_revenue, 2)
            ]);
        }
    }

    /**
     * Export Service Sales Report
     */
    private function exportServiceSales($handle, $startDate, $endDate)
    {
        fputcsv($handle, ['SERVICE REVENUE REPORT']);
        fputcsv($handle, ['Period:', $startDate->format('M d, Y') . ' to ' . $endDate->format('M d, Y')]);
        fputcsv($handle, []);
        
        fputcsv($handle, ['Service Name', 'Category', 'Requests', 'Revenue']);
        
        $serviceSales = Service::join('service_requests', 'services.id', '=', 'service_requests.service_id')
            ->whereBetween('service_requests.created_at', [$startDate, $endDate])
            ->selectRaw('
                services.id,
                services.name,
                services.category,
                COUNT(DISTINCT service_requests.id) as request_count
            ')
            ->groupBy('services.id', 'services.name', 'services.category')
            ->orderByDesc('request_count')
            ->get();
        
        foreach ($serviceSales as $service) {
            // Get revenue separately to avoid JOIN multiplication issues
            $revenue = Payment::where('status', 'completed')
                ->whereIn('service_request_id', function($query) use ($service, $startDate, $endDate) {
                    $query->select('id')
                        ->from('service_requests')
                        ->where('service_id', $service->id)
                        ->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->sum('amount');
            
            fputcsv($handle, [
                $service->name,
                $service->category,
                $service->request_count,
                '₱' . number_format($revenue, 2)
            ]);
        }
    }

    /**
     * Export Repeat Customers Report
     */
    private function exportRepeatCustomers($handle, $startDate, $endDate)
    {
        fputcsv($handle, ['CUSTOMER REPORTS']);
        fputcsv($handle, ['Period:', $startDate->format('M d, Y') . ' to ' . $endDate->format('M d, Y')]);
        fputcsv($handle, []);
        
        // All Customers Section
        fputcsv($handle, ['=== ALL CUSTOMERS ===']);
        fputcsv($handle, ['Customer Name', 'Email', 'Total Bookings', 'Total Spent', 'Payment Methods', 'Last Booking', 'Customer Type']);
        
        $allCustomers = User::where('role', 'guest')
            ->withCount(['bookings' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->with(['bookings' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->with('payments')
                      ->orderByDesc('created_at');
            }])
            ->having('bookings_count', '>', 0)
            ->orderByDesc('bookings_count')
            ->get();
        
        foreach ($allCustomers as $customer) {
            $totalSpent = $customer->bookings->sum('total_price');
            $paymentMethods = $customer->bookings->flatMap->payments->pluck('payment_method')->unique()->filter();
            $paymentMethodsStr = $paymentMethods->map(function($method) {
                return ucfirst(str_replace('_', ' ', $method));
            })->join(', ');
            
            $lastBooking = $customer->bookings->first();
            $lastBookingDate = $lastBooking ? $lastBooking->created_at->format('M d, Y') : 'N/A';
            $isRepeatCustomer = $customer->bookings_count >= 2;
            
            fputcsv($handle, [
                $customer->name,
                $customer->email,
                $customer->bookings_count,
                '₱' . number_format($totalSpent, 2),
                $paymentMethodsStr ?: 'No payments',
                $lastBookingDate,
                $isRepeatCustomer ? 'Repeat Customer' : 'One-Time Customer'
            ]);
        }
        
        // Repeat Customers Section
        fputcsv($handle, []);
        fputcsv($handle, []);
        fputcsv($handle, ['=== REPEAT CUSTOMERS (2+ Bookings) ===']);
        fputcsv($handle, ['Customer Name', 'Email', 'Total Bookings', 'Completed Bookings', 'Total Spent', 'Payment Methods']);
        
        $repeatCustomers = User::where('role', 'guest')
            ->withCount(['bookings' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withCount(['bookings as completed_bookings_count' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed');
            }])
            ->with(['bookings' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->with('payments');
            }])
            ->having('bookings_count', '>=', 2)
            ->orderByDesc('bookings_count')
            ->get();
        
        foreach ($repeatCustomers as $customer) {
            $totalSpent = $customer->bookings->sum('total_price');
            $paymentMethods = Payment::where('user_id', $customer->id)
                ->whereHas('booking', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->pluck('payment_method')
                ->unique()
                ->filter();
            
            $paymentMethodsStr = $paymentMethods->map(function($method) {
                return ucfirst(str_replace('_', ' ', $method));
            })->join(', ');
            
            fputcsv($handle, [
                $customer->name,
                $customer->email,
                $customer->bookings_count,
                $customer->completed_bookings_count,
                '₱' . number_format($totalSpent, 2),
                $paymentMethodsStr ?: 'No payments'
            ]);
        }
    }

    /**
     * Export Customer Preferences Report
     */
    private function exportCustomerPreferences($handle, $startDate, $endDate)
    {
        fputcsv($handle, ['CUSTOMER PREFERENCES REPORT']);
        fputcsv($handle, ['Period:', $startDate->format('M d, Y') . ' to ' . $endDate->format('M d, Y')]);
        fputcsv($handle, []);
        
        // Room Preferences
        fputcsv($handle, ['=== ROOM TYPE PREFERENCES ===']);
        fputcsv($handle, ['Category', 'Bookings', 'Unique Customers']);
        
        $roomPreferences = Room::join('bookings', 'rooms.id', '=', 'bookings.room_id')
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->selectRaw('
                rooms.category,
                COUNT(bookings.id) as booking_count,
                COUNT(DISTINCT bookings.user_id) as unique_customers
            ')
            ->groupBy('rooms.category')
            ->orderByDesc('booking_count')
            ->get();
        
        foreach ($roomPreferences as $pref) {
            fputcsv($handle, [$pref->category, $pref->booking_count, $pref->unique_customers]);
        }
        fputcsv($handle, []);
        
        // Food Preferences
        fputcsv($handle, ['=== TOP FOOD ITEMS ===']);
        fputcsv($handle, ['Item', 'Category', 'Orders', 'Unique Customers']);
        
        $foodPreferences = MenuItem::join('order_items', 'menu_items.id', '=', 'order_items.menu_item_id')
            ->join('food_orders', 'order_items.food_order_id', '=', 'food_orders.id')
            ->join('menu_categories', 'menu_items.menu_category_id', '=', 'menu_categories.id')
            ->whereBetween('food_orders.created_at', [$startDate, $endDate])
            ->selectRaw('
                menu_items.name as item_name,
                menu_categories.name as category,
                COUNT(order_items.id) as total_orders,
                COUNT(DISTINCT food_orders.user_id) as unique_customers
            ')
            ->groupBy('menu_items.id', 'menu_items.name', 'menu_categories.name')
            ->orderByDesc('total_orders')
            ->take(20)
            ->get();
        
        foreach ($foodPreferences as $pref) {
            fputcsv($handle, [$pref->item_name, $pref->category, $pref->total_orders, $pref->unique_customers]);
        }
    }

    /**
     * Export Customer Analytics Summary
     */
    private function exportCustomerAnalytics($handle, $startDate, $endDate)
    {
        fputcsv($handle, ['CUSTOMER ANALYTICS SUMMARY']);
        fputcsv($handle, ['Period:', $startDate->format('M d, Y') . ' to ' . $endDate->format('M d, Y')]);
        fputcsv($handle, []);

        // Overview Statistics
        fputcsv($handle, ['=== OVERVIEW STATISTICS ===']);
        $totalCustomers = User::where('role', 'guest')
            ->withCount(['bookings' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->having('bookings_count', '>', 0)
            ->count();

        $repeatCustomers = User::where('role', 'guest')
            ->withCount(['bookings' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->having('bookings_count', '>=', 2)
            ->count();

        $retentionRate = $totalCustomers > 0 ? round(($repeatCustomers / $totalCustomers) * 100, 1) : 0;

        fputcsv($handle, ['Total Customers with Bookings', $totalCustomers]);
        fputcsv($handle, ['Repeat Customers (2+ bookings)', $repeatCustomers]);
        fputcsv($handle, ['Customer Retention Rate', $retentionRate . '%']);
        fputcsv($handle, []);

        // Top 5 Repeat Customers
        fputcsv($handle, ['=== TOP 5 REPEAT CUSTOMERS ===']);
        fputcsv($handle, ['Customer Name', 'Email', 'Total Bookings', 'Total Spent', 'Payment Methods']);

        $topRepeatCustomers = User::where('role', 'guest')
            ->withCount(['bookings' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->with(['bookings' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])->with('payments');
            }])
            ->having('bookings_count', '>=', 2)
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get();

        foreach ($topRepeatCustomers as $customer) {
            $totalSpent = $customer->bookings->sum('total_price');
            $paymentMethods = $customer->bookings->flatMap->payments->pluck('payment_method')->unique()->filter();
            $paymentMethodsStr = $paymentMethods->map(function($method) {
                return ucfirst(str_replace('_', ' ', $method));
            })->join(', ');

            fputcsv($handle, [
                $customer->name,
                $customer->email,
                $customer->bookings_count,
                '₱' . number_format($totalSpent, 2),
                $paymentMethodsStr ?: 'No payments'
            ]);
        }
        fputcsv($handle, []);

        // Top Payment Methods
        fputcsv($handle, ['=== TOP PAYMENT METHODS ===']);
        fputcsv($handle, ['Payment Method', 'Transactions', 'Total Amount']);

        $topPaymentMethods = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('payment_method')
            ->orderByDesc('count')
            ->get();

        foreach ($topPaymentMethods as $method) {
            fputcsv($handle, [
                ucfirst(str_replace('_', ' ', $method->payment_method)),
                $method->count,
                '₱' . number_format($method->total, 2)
            ]);
        }
        fputcsv($handle, []);

        // Top Room Preferences
        fputcsv($handle, ['=== TOP ROOM PREFERENCES ===']);
        fputcsv($handle, ['Room Category', 'Bookings', 'Unique Customers']);

        $topRoomPreferences = Booking::join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->selectRaw('rooms.category, COUNT(*) as booking_count, COUNT(DISTINCT bookings.user_id) as unique_customers')
            ->groupBy('rooms.category')
            ->orderByDesc('booking_count')
            ->get();

        foreach ($topRoomPreferences as $pref) {
            fputcsv($handle, [$pref->category, $pref->booking_count, $pref->unique_customers]);
        }
        fputcsv($handle, []);

        // Top Service Preferences
        fputcsv($handle, ['=== TOP SERVICE PREFERENCES ===']);
        fputcsv($handle, ['Service Name', 'Requests']);

        $topServicePreferences = ServiceRequest::join('services', 'service_requests.service_id', '=', 'services.id')
            ->whereBetween('service_requests.created_at', [$startDate, $endDate])
            ->selectRaw('services.name, COUNT(*) as request_count')
            ->groupBy('services.name')
            ->orderByDesc('request_count')
            ->limit(10)
            ->get();

        foreach ($topServicePreferences as $pref) {
            fputcsv($handle, [$pref->name, $pref->request_count]);
        }
        fputcsv($handle, []);

        // Top Food Items
        fputcsv($handle, ['=== TOP FOOD ITEMS ===']);
        fputcsv($handle, ['Item Name', 'Total Orders']);

        $topFoodItems = FoodOrder::join('order_items', 'food_orders.id', '=', 'order_items.food_order_id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->whereBetween('food_orders.created_at', [$startDate, $endDate])
            ->selectRaw('menu_items.name, SUM(order_items.quantity) as total_orders')
            ->groupBy('menu_items.name')
            ->orderByDesc('total_orders')
            ->limit(10)
            ->get();

        foreach ($topFoodItems as $item) {
            fputcsv($handle, [$item->name, $item->total_orders]);
        }
    }

    /**
     * Export Payment Methods Report
     */
    private function exportPaymentMethods($handle, $startDate, $endDate)
    {
        fputcsv($handle, ['PAYMENT METHODS REPORT']);
        fputcsv($handle, ['Period:', $startDate->format('M d, Y') . ' to ' . $endDate->format('M d, Y')]);
        fputcsv($handle, []);
        
        fputcsv($handle, ['Payment Method', 'Transactions', 'Total Amount', 'Percentage']);
        
        $paymentMethods = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                payment_method,
                COUNT(*) as count,
                SUM(amount) as total_amount
            ')
            ->groupBy('payment_method')
            ->orderByDesc('total_amount')
            ->get();
        
        $grandTotal = $paymentMethods->sum('total_amount');
        
        foreach ($paymentMethods as $method) {
            $percentage = $grandTotal > 0 ? round(($method->total_amount / $grandTotal) * 100, 1) : 0;
            
            fputcsv($handle, [
                ucfirst(str_replace('_', ' ', $method->payment_method)),
                $method->count,
                '₱' . number_format($method->total_amount, 2),
                $percentage . '%'
            ]);
        }
        
        fputcsv($handle, []);
        fputcsv($handle, ['GRAND TOTAL', $paymentMethods->sum('count'), '₱' . number_format($grandTotal, 2), '100%']);
    }

    /**
     * Get statistics for the given date range
     */
    protected function getStats($startDate, $endDate)
    {
        $totalRequests = ServiceRequest::whereBetween('created_at', [$startDate, $endDate])->count();
        
        $completedRequests = ServiceRequest::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();
        
        $pendingRequests = ServiceRequest::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'pending')
            ->count();
        
        // Fixed MySQL query for average response time
        $avgResponseTime = DB::table('service_requests')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('assigned_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, assigned_at)) as avg_hours')
            ->value('avg_hours') ?: 0;
        
        return [
            'total_requests' => $totalRequests,
            'completed_requests' => $completedRequests,
            'pending_requests' => $pendingRequests,
            'avg_response_time' => round($avgResponseTime, 1)
        ];
    }

    /**
     * Room Booking Sales Report
     */
    public function roomSales(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // Room booking statistics - Include soft-deleted bookings for historical accuracy
        $stats = [
            'total_bookings' => Booking::withTrashed()->whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed_bookings' => Booking::withTrashed()
                ->whereIn('status', ['completed', 'checked_out'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'cancelled_bookings' => Booking::withTrashed()
                ->where('status', 'cancelled')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'total_revenue' => Payment::whereNotNull('booking_id')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
            'avg_booking_value' => Payment::whereNotNull('booking_id')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->avg('amount'),
        ];

        // Revenue by Rooms category - Include soft-deleted bookings for historical data
        $revenueByRooms = Room::where('category', 'Rooms')
            ->leftJoin('bookings', function($join) {
                $join->on('rooms.id', '=', 'bookings.room_id');
            })
            ->leftJoin('payments', function($join) {
                $join->on('bookings.id', '=', 'payments.booking_id')
                     ->where('payments.status', '=', 'completed');
            })
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->selectRaw('
                rooms.id,
                rooms.name,
                COUNT(DISTINCT bookings.id) as booking_count,
                COALESCE(SUM(payments.amount), 0) as total_revenue
            ')
            ->groupBy('rooms.id', 'rooms.name')
            ->having('booking_count', '>', 0)
            ->orderByDesc('total_revenue')
            ->get();

        // Revenue by Cottages category - Include soft-deleted bookings for historical data
        $revenueByCottages = Room::where('category', 'Cottages')
            ->leftJoin('bookings', function($join) {
                $join->on('rooms.id', '=', 'bookings.room_id');
            })
            ->leftJoin('payments', function($join) {
                $join->on('bookings.id', '=', 'payments.booking_id')
                     ->where('payments.status', '=', 'completed');
            })
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->selectRaw('
                rooms.id,
                rooms.name,
                COUNT(DISTINCT bookings.id) as booking_count,
                COALESCE(SUM(payments.amount), 0) as total_revenue
            ')
            ->groupBy('rooms.id', 'rooms.name')
            ->having('booking_count', '>', 0)
            ->orderByDesc('total_revenue')
            ->get();

        // Revenue by Event and Dining category - Include soft-deleted bookings for historical data
        $revenueByEventDining = Room::where('category', 'Event and Dining')
            ->leftJoin('bookings', function($join) {
                $join->on('rooms.id', '=', 'bookings.room_id');
            })
            ->leftJoin('payments', function($join) {
                $join->on('bookings.id', '=', 'payments.booking_id')
                     ->where('payments.status', '=', 'completed');
            })
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->selectRaw('
                rooms.id,
                rooms.name,
                COUNT(DISTINCT bookings.id) as booking_count,
                COALESCE(SUM(payments.amount), 0) as total_revenue
            ')
            ->groupBy('rooms.id', 'rooms.name')
            ->having('booking_count', '>', 0)
            ->orderByDesc('total_revenue')
            ->get();

        // Revenue by category summary (for overview) - Include soft-deleted bookings
        $revenueByCategory = Room::leftJoin('bookings', function($join) {
                $join->on('rooms.id', '=', 'bookings.room_id');
            })
            ->leftJoin('payments', function($join) {
                $join->on('bookings.id', '=', 'payments.booking_id')
                     ->where('payments.status', '=', 'completed');
            })
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->selectRaw('
                rooms.category,
                COUNT(DISTINCT bookings.id) as booking_count,
                COALESCE(SUM(payments.amount), 0) as total_revenue
            ')
            ->groupBy('rooms.category')
            ->orderByDesc('total_revenue')
            ->get();

        // Daily revenue trends - using Payment model
        $dailyRevenue = Payment::whereNotNull('booking_id')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(amount) as revenue, COUNT(*) as bookings')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Booking status breakdown with accurate revenue from payments - Include soft-deleted
        $statusBreakdown = Booking::withTrashed()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
        
        // Add revenue from Payment model to each status
        $statusBreakdown = $statusBreakdown->map(function($status) use ($startDate, $endDate) {
            $status->revenue = Payment::whereHas('booking', function($query) use ($status, $startDate, $endDate) {
                $query->withTrashed()
                      ->where('status', $status->status)
                      ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->where('status', 'completed')
            ->sum('amount');
            return $status;
        });

        // Monthly comparison (if date range is large enough) - using Payment model
        $monthlyData = collect();
        if ($startDate->diffInMonths($endDate) >= 1) {
            $monthlyData = Payment::whereNotNull('booking_id')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('
                    YEAR(created_at) as year,
                    MONTH(created_at) as month,
                    COUNT(*) as total_bookings,
                    SUM(amount) as revenue
                ')
                ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                ->orderByRaw('year, month')
                ->get();
        }

        // Top 3 facilities per category - Include soft-deleted bookings
        $topRooms = Room::where('category', 'Rooms')
            ->withCount([
                'bookings as booking_count' => function ($query) use ($startDate, $endDate) {
                    $query->withTrashed()->whereBetween('created_at', [$startDate, $endDate]);
                }
            ])
            ->having('booking_count', '>', 0)
            ->orderByDesc('booking_count')
            ->take(3)
            ->get();

        $topCottages = Room::where('category', 'Cottages')
            ->withCount([
                'bookings as booking_count' => function ($query) use ($startDate, $endDate) {
                    $query->withTrashed()->whereBetween('created_at', [$startDate, $endDate]);
                }
            ])
            ->having('booking_count', '>', 0)
            ->orderByDesc('booking_count')
            ->take(3)
            ->get();

        $topEventDining = Room::where('category', 'Event and Dining')
            ->withCount([
                'bookings as booking_count' => function ($query) use ($startDate, $endDate) {
                    $query->withTrashed()->whereBetween('created_at', [$startDate, $endDate]);
                }
            ])
            ->having('booking_count', '>', 0)
            ->orderByDesc('booking_count')
            ->take(3)
            ->get();

        $routePrefix = $this->getRoutePrefix();

        return view('manager.reports.room-sales', compact(
            'stats',
            'revenueByRooms',
            'revenueByCottages',
            'revenueByEventDining',
            'revenueByCategory',
            'dailyRevenue',
            'statusBreakdown',
            'monthlyData',
            'topRooms',
            'topCottages',
            'topEventDining',
            'startDate',
            'endDate',
            'routePrefix'
        ));
    }

    /**
     * Food Order Sales Report
     */
    public function foodSales(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // Food order statistics - using actual payment data for accurate revenue
        $stats = [
            'total_orders' => FoodOrder::whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed_orders' => FoodOrder::whereIn('status', ['delivered', 'completed'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'cancelled_orders' => FoodOrder::where('status', 'cancelled')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'total_revenue' => Payment::whereNotNull('food_order_id')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
            'avg_order_value' => Payment::whereNotNull('food_order_id')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->avg('amount'),
        ];

        // Revenue by menu item - using completed orders
        $revenueByItem = MenuItem::join('order_items', 'menu_items.id', '=', 'order_items.menu_item_id')
            ->join('food_orders', 'order_items.food_order_id', '=', 'food_orders.id')
            ->whereIn('food_orders.status', ['delivered', 'completed'])
            ->whereBetween('food_orders.created_at', [$startDate, $endDate])
            ->selectRaw('
                menu_items.id,
                menu_items.name,
                menu_items.price,
                SUM(order_items.quantity) as total_quantity,
                SUM(order_items.total_price) as total_revenue
            ')
            ->groupBy('menu_items.id', 'menu_items.name', 'menu_items.price')
            ->orderByDesc('total_revenue')
            ->get();

        // Revenue by category
        $revenueByCategory = MenuCategory::join('menu_items', 'menu_categories.id', '=', 'menu_items.menu_category_id')
            ->join('order_items', 'menu_items.id', '=', 'order_items.menu_item_id')
            ->join('food_orders', 'order_items.food_order_id', '=', 'food_orders.id')
            ->whereIn('food_orders.status', ['delivered', 'completed'])
            ->whereBetween('food_orders.created_at', [$startDate, $endDate])
            ->selectRaw('
                menu_categories.name as category,
                COUNT(DISTINCT food_orders.id) as order_count,
                SUM(order_items.total_price) as total_revenue
            ')
            ->groupBy('menu_categories.id', 'menu_categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        // Daily revenue trends - using payment data
        $dailyRevenue = Payment::whereNotNull('food_order_id')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(amount) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Order status breakdown
        $statusBreakdown = FoodOrder::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Add revenue to status breakdown
        $statusBreakdown = $statusBreakdown->map(function($status) use ($startDate, $endDate) {
            $status->revenue = Payment::whereHas('foodOrder', function($query) use ($status, $startDate, $endDate) {
                $query->where('status', $status->status)
                      ->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->where('status', 'completed')
            ->sum('amount');
            return $status;
        });

        // Top 10 menu items by quantity sold
        $topMenuItems = MenuItem::join('order_items', 'menu_items.id', '=', 'order_items.menu_item_id')
            ->join('food_orders', 'order_items.food_order_id', '=', 'food_orders.id')
            ->whereIn('food_orders.status', ['delivered', 'completed'])
            ->whereBetween('food_orders.created_at', [$startDate, $endDate])
            ->selectRaw('
                menu_items.id,
                menu_items.name,
                menu_items.price,
                SUM(order_items.quantity) as total_quantity,
                SUM(order_items.total_price) as total_revenue
            ')
            ->groupBy('menu_items.id', 'menu_items.name', 'menu_items.price')
            ->orderByDesc('total_quantity')
            ->take(10)
            ->get();

        $routePrefix = $this->getRoutePrefix();

        return view('manager.reports.food-sales', compact(
            'stats',
            'revenueByItem',
            'revenueByCategory',
            'dailyRevenue',
            'statusBreakdown',
            'topMenuItems',
            'startDate',
            'endDate',
            'routePrefix'
        ));
    }

    /**
     * Service Revenue Report
     */
    public function serviceSales(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // Get payments related to service requests
        $stats = [
            'total_requests' => ServiceRequest::whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed_requests' => ServiceRequest::where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'cancelled_requests' => ServiceRequest::where('status', 'cancelled')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'total_revenue' => \App\Models\Payment::whereNotNull('service_request_id')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
            'avg_service_value' => \App\Models\Payment::whereNotNull('service_request_id')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->avg('amount'),
        ];

        // Revenue by service type
        $revenueByService = Service::join('service_requests', 'services.id', '=', 'service_requests.service_id')
            ->join('payments', 'service_requests.id', '=', 'payments.service_request_id')
            ->where('payments.status', 'completed')
            ->whereBetween('payments.created_at', [$startDate, $endDate])
            ->selectRaw('
                services.id,
                services.name,
                services.price,
                COUNT(DISTINCT service_requests.id) as request_count,
                SUM(payments.amount) as total_revenue
            ')
            ->groupBy('services.id', 'services.name', 'services.price')
            ->orderByDesc('total_revenue')
            ->get();

        // Revenue by category
        $revenueByCategory = Service::join('service_requests', 'services.id', '=', 'service_requests.service_id')
            ->join('payments', 'service_requests.id', '=', 'payments.service_request_id')
            ->where('payments.status', 'completed')
            ->whereBetween('payments.created_at', [$startDate, $endDate])
            ->selectRaw('
                services.category,
                COUNT(DISTINCT service_requests.id) as request_count,
                SUM(payments.amount) as total_revenue
            ')
            ->groupBy('services.category')
            ->orderByDesc('total_revenue')
            ->get();

        // Daily revenue trends
        $dailyRevenue = \App\Models\Payment::whereNotNull('service_request_id')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(amount) as revenue, COUNT(*) as payments')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Status breakdown
        $statusBreakdown = ServiceRequest::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Top 10 services by request count
        $topServices = Service::join('service_requests', 'services.id', '=', 'service_requests.service_id')
            ->join('payments', 'service_requests.id', '=', 'payments.service_request_id')
            ->where('payments.status', 'completed')
            ->whereBetween('payments.created_at', [$startDate, $endDate])
            ->selectRaw('
                services.id,
                services.name,
                services.price,
                services.category,
                COUNT(DISTINCT service_requests.id) as request_count,
                SUM(payments.amount) as total_revenue
            ')
            ->groupBy('services.id', 'services.name', 'services.price', 'services.category')
            ->orderByDesc('request_count')
            ->take(10)
            ->get();

        $routePrefix = $this->getRoutePrefix();

        return view('manager.reports.service-sales', compact(
            'stats',
            'revenueByService',
            'revenueByCategory',
            'dailyRevenue',
            'statusBreakdown',
            'topServices',
            'startDate',
            'endDate',
            'routePrefix'
        ));
    }

    /**
     * Customer Analytics Summary
     */
    public function customerAnalytics(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // Customer Statistics
        $totalCustomers = User::where('role', 'guest')
            ->withCount(['bookings' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->having('bookings_count', '>', 0)
            ->count();

        $repeatCustomers = User::where('role', 'guest')
            ->withCount(['bookings' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->having('bookings_count', '>=', 2)
            ->count();

        $retentionRate = $totalCustomers > 0 ? round(($repeatCustomers / $totalCustomers) * 100, 1) : 0;

        // Top 5 Repeat Customers
        $topRepeatCustomers = User::where('role', 'guest')
            ->withCount(['bookings' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->with(['bookings' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])->with('payments');
            }])
            ->having('bookings_count', '>=', 2)
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get();

        // Most Popular Payment Methods
        $topPaymentMethods = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('payment_method')
            ->orderByDesc('count')
            ->limit(3)
            ->get();

        // Top Room Preferences
        $topRoomPreferences = Booking::join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->selectRaw('rooms.category, COUNT(*) as booking_count, COUNT(DISTINCT bookings.user_id) as unique_customers')
            ->groupBy('rooms.category')
            ->orderByDesc('booking_count')
            ->limit(3)
            ->get();

        // Top Service Preferences
        $topServicePreferences = ServiceRequest::join('services', 'service_requests.service_id', '=', 'services.id')
            ->whereBetween('service_requests.created_at', [$startDate, $endDate])
            ->selectRaw('services.name, COUNT(*) as request_count')
            ->groupBy('services.name')
            ->orderByDesc('request_count')
            ->limit(3)
            ->get();

        // Top Food Items
        $topFoodItems = FoodOrder::join('order_items', 'food_orders.id', '=', 'order_items.food_order_id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->whereBetween('food_orders.created_at', [$startDate, $endDate])
            ->selectRaw('menu_items.name, SUM(order_items.quantity) as total_orders')
            ->groupBy('menu_items.name')
            ->orderByDesc('total_orders')
            ->limit(3)
            ->get();

        $routePrefix = $this->getRoutePrefix();

        return view('manager.reports.customer-analytics', compact(
            'totalCustomers',
            'repeatCustomers',
            'retentionRate',
            'topRepeatCustomers',
            'topPaymentMethods',
            'topRoomPreferences',
            'topServicePreferences',
            'topFoodItems',
            'startDate',
            'endDate',
            'routePrefix'
        ));
    }

    /**
     * Customer Reports - Repeat Customers
     */
    public function repeatCustomers(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // Get customers with multiple bookings
        $repeatCustomers = User::where('role', 'guest')
            ->withCount([
                'bookings as total_bookings' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                },
                'bookings as completed_bookings' => function ($query) use ($startDate, $endDate) {
                    $query->where('status', 'checked_out')
                          ->whereBetween('created_at', [$startDate, $endDate]);
                }
            ])
            ->withSum([
                'payments as total_spent' => function ($query) use ($startDate, $endDate) {
                    $query->where('status', 'completed')
                          ->whereBetween('created_at', [$startDate, $endDate]);
                }
            ], 'amount')
            ->having('total_bookings', '>=', 2)
            ->orderByDesc('total_bookings')
            ->paginate(20);

        // Statistics
        $stats = [
            'total_customers' => User::where('role', 'guest')->count(),
            'repeat_customers' => User::where('role', 'guest')
                ->has('bookings', '>=', 2)
                ->count(),
            'one_time_customers' => User::where('role', 'guest')
                ->has('bookings', '=', 1)
                ->count(),
            'avg_bookings_per_customer' => round(Booking::count() / max(User::where('role', 'guest')->count(), 1), 2),
        ];

        $stats['retention_rate'] = $stats['total_customers'] > 0 
            ? round(($stats['repeat_customers'] / $stats['total_customers']) * 100, 1) 
            : 0;

        $routePrefix = $this->getRoutePrefix();

        return view('manager.reports.repeat-customers', compact(
            'repeatCustomers',
            'stats',
            'startDate',
            'endDate',
            'routePrefix'
        ));
    }

    /**
     * Customer Reports - Customer Preferences
     */
    public function customerPreferences(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // Room Type Preferences
        $roomPreferences = Booking::join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->selectRaw('
                rooms.category,
                COUNT(*) as booking_count,
                COUNT(DISTINCT bookings.user_id) as unique_customers
            ')
            ->groupBy('rooms.category')
            ->orderByDesc('booking_count')
            ->get();

        // Service Preferences
        $servicePreferences = ServiceRequest::join('services', 'service_requests.service_id', '=', 'services.id')
            ->whereBetween('service_requests.created_at', [$startDate, $endDate])
            ->selectRaw('
                services.category,
                services.name,
                COUNT(*) as request_count,
                COUNT(DISTINCT service_requests.user_id) as unique_customers
            ')
            ->groupBy('services.category', 'services.name')
            ->orderByDesc('request_count')
            ->get();

        // Food Preferences (Top Menu Items)
        $foodPreferences = FoodOrder::join('order_items', 'food_orders.id', '=', 'order_items.food_order_id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->join('menu_categories', 'menu_items.menu_category_id', '=', 'menu_categories.id')
            ->whereBetween('food_orders.created_at', [$startDate, $endDate])
            ->selectRaw('
                menu_categories.name as category,
                menu_items.name as item_name,
                SUM(order_items.quantity) as total_orders,
                COUNT(DISTINCT food_orders.user_id) as unique_customers
            ')
            ->groupBy('menu_categories.name', 'menu_items.name')
            ->orderByDesc('total_orders')
            ->take(20)
            ->get();

        // Peak Booking Times
        $bookingTimes = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                CASE DAYOFWEEK(check_in)
                    WHEN 1 THEN "Sunday"
                    WHEN 2 THEN "Monday"
                    WHEN 3 THEN "Tuesday"
                    WHEN 4 THEN "Wednesday"
                    WHEN 5 THEN "Thursday"
                    WHEN 6 THEN "Friday"
                    WHEN 7 THEN "Saturday"
                END as day_name,
                COUNT(*) as booking_count
            ')
            ->groupBy('day_name')
            ->orderByRaw('DAYOFWEEK(check_in)')
            ->get();

        $routePrefix = $this->getRoutePrefix();

        return view('manager.reports.customer-preferences', compact(
            'roomPreferences',
            'servicePreferences',
            'foodPreferences',
            'bookingTimes',
            'startDate',
            'endDate',
            'routePrefix'
        ));
    }

    /**
     * Customer Reports - Payment Methods Analysis
     */
    public function paymentMethods(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // Payment Method Breakdown
        $paymentMethodStats = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                payment_method,
                COUNT(*) as transaction_count,
                SUM(amount) as total_amount,
                AVG(amount) as avg_transaction
            ')
            ->groupBy('payment_method')
            ->orderByDesc('total_amount')
            ->get();

        // Payment Methods by Source (Booking, Food, Service)
        $paymentsBySource = [
            'bookings' => Payment::whereNotNull('booking_id')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
                ->groupBy('payment_method')
                ->get(),
            'food_orders' => Payment::whereNotNull('food_order_id')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
                ->groupBy('payment_method')
                ->get(),
            'services' => Payment::whereNotNull('service_request_id')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
                ->groupBy('payment_method')
                ->get(),
        ];

        // Daily Payment Trends
        $dailyPayments = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                DATE(created_at) as date,
                payment_method,
                COUNT(*) as count,
                SUM(amount) as total
            ')
            ->groupBy('date', 'payment_method')
            ->orderBy('date')
            ->get();

        // Statistics
        $stats = [
            'total_transactions' => Payment::where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'total_revenue' => Payment::where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
            'avg_transaction' => Payment::where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->avg('amount') ?? 0,
            'most_popular_method' => $paymentMethodStats->isNotEmpty() 
                ? $paymentMethodStats->first()->payment_method 
                : 'N/A',
        ];

        $routePrefix = $this->getRoutePrefix();

        return view('manager.reports.payment-methods', compact(
            'paymentMethodStats',
            'paymentsBySource',
            'dailyPayments',
            'stats',
            'startDate',
            'endDate',
            'routePrefix'
        ));
    }
}

