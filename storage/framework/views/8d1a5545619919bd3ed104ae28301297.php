<?php $__env->startSection('title', 'Staff Performance Report'); ?>

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
                                    <span class="text-sm font-medium text-gray-300">Staff Performance</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-3xl font-bold text-green-50">Staff Performance Report</h1>
                    <p class="text-gray-400 mt-2">Individual staff productivity and performance analysis</p>
                </div>
                <div class="flex space-x-3">
                    <a href="<?php echo e(route('manager.reports.index', request()->query())); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-gray-700 text-gray-300 rounded-lg font-medium hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                    <a href="<?php echo e(route('manager.reports.export', ['type' => 'staff-performance'] + request()->query())); ?>" 
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

        <!-- Team Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-green-50 mb-2"><?php echo e($staffMetrics->count()); ?></h3>
                    <p class="text-gray-400 text-sm uppercase tracking-wider font-medium">Active Staff Members</p>
                </div>
            </div>

            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tasks text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-green-50 mb-2"><?php echo e(number_format($staffMetrics->sum('total_assigned'))); ?></h3>
                    <p class="text-gray-400 text-sm uppercase tracking-wider font-medium">Total Tasks Assigned</p>
                </div>
            </div>

            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-double text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-green-50 mb-2"><?php echo e(number_format($staffMetrics->sum('completed_tasks'))); ?></h3>
                    <p class="text-gray-400 text-sm uppercase tracking-wider font-medium">Tasks Completed</p>
                </div>
            </div>

            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-percentage text-2xl text-white"></i>
                    </div>
                    <?php 
                        $totalAssigned = $staffMetrics->sum('total_assigned');
                        $totalCompleted = $staffMetrics->sum('completed_tasks');
                        $avgCompletionRate = $totalAssigned > 0 ? ($totalCompleted / $totalAssigned) * 100 : 0;
                    ?>
                    <h3 class="text-3xl font-bold text-green-50 mb-2"><?php echo e(round($avgCompletionRate, 1)); ?>%</h3>
                    <p class="text-gray-400 text-sm uppercase tracking-wider font-medium">Average Completion Rate</p>
                </div>
            </div>
        </div>

        <!-- Staff Performance Table -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden mb-8">
            <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-green-100">Individual Staff Performance</h3>
            </div>
            <div class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full" id="staffTable">
                        <thead class="bg-gray-750">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Staff Member</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Total Assigned</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Completed</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Pending</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Completion Rate</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Avg Completion Time</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Performance Rating</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            <?php $__empty_0 = true; $__currentLoopData = $staffMetrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                            <tr class="hover:bg-gray-750 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center mr-4">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                        <div>
                                            <h6 class="text-green-100 font-medium"><?php echo e($staff->name); ?></h6>
                                            <p class="text-gray-400 text-sm"><?php echo e($staff->email); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-600/20 text-blue-400">
                                        <?php echo e(number_format($staff->total_assigned)); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-600/20 text-green-400">
                                        <?php echo e(number_format($staff->completed_tasks)); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-600/20 text-yellow-400">
                                        <?php echo e(number_format($staff->pending_tasks)); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                            <?php echo e($staff->completion_rate >= 80 ? 'bg-green-600/20 text-green-400' : 
                                               ($staff->completion_rate >= 60 ? 'bg-yellow-600/20 text-yellow-400' : 'bg-red-600/20 text-red-400')); ?>">
                                            <?php echo e($staff->completion_rate); ?>%
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-300">
                                    <?php echo e($staff->avg_completion_time); ?>h
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php
                                        $rating = 'Poor';
                                        $ratingClass = 'red';
                                        $stars = 1;
                                        
                                        if ($staff->completion_rate >= 90 && $staff->avg_completion_time <= 24) {
                                            $rating = 'Excellent';
                                            $ratingClass = 'green';
                                            $stars = 5;
                                        } elseif ($staff->completion_rate >= 80 && $staff->avg_completion_time <= 48) {
                                            $rating = 'Very Good';
                                            $ratingClass = 'green';
                                            $stars = 4;
                                        } elseif ($staff->completion_rate >= 70) {
                                            $rating = 'Good';
                                            $ratingClass = 'blue';
                                            $stars = 3;
                                        } elseif ($staff->completion_rate >= 50) {
                                            $rating = 'Fair';
                                            $ratingClass = 'yellow';
                                            $stars = 2;
                                        }
                                    ?>
                                    <div class="text-center">
                                        <div class="mb-1">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?php echo e($i <= $stars ? 'text-yellow-400' : 'text-gray-600'); ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <small class="text-<?php echo e($ratingClass); ?>-400"><?php echo e($rating); ?></small>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <i class="fas fa-users text-4xl text-gray-600 mb-4"></i>
                                    <h5 class="text-gray-400 font-medium mb-2">No staff performance data found</h5>
                                    <p class="text-gray-500">No staff assignments were made during the selected period.</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Workload Distribution and Performance Summary -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Workload Distribution Chart -->
            <div class="lg:col-span-2 bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-green-100">Workload Distribution</h3>
                </div>
                <div class="p-6">
                    <div class="relative" style="height: 300px;">
                        <canvas id="workloadChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Performers Award -->
            <div class="bg-gradient-to-br from-gray-800 via-gray-800 to-gray-900 rounded-2xl border border-yellow-600/30 overflow-hidden shadow-2xl">
                <!-- Award Header -->
                <div class="bg-gradient-to-r from-yellow-600 via-amber-500 to-yellow-600 px-6 py-5 text-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-yellow-400/10 animate-pulse"></div>
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-3 ring-4 ring-white/30">
                            <i class="fas fa-trophy text-3xl text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-1">Top Performers</h3>
                        <p class="text-yellow-100 text-xs">Outstanding Staff Members</p>
                    </div>
                </div>

                <div class="p-6 space-y-3">
                    <?php if($staffMetrics->count() > 0): ?>
                        <?php
                            $topStaff = $staffMetrics->sortByDesc('completion_rate')->take(3)->values();
                        ?>
                        
                        <?php $__currentLoopData = $topStaff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center group hover:bg-gray-700/30 rounded-lg p-2 transition-all duration-200 <?php echo e($index === 0 ? 'bg-yellow-500/10' : ''); ?>">
                                <div class="relative">
                                    <?php if($index === 0): ?>
                                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center mr-3 ring-4 ring-yellow-400/30 shadow-lg">
                                            <i class="fas fa-crown text-white text-lg"></i>
                                        </div>
                                    <?php elseif($index === 1): ?>
                                        <div class="w-10 h-10 bg-gradient-to-br from-gray-300 to-gray-500 rounded-full flex items-center justify-center mr-3 ring-4 ring-gray-400/30">
                                            <i class="fas fa-medal text-white"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-10 h-10 bg-gradient-to-br from-amber-600 to-amber-800 rounded-full flex items-center justify-center mr-3 ring-4 ring-amber-600/30">
                                            <i class="fas fa-award text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="absolute -top-1 -right-1 w-5 h-5 bg-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                        <?php echo e($index + 1); ?>

                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-gray-200 font-medium text-sm truncate"><?php echo e($staff->name); ?></p>
                                    <div class="flex items-center justify-between text-xs mt-0.5">
                                        <span class="text-gray-400 flex items-center">
                                            <i class="fas fa-tasks mr-1"></i>
                                            <?php echo e(number_format($staff->completed_tasks)); ?> completed
                                        </span>
                                        <span class="text-green-400 font-semibold"><?php echo e($staff->completion_rate); ?>%</span>
                                    </div>
                                </div>
                                <?php if($index === 0): ?>
                                    <i class="fas fa-sparkles text-yellow-400 text-lg ml-2"></i>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <!-- Additional Staff Stats -->
                        <?php if($staffMetrics->count() > 3): ?>
                            <div class="pt-3 mt-3 border-t border-gray-700">
                                <div class="text-center text-xs text-gray-400">
                                    <i class="fas fa-users mr-1"></i>
                                    <?php echo e($staffMetrics->count() - 3); ?> more staff member<?php echo e($staffMetrics->count() - 3 != 1 ? 's' : ''); ?>

                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-users text-4xl mb-3"></i>
                            <p class="text-sm">No staff performance data available for this period.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Award Footer -->
                <div class="bg-gradient-to-r from-gray-900 to-gray-800 px-6 py-3 border-t border-gray-700">
                    <div class="flex items-center justify-center text-xs text-gray-400">
                        <i class="fas fa-star text-yellow-500 mr-2"></i>
                        <span>Based on completion rate</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Insights -->
        <?php if($staffMetrics->count() > 0): ?>
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
            <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-green-100">Team Performance Insights</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Team Efficiency -->
                    <div class="bg-gray-750 rounded-lg p-4 border border-gray-600">
                        <div class="flex items-start">
                            <div class="mr-4">
                                <?php if($avgCompletionRate > 80): ?>
                                    <i class="fas fa-check-circle text-green-400 text-2xl"></i>
                                <?php elseif($avgCompletionRate > 60): ?>
                                    <i class="fas fa-exclamation-triangle text-yellow-400 text-2xl"></i>
                                <?php else: ?>
                                    <i class="fas fa-times-circle text-red-400 text-2xl"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h6 class="text-green-100 font-semibold mb-2">Team Efficiency</h6>
                                <?php if($avgCompletionRate > 80): ?>
                                    <p class="text-green-400 text-sm">Excellent team performance! The team is highly efficient and productive.</p>
                                <?php elseif($avgCompletionRate > 60): ?>
                                    <p class="text-yellow-400 text-sm">Good team performance with room for improvement. Focus on training and process optimization.</p>
                                <?php else: ?>
                                    <p class="text-red-400 text-sm">Team performance needs attention. Review workload distribution and provide additional support.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Workload Balance -->
                    <div class="bg-gray-750 rounded-lg p-4 border border-gray-600">
                        <div class="flex items-start">
                            <div class="mr-4">
                                <?php
                                    $maxTasks = $staffMetrics->max('total_assigned');
                                    $minTasks = $staffMetrics->min('total_assigned');
                                    $taskVariance = $maxTasks - $minTasks;
                                    $avgTasks = $staffMetrics->avg('total_assigned');
                                    $isBalanced = $taskVariance <= ($avgTasks * 0.5);
                                ?>
                                <?php if($isBalanced): ?>
                                    <i class="fas fa-balance-scale text-green-400 text-2xl"></i>
                                <?php else: ?>
                                    <i class="fas fa-balance-scale-right text-yellow-400 text-2xl"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h6 class="text-green-100 font-semibold mb-2">Workload Balance</h6>
                                <?php if($isBalanced): ?>
                                    <p class="text-green-400 text-sm">Workload is well-balanced across the team. Good distribution of tasks.</p>
                                <?php else: ?>
                                    <p class="text-yellow-400 text-sm">Workload distribution could be improved. Consider redistributing tasks more evenly.</p>
                                <?php endif; ?>
                                <small class="text-gray-400">Range: <?php echo e($minTasks); ?> - <?php echo e($maxTasks); ?> tasks per staff member</small>
                            </div>
                        </div>
                    </div>

                    <!-- Training Needs -->
                    <div class="bg-gray-750 rounded-lg p-4 border border-gray-600">
                        <div class="flex items-start">
                            <div class="mr-4">
                                <?php $needsTraining = $staffMetrics->where('completion_rate', '<', 70)->count(); ?>
                                <?php if($needsTraining == 0): ?>
                                    <i class="fas fa-graduation-cap text-green-400 text-2xl"></i>
                                <?php elseif($needsTraining <= 2): ?>
                                    <i class="fas fa-graduation-cap text-yellow-400 text-2xl"></i>
                                <?php else: ?>
                                    <i class="fas fa-graduation-cap text-red-400 text-2xl"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h6 class="text-green-100 font-semibold mb-2">Training Recommendations</h6>
                                <?php if($needsTraining == 0): ?>
                                    <p class="text-green-400 text-sm">All staff members are performing well. Continue with current training programs.</p>
                                <?php elseif($needsTraining <= 2): ?>
                                    <p class="text-yellow-400 text-sm"><?php echo e($needsTraining); ?> staff member(s) may benefit from additional training and support.</p>
                                <?php else: ?>
                                    <p class="text-red-400 text-sm"><?php echo e($needsTraining); ?> staff members need focused training and mentoring programs.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Recognition -->
                    <div class="bg-gray-750 rounded-lg p-4 border border-gray-600">
                        <div class="flex items-start">
                            <div class="mr-4">
                                <i class="fas fa-award text-indigo-400 text-2xl"></i>
                            </div>
                            <div>
                                <h6 class="text-green-100 font-semibold mb-2">Recognition Opportunities</h6>
                                <?php $highPerformers = $staffMetrics->where('completion_rate', '>=', 85)->count(); ?>
                                <?php if($highPerformers > 0): ?>
                                    <p class="text-indigo-400 text-sm"><?php echo e($highPerformers); ?> staff member(s) deserve recognition for outstanding performance.</p>
                                <?php else: ?>
                                    <p class="text-gray-400 text-sm">Focus on improving overall team performance before implementing recognition programs.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart.js defaults for dark theme
Chart.defaults.color = '#9ca3af';
Chart.defaults.borderColor = '#374151';
Chart.defaults.backgroundColor = 'rgba(55, 65, 81, 0.1)';

// Workload Distribution Chart
const workloadCtx = document.getElementById('workloadChart').getContext('2d');
new Chart(workloadCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($staffMetrics->pluck('name')); ?>,
        datasets: [{
            label: 'Assigned Tasks',
            data: <?php echo json_encode($staffMetrics->pluck('total_assigned')); ?>,
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderColor: 'rgba(59, 130, 246, 1)',
            borderWidth: 1,
            borderRadius: 4
        }, {
            label: 'Completed Tasks',
            data: <?php echo json_encode($staffMetrics->pluck('completed_tasks')); ?>,
            backgroundColor: 'rgba(34, 197, 94, 0.8)',
            borderColor: 'rgba(34, 197, 94, 1)',
            borderWidth: 1,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
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
                    text: 'Number of Tasks',
                    color: '#9ca3af'
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
                },
                title: {
                    display: true,
                    text: 'Staff Members',
                    color: '#9ca3af'
                }
            }
        },
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
        }
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\manager\reports\staff-performance.blade.php ENDPATH**/ ?>