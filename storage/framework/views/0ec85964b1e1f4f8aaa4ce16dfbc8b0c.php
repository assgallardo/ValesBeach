<?php $__env->startSection('title', 'Service Usage Report'); ?>

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
                                    <span class="text-sm font-medium text-gray-300">Service Usage</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-3xl font-bold text-green-50">Service Usage Report</h1>
                    <p class="text-gray-400 mt-2">Detailed service utilization analysis</p>
                </div>
                <div class="flex space-x-3">
                    <a href="<?php echo e(route('manager.reports.index', request()->query())); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-gray-700 text-gray-300 rounded-lg font-medium hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                    <a href="<?php echo e(route('manager.reports.export', ['type' => 'service-usage'] + request()->query())); ?>" 
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

        <!-- Category Breakdown -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden mb-8">
            <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-green-100">Service Categories Breakdown</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    <?php $__currentLoopData = $categoryBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-gray-750 rounded-lg p-6 text-center border border-gray-600">
                        <div class="mb-4">
                            <?php switch($category->category):
                                case ('spa'): ?>
                                    <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto">
                                        <i class="fas fa-spa text-2xl text-white"></i>
                                    </div>
                                    <?php break; ?>
                                <?php case ('dining'): ?>
                                    <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto">
                                        <i class="fas fa-utensils text-2xl text-white"></i>
                                    </div>
                                    <?php break; ?>
                                <?php case ('activities'): ?>
                                    <div class="w-16 h-16 bg-yellow-600 rounded-full flex items-center justify-center mx-auto">
                                        <i class="fas fa-volleyball-ball text-2xl text-white"></i>
                                    </div>
                                    <?php break; ?>
                                <?php case ('transportation'): ?>
                                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto">
                                        <i class="fas fa-car text-2xl text-white"></i>
                                    </div>
                                    <?php break; ?>
                                <?php case ('room_service'): ?>
                                    <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center mx-auto">
                                        <i class="fas fa-concierge-bell text-2xl text-white"></i>
                                    </div>
                                    <?php break; ?>
                                <?php default: ?>
                                    <div class="w-16 h-16 bg-gray-600 rounded-full flex items-center justify-center mx-auto">
                                        <i class="fas fa-cog text-2xl text-white"></i>
                                    </div>
                            <?php endswitch; ?>
                        </div>
                        <h4 class="text-3xl font-bold text-green-50 mb-2"><?php echo e(number_format($category->total_requests)); ?></h4>
                        <p class="text-gray-400 text-sm uppercase tracking-wider font-medium"><?php echo e(ucfirst(str_replace('_', ' ', $category->category))); ?></p>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        <!-- Detailed Service Usage Table -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden mb-8">
            <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-green-100">Service Performance Details</h3>
            </div>
            <div class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full" id="servicesTable">
                        <thead class="bg-gray-750">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Service Name</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Total Requests</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Completed</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Pending</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Cancelled</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Completion Rate</th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Performance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            <?php $__empty_1 = true; $__currentLoopData = $serviceUsageDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-750 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="mr-4">
                                            <?php switch($service->category):
                                                case ('spa'): ?>
                                                    <i class="fas fa-spa text-purple-400"></i>
                                                    <?php break; ?>
                                                <?php case ('dining'): ?>
                                                    <i class="fas fa-utensils text-green-400"></i>
                                                    <?php break; ?>
                                                <?php case ('activities'): ?>
                                                    <i class="fas fa-volleyball-ball text-yellow-400"></i>
                                                    <?php break; ?>
                                                <?php case ('transportation'): ?>
                                                    <i class="fas fa-car text-blue-400"></i>
                                                    <?php break; ?>
                                                <?php case ('room_service'): ?>
                                                    <i class="fas fa-concierge-bell text-red-400"></i>
                                                    <?php break; ?>
                                                <?php default: ?>
                                                    <i class="fas fa-cog text-gray-400"></i>
                                            <?php endswitch; ?>
                                        </div>
                                        <div>
                                            <h6 class="text-green-100 font-semibold"><?php echo e($service->name); ?></h6>
                                            <?php if($service->description): ?>
                                                <small class="text-gray-400"><?php echo e(Str::limit($service->description, 50)); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-700 text-gray-300">
                                        <?php echo e(ucfirst(str_replace('_', ' ', $service->category))); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-600/20 text-blue-400">
                                        <?php echo e(number_format($service->total_requests)); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-600/20 text-green-400">
                                        <?php echo e(number_format($service->completed_requests)); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-600/20 text-yellow-400">
                                        <?php echo e(number_format($service->pending_requests)); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-600/20 text-red-400">
                                        <?php echo e(number_format($service->cancelled_requests)); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                        <?php echo e($service->completion_rate >= 80 ? 'bg-green-600/20 text-green-400' : 
                                           ($service->completion_rate >= 60 ? 'bg-yellow-600/20 text-yellow-400' : 'bg-red-600/20 text-red-400')); ?>">
                                        <?php echo e($service->completion_rate); ?>%
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="w-24 mx-auto">
                                        <div class="flex items-center">
                                            <div class="flex-1 bg-gray-700 rounded-full h-4 mr-2">
                                                <div class="h-4 rounded-full flex items-center justify-center text-xs font-medium text-white
                                                    <?php echo e($service->completion_rate >= 80 ? 'bg-green-500' : 
                                                       ($service->completion_rate >= 60 ? 'bg-yellow-500' : 'bg-red-500')); ?>" 
                                                     style="width: <?php echo e($service->completion_rate); ?>%;">
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-400"><?php echo e($service->completion_rate); ?>%</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <i class="fas fa-chart-bar text-4xl text-gray-600 mb-4"></i>
                                    <h5 class="text-gray-400 font-medium mb-2">No service usage data found</h5>
                                    <p class="text-gray-500">No service requests were made during the selected period.</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Service Performance Insights -->
        <?php if($serviceUsageDetails->count() > 0): ?>
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
            <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-green-100">Performance Insights</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Top Performer -->
                    <div class="bg-green-900/30 border border-green-600/30 rounded-lg p-6 text-center">
                        <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-trophy text-2xl text-white"></i>
                        </div>
                        <h6 class="text-green-100 font-semibold mb-3">Top Performer</h6>
                        <?php $topService = $serviceUsageDetails->sortByDesc('completion_rate')->first(); ?>
                        <p class="text-green-50 font-medium mb-1"><?php echo e($topService->name); ?></p>
                        <small class="text-green-400"><?php echo e($topService->completion_rate); ?>% completion rate</small>
                    </div>

                    <!-- Most Popular -->
                    <div class="bg-blue-900/30 border border-blue-600/30 rounded-lg p-6 text-center">
                        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-star text-2xl text-white"></i>
                        </div>
                        <h6 class="text-blue-100 font-semibold mb-3">Most Popular</h6>
                        <?php $popularService = $serviceUsageDetails->sortByDesc('total_requests')->first(); ?>
                        <p class="text-blue-50 font-medium mb-1"><?php echo e($popularService->name); ?></p>
                        <small class="text-blue-400"><?php echo e($popularService->total_requests); ?> requests</small>
                    </div>

                    <!-- Needs Attention -->
                    <div class="bg-yellow-900/30 border border-yellow-600/30 rounded-lg p-6 text-center">
                        <div class="w-16 h-16 bg-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-exclamation-triangle text-2xl text-white"></i>
                        </div>
                        <h6 class="text-yellow-100 font-semibold mb-3">Needs Attention</h6>
                        <?php $needsAttention = $serviceUsageDetails->sortBy('completion_rate')->first(); ?>
                        <p class="text-yellow-50 font-medium mb-1"><?php echo e($needsAttention->name); ?></p>
                        <small class="text-yellow-400"><?php echo e($needsAttention->completion_rate); ?>% completion rate</small>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
