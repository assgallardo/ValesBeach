<?php $__env->startSection('title', 'Performance Metrics'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-900 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <nav class="flex mb-2" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            <li class="inline-flex items-center">
                                <a href="<?php echo e(route('manager.reports.index', request()->query())); ?>" 
                                   class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-green-400">
                                    <i class="fas fa-chart-line mr-2"></i>
                                    Reports Dashboard
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <i class="fas fa-chevron-right text-gray-600 mx-2"></i>
                                    <span class="text-sm font-medium text-gray-300">Performance Metrics</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-3xl font-bold text-green-50">Performance Metrics Report</h1>
                    <p class="text-gray-400 mt-2">Response times and efficiency analysis</p>
                </div>
                <div class="flex space-x-3">
                    <a href="<?php echo e(route('manager.reports.index', request()->query())); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-gray-700 text-gray-300 rounded-lg font-medium hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                    <a href="<?php echo e(route('manager.reports.export', ['type' => 'performance-metrics'] + request()->query())); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>Export CSV
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
                        Showing data from <strong><?php echo e($startDate->format('M d, Y')); ?></strong> to <strong><?php echo e($endDate->format('M d, Y')); ?></strong>
                    </span>
                </div>
            </div>
        </div>

        <!-- Key Performance Indicators -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-green-50 mb-2"><?php echo e(round($avgResponseTime, 1)); ?>h</h3>
                    <p class="text-gray-400 text-sm uppercase tracking-wider font-medium mb-1">Average Response Time</p>
                    <small class="text-gray-500">Time to assign request</small>
                </div>
            </div>
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-stopwatch text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-green-50 mb-2"><?php echo e(round($avgCompletionTime, 1)); ?>h</h3>
                    <p class="text-gray-400 text-sm uppercase tracking-wider font-medium mb-1">Average Completion Time</p>
                    <small class="text-gray-500">Time from assignment to completion</small>
                </div>
            </div>
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tachometer-alt text-2xl text-white"></i>
                    </div>
                    <?php 
                        $totalTime = $avgResponseTime + $avgCompletionTime;
                        $efficiency = $totalTime > 0 ? min(100, max(0, 100 - ($totalTime * 2))) : 0;
                    ?>
                    <h3 class="text-3xl font-bold text-green-50 mb-2"><?php echo e(round($efficiency, 1)); ?>%</h3>
                    <p class="text-gray-400 text-sm uppercase tracking-wider font-medium mb-1">Service Efficiency</p>
                    <small class="text-gray-500">Based on response + completion time</small>
                </div>
            </div>
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-line text-2xl text-white"></i>
                    </div>
                    <?php 
                        $completedCount = $statusMetrics->where('status', 'completed')->first()->count ?? 0;
                        $totalCount = $statusMetrics->sum('count');
                        $completionRate = $totalCount > 0 ? ($completedCount / $totalCount) * 100 : 0;
                    ?>
                    <h3 class="text-3xl font-bold text-green-50 mb-2"><?php echo e(round($completionRate, 1)); ?>%</h3>
                    <p class="text-gray-400 text-sm uppercase tracking-wider font-medium mb-1">Overall Completion Rate</p>
                    <small class="text-gray-500"><?php echo e($completedCount); ?> of <?php echo e($totalCount); ?> requests</small>
                </div>
            </div>
        </div>

        <!-- Status Breakdown and Chart -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Status Table -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-green-100">Request Status Distribution</h3>
                </div>
                <div class="overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-750">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Count</th>
                                    <th class="px-6 py-4 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Percentage</th>
                                    <th class="px-6 py-4 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Avg Duration (Hours)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                <?php $__currentLoopData = $statusMetrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-750 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <?php switch($status->status):
                                                case ('completed'): ?>
                                                    <i class="fas fa-check-circle text-green-400 mr-2"></i>
                                                    <?php break; ?>
                                                <?php case ('pending'): ?>
                                                    <i class="fas fa-clock text-yellow-400 mr-2"></i>
                                                    <?php break; ?>
                                                <?php case ('in_progress'): ?>
                                                    <i class="fas fa-spinner text-blue-400 mr-2"></i>
                                                    <?php break; ?>
                                                <?php case ('cancelled'): ?>
                                                    <i class="fas fa-times-circle text-red-400 mr-2"></i>
                                                    <?php break; ?>
                                                <?php default: ?>
                                                    <i class="fas fa-circle text-gray-400 mr-2"></i>
                                            <?php endswitch; ?>
                                            <span class="text-green-100 capitalize"><?php echo e(str_replace('_', ' ', $status->status)); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-600/20 text-blue-400">
                                            <?php echo e(number_format($status->count)); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-300">
                                        <?php echo e(round(($status->count / $statusMetrics->sum('count')) * 100, 1)); ?>%
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-300">
                                        <?php echo e(round($status->avg_duration ?? 0, 1)); ?>h
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Status Chart -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-green-100">Status Overview</h3>
                </div>
                <div class="p-6">
                    <div class="relative" style="height: 300px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Peak Hours Analysis -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden mb-8">
            <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-green-100">Peak Hours Analysis</h3>
            </div>
            <div class="p-6">
                <div class="relative" style="height: 300px;">
                    <canvas id="peakHoursChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Monthly Trends (if applicable) -->
        <?php if(!empty($monthlyTrends) && count($monthlyTrends) > 0): ?>
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden mb-8">
            <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-green-100">Monthly Performance Trends</h3>
            </div>
            <div class="p-6">
                <div class="relative" style="height: 300px;">
                    <canvas id="monthlyTrendsChart"></canvas>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Performance Insights -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
            <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-green-100">Performance Insights & Recommendations</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Response Time Insight -->
                    <div class="bg-gray-750 rounded-lg p-4 border border-gray-600">
                        <div class="flex items-start">
                            <div class="mr-4">
                                <?php if($avgResponseTime < 2): ?>
                                    <i class="fas fa-check-circle text-green-400 text-2xl"></i>
                                <?php elseif($avgResponseTime < 6): ?>
                                    <i class="fas fa-exclamation-triangle text-yellow-400 text-2xl"></i>
                                <?php else: ?>
                                    <i class="fas fa-times-circle text-red-400 text-2xl"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h6 class="text-green-100 font-semibold mb-2">Response Time Analysis</h6>
                                <?php if($avgResponseTime < 2): ?>
                                    <p class="text-green-400 text-sm">Excellent response time! Requests are being assigned quickly.</p>
                                <?php elseif($avgResponseTime < 6): ?>
                                    <p class="text-yellow-400 text-sm">Response time is acceptable but could be improved. Consider optimizing staff allocation.</p>
                                <?php else: ?>
                                    <p class="text-red-400 text-sm">Response time needs improvement. Review staffing levels and assignment processes.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Completion Rate Insight -->
                    <div class="bg-gray-750 rounded-lg p-4 border border-gray-600">
                        <div class="flex items-start">
                            <div class="mr-4">
                                <?php if($completionRate > 85): ?>
                                    <i class="fas fa-trophy text-green-400 text-2xl"></i>
                                <?php elseif($completionRate > 70): ?>
                                    <i class="fas fa-star text-yellow-400 text-2xl"></i>
                                <?php else: ?>
                                    <i class="fas fa-flag text-red-400 text-2xl"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h6 class="text-green-100 font-semibold mb-2">Completion Rate Analysis</h6>
                                <?php if($completionRate > 85): ?>
                                    <p class="text-green-400 text-sm">Outstanding completion rate! Service delivery is highly effective.</p>
                                <?php elseif($completionRate > 70): ?>
                                    <p class="text-yellow-400 text-sm">Good completion rate. Monitor cancelled requests to identify improvement areas.</p>
                                <?php else: ?>
                                    <p class="text-red-400 text-sm">Completion rate needs attention. Review service processes and staff training.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Peak Hours Insight -->
                    <div class="bg-gray-750 rounded-lg p-4 border border-gray-600">
                        <div class="flex items-start">
                            <div class="mr-4">
                                <i class="fas fa-chart-line text-blue-400 text-2xl"></i>
                            </div>
                            <div>
                                <h6 class="text-green-100 font-semibold mb-2">Peak Hours Optimization</h6>
                                <?php if($peakHours->count() > 0): ?>
                                    <?php $peakHour = $peakHours->first(); ?>
                                    <p class="text-blue-400 text-sm">
                                        Peak activity at <?php echo e($peakHour->hour); ?>:00 with <?php echo e($peakHour->request_count); ?> requests. 
                                        Consider additional staffing during peak hours.
                                    </p>
                                <?php else: ?>
                                    <p class="text-gray-400 text-sm">No peak hour data available for the selected period.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Overall Efficiency -->
                    <div class="bg-gray-750 rounded-lg p-4 border border-gray-600">
                        <div class="flex items-start">
                            <div class="mr-4">
                                <?php if($efficiency > 80): ?>
                                    <i class="fas fa-rocket text-green-400 text-2xl"></i>
                                <?php elseif($efficiency > 60): ?>
                                    <i class="fas fa-cogs text-yellow-400 text-2xl"></i>
                                <?php else: ?>
                                    <i class="fas fa-wrench text-red-400 text-2xl"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h6 class="text-green-100 font-semibold mb-2">Service Efficiency</h6>
                                <?php if($efficiency > 80): ?>
                                    <p class="text-green-400 text-sm">Highly efficient service delivery. Maintain current processes and standards.</p>
                                <?php elseif($efficiency > 60): ?>
                                    <p class="text-yellow-400 text-sm">Moderate efficiency. Focus on reducing response and completion times.</p>
                                <?php else: ?>
                                    <p class="text-red-400 text-sm">Efficiency needs improvement. Review entire service delivery workflow.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart.js defaults for dark theme
Chart.defaults.color = '#9ca3af';
Chart.defaults.borderColor = '#374151';
Chart.defaults.backgroundColor = 'rgba(55, 65, 81, 0.1)';

// Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($statusMetrics->pluck('status')->map(function($status) { return ucfirst(str_replace('_', ' ', $status)); })); ?>,
        datasets: [{
            data: <?php echo json_encode($statusMetrics->pluck('count')); ?>,
            backgroundColor: [
                'rgba(34, 197, 94, 0.8)',   // completed - green
                'rgba(245, 158, 11, 0.8)',  // pending - yellow
                'rgba(59, 130, 246, 0.8)',  // in_progress - blue
                'rgba(239, 68, 68, 0.8)',   // cancelled - red
                'rgba(156, 163, 175, 0.8)'  // other - gray
            ],
            borderWidth: 2,
            borderColor: '#111827'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
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
                padding: 12
            }
        }
    }
});

