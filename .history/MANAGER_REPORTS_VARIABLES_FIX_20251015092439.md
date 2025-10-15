# Manager Reports Missing Variables Fix

## Date: October 15, 2025

## Error Reported

```
Error: Undefined variable $staffPerformance 
View: C:\Users\sethy\valesbeach\resources\views\manager\reports\index.blade.php
Line: 160
```

## Root Cause

The `manager.reports.index` view requires multiple data variables that were not being provided by the controller:

### Required Variables:
1. ✅ `$stats` - Already provided
2. ✅ `$startDate` - Already provided
3. ✅ `$endDate` - Already provided
4. ❌ `$staffPerformance` - **MISSING**
5. ❌ `$serviceUsage` - **MISSING**
6. ❌ `$performanceMetrics` - **MISSING**
7. ❌ `$dailyTrends` - **MISSING**

## Solution Implemented

Updated `ReportsController@index()` method to query and provide all required data:

### 1. Service Usage Data
```php
$serviceUsage = Service::withCount([
    'serviceRequests as request_count' => function ($query) use ($startDate, $endDate) {
        $query->whereBetween('created_at', [$startDate, $endDate]);
    }
])
->having('request_count', '>', 0)
->orderBy('request_count', 'desc')
->limit(10)
->get();
```
**Purpose:** Top 10 services by request count for chart display

### 2. Performance Metrics
```php
$performanceMetrics = ServiceRequest::whereBetween('created_at', [$startDate, $endDate])
    ->selectRaw('status, COUNT(*) as count')
    ->groupBy('status')
    ->get();
```
**Purpose:** Service request counts grouped by status (pending, completed, etc.) for pie chart

### 3. Staff Performance
```php
$staffPerformance = User::where('role', 'staff')
    ->withCount([
        'assignedServiceRequests as assigned_count' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
    ])
    ->having('assigned_count', '>', 0)
    ->orderBy('assigned_count', 'desc')
    ->get();
```
**Purpose:** Staff members with their assigned task counts for performance table

### 4. Daily Trends
```php
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
```
**Purpose:** Daily request counts for line chart showing trends over time

## Updated Controller Method

**File:** `app/Http/Controllers/Manager/ReportsController.php`

```php
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
    $serviceUsage = Service::withCount([...])->get();
    
    // Performance metrics by status
    $performanceMetrics = ServiceRequest::whereBetween([...])->get();
    
    // Staff performance data
    $staffPerformance = User::where('role', 'staff')->withCount([...])->get();
    
    // Daily trends for the date range
    $dailyTrends = collect([...]);

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
```

## View Usage

### Staff Performance Table (Line 160)
```blade
@forelse($staffPerformance as $staff)
<tr>
    <td>{{ $staff->name }}</td>
    <td><span class="badge bg-info">{{ $staff->assigned_count }}</span></td>
    <td><!-- Progress bar --></td>
</tr>
@empty
<tr>
    <td colspan="3">No staff performance data available</td>
</tr>
@endforelse
```

### Service Usage Chart (Line 299)
```javascript
labels: {!! json_encode($serviceUsage->pluck('name')->take(10)) !!},
data: {!! json_encode($serviceUsage->pluck('request_count')->take(10)) !!}
```

### Status Distribution Chart (Line 324)
```javascript
labels: {!! json_encode($performanceMetrics->pluck('status')) !!},
data: {!! json_encode($performanceMetrics->pluck('count')) !!}
```

### Daily Trends Chart
```javascript
labels: {!! json_encode($dailyTrends->pluck('date')) !!},
data: {!! json_encode($dailyTrends->pluck('request_count')) !!}
```

## Data Structure

### $staffPerformance
```php
Collection [
    User {
        id: 1,
        name: "John Doe",
        role: "staff",
        assigned_count: 15
    },
    ...
]
```

### $serviceUsage
```php
Collection [
    Service {
        id: 1,
        name: "Room Cleaning",
        request_count: 42
    },
    ...
]
```

### $performanceMetrics
```php
Collection [
    ['status' => 'completed', 'count' => 50],
    ['status' => 'pending', 'count' => 20],
    ['status' => 'in_progress', 'count' => 15],
    ...
]
```

### $dailyTrends
```php
Collection [
    ['date' => 'Oct 08', 'request_count' => 12],
    ['date' => 'Oct 09', 'request_count' => 15],
    ['date' => 'Oct 10', 'request_count' => 18],
    ...
]
```

## Dependencies

### Required Relationships in Models:

**Service Model:**
- `serviceRequests()` - hasMany relationship

**User Model:**
- `assignedServiceRequests()` - hasMany relationship (already exists)

## Testing Recommendations

1. **Access Reports Page:**
   - Navigate to `manager/reports`
   - ✅ Verify page loads without errors

2. **Check Staff Performance Table:**
   - Verify staff names display
   - Verify task counts show
   - ✅ No "Undefined variable $staffPerformance" error

3. **Check Charts:**
   - Verify Service Usage bar chart displays
   - Verify Status Distribution pie chart displays
   - Verify Daily Trends line chart displays
   - ✅ All charts render with data

4. **Test Date Range:**
   - Select different date ranges
   - Verify all data updates accordingly
   - ✅ All queries respect date range

5. **Test Empty States:**
   - Test with date range that has no data
   - ✅ Verify "No data available" messages show

## Files Modified

1. `app/Http/Controllers/Manager/ReportsController.php`
   - Updated `index()` method (lines 20-86)
   - Added queries for: `$serviceUsage`, `$performanceMetrics`, `$staffPerformance`, `$dailyTrends`

## Summary

✅ **Fixed undefined variable error** - Added `$staffPerformance` to controller  
✅ **Added missing chart data** - Provided `$serviceUsage`, `$performanceMetrics`, `$dailyTrends`  
✅ **All queries respect date range** - Filtered by `$startDate` and `$endDate`  
✅ **Optimized queries** - Used `withCount()` and aggregations  
✅ **Complete dashboard** - All charts and tables now have data  

The manager reports dashboard now loads correctly with all required data for statistics cards, performance tables, and interactive charts.