// Initialize DataTable for better sorting and searching
$(document).ready(function() {
    $('#servicesTable').DataTable({
        "pageLength": 25,
        "order": [[ 2, "desc" ]], // Sort by total requests descending
        "columnDefs": [
            { "orderable": false, "targets": [7] } // Disable sorting on progress bar column
        ],
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        "language": {
            "search": "Search services:",
            "lengthMenu": "Show _MENU_ services per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ services",
            "paginate": {
                "previous": "Previous",
                "next": "Next"
            }
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<style>
.dataTables_wrapper {
    color: #d1d5db;
}
.dataTables_wrapper .dataTables_length select {
    background-color: #374151;
    border: 1px solid #4b5563;
    color: #d1d5db;
    border-radius: 0.375rem;
    padding: 0.375rem;
}
.dataTables_wrapper .dataTables_filter input {
    background-color: #374151;
    border: 1px solid #4b5563;
    color: #d1d5db;
    border-radius: 0.375rem;
    padding: 0.375rem;
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    color: #d1d5db !important;
    background: #374151;
    border: 1px solid #4b5563;
    margin: 0 2px;
    border-radius: 0.375rem;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #4b5563 !important;
    color: #10b981 !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #10b981 !important;
    color: white !important;
    border-color: #10b981 !important;
}
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\valesbeachresort\ValesBeach\resources\views/manager/reports/service-usage.blade.php ENDPATH**/ ?>