// Peak Hours Chart
const peakHoursCtx = document.getElementById('peakHoursChart').getContext('2d');
new Chart(peakHoursCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($peakHours->pluck('hour')->map(function($hour) { return $hour . ':00'; })); ?>,
        datasets: [{
            label: 'Requests',
            data: <?php echo json_encode($peakHours->pluck('request_count')); ?>,
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
                },
                title: {
                    display: true,
                    text: 'Number of Requests',
                    color: '#9ca3af'
                }
            },
            x: {
                ticks: {
                    color: '#9ca3af'
                },
                grid: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Hour of Day',
                    color: '#9ca3af'
                }
            }
        }
    }
});

<?php if(!empty($monthlyTrends) && count($monthlyTrends) > 0): ?>
// Monthly Trends Chart
const monthlyTrendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
new Chart(monthlyTrendsCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($monthlyTrends->map(function($trend) { return date('M Y', mktime(0, 0, 0, $trend->month, 1, $trend->year)); })); ?>,
        datasets: [{
            label: 'Total Requests',
            data: <?php echo json_encode($monthlyTrends->pluck('total_requests')); ?>,
            fill: false,
            borderColor: 'rgba(59, 130, 246, 1)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.1,
            pointRadius: 6,
            pointHoverRadius: 8
        }, {
            label: 'Completed Requests',
            data: <?php echo json_encode($monthlyTrends->pluck('completed_requests')); ?>,
            fill: false,
            borderColor: 'rgba(34, 197, 94, 1)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.1,
            pointRadius: 6,
            pointHoverRadius: 8
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
<?php endif; ?>
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\manager\reports\performance-metrics.blade.php ENDPATH**/ ?>