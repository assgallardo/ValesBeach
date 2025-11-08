<?php $__env->startSection('title', 'Service Usage Report'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-900 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <nav class="flex mb-3" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1">
                            <li class="inline-flex items-center">
                                <a href="<?php echo e(route('manager.reports.index', request()->query())); ?>" 
                                   class="inline-flex items-center text-sm text-gray-400 hover:text-green-400 transition-colors">
                                    <i class="fas fa-chart-line mr-2"></i>
                                    Reports Dashboard
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <i class="fas fa-chevron-right text-gray-600 mx-2 text-xs"></i>
                                    <span class="text-sm text-green-400">Service Usage</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-3xl font-bold text-white mb-2">Service Usage Report</h1>
                    <p class="text-gray-400">Track and analyze service request patterns and performance</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="<?php echo e(route('manager.reports.index', request()->query())); ?>" 
                       class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-700 text-white rounded-lg font-medium hover:bg-gray-600 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back
                    </a>
                    <a href="<?php echo e(route('manager.reports.export', ['type' => 'service-usage'] + request()->query())); ?>" 
                       class="inline-flex items-center justify-center px-5 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg font-medium hover:from-green-700 hover:to-green-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-download mr-2"></i>
                        Export CSV
                    </a>
                </div>
            </div>
        </div>

        <!-- Date Range Display -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-900/40 to-blue-800/40 border border-blue-500/30 rounded-xl p-4 backdrop-blur-sm">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-calendar-alt text-blue-400"></i>
                    </div>
                    <div>
                        <p class="text-blue-100 text-sm">Report Period</p>
                        <p class="text-white font-semibold">
                            <?php echo e($startDate->format('M d, Y')); ?> - <?php echo e($endDate->format('M d, Y')); ?>

                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Breakdown -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="w-1 h-8 bg-gradient-to-b from-green-400 to-green-600 rounded-full mr-3"></div>
                    <h2 class="text-2xl font-bold text-white">Service Categories</h2>
                </div>
                <p class="text-gray-400 text-sm">Click a category to filter</p>
            </div>
            
            <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                <!-- All Categories Card -->
                <div class="category-filter active cursor-pointer group bg-gradient-to-br from-gray-800 to-gray-900 rounded-lg p-4 border-2 border-green-500 hover:border-green-400 transition-all duration-300 hover:shadow-xl hover:scale-105 shadow-lg shadow-green-500/30" 
                     data-category="all">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-md group-hover:shadow-green-500/50 transition-shadow">
                            <i class="fas fa-th-large text-lg text-white"></i>
                        </div>
                        <?php
                            $totalRequests = $categoryBreakdown->sum('total_requests');
                        ?>
                        <h4 class="text-xl font-bold text-white mb-0.5"><?php echo e(number_format($totalRequests)); ?></h4>
                        <p class="text-gray-400 text-[10px] uppercase tracking-wider font-semibold">All Categories</p>
                    </div>
                </div>

                <?php $__currentLoopData = $categoryBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="category-filter cursor-pointer group bg-gradient-to-br from-gray-800 to-gray-900 rounded-lg p-4 border-2 border-gray-700 hover:border-gray-600 transition-all duration-300 hover:shadow-xl hover:scale-105" 
                     data-category="<?php echo e($category->category); ?>">
                    <div class="text-center">
                        <?php switch($category->category):
                            case ('spa'): ?>
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-md group-hover:shadow-purple-500/50 transition-shadow">
                                    <i class="fas fa-spa text-lg text-white"></i>
                                </div>
                                <?php break; ?>
                            <?php case ('dining'): ?>
                                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-md group-hover:shadow-green-500/50 transition-shadow">
                                    <i class="fas fa-utensils text-lg text-white"></i>
                                </div>
                                <?php break; ?>
                            <?php case ('activities'): ?>
                                <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-md group-hover:shadow-yellow-500/50 transition-shadow">
                                    <i class="fas fa-volleyball-ball text-lg text-white"></i>
                                </div>
                                <?php break; ?>
                            <?php case ('transportation'): ?>
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-md group-hover:shadow-blue-500/50 transition-shadow">
                                    <i class="fas fa-car text-lg text-white"></i>
                                </div>
                                <?php break; ?>
                            <?php case ('room_service'): ?>
                                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-md group-hover:shadow-red-500/50 transition-shadow">
                                    <i class="fas fa-concierge-bell text-lg text-white"></i>
                                </div>
                                <?php break; ?>
                            <?php default: ?>
                                <div class="w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-md group-hover:shadow-gray-500/50 transition-shadow">
                                    <i class="fas fa-cog text-lg text-white"></i>
                                </div>
                        <?php endswitch; ?>
                        <h4 class="text-xl font-bold text-white mb-0.5"><?php echo e(number_format($category->total_requests)); ?></h4>
                        <p class="text-gray-400 text-[10px] uppercase tracking-wider font-semibold"><?php echo e(ucfirst(str_replace('_', ' ', $category->category))); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <!-- Performance Insights Cards -->
        <?php if($serviceUsageDetails->count() > 0): ?>
        <div class="mb-6">
            <div class="flex items-center mb-4">
                <div class="w-1 h-6 bg-gradient-to-b from-yellow-400 to-yellow-600 rounded-full mr-2"></div>
                <h2 class="text-xl font-bold text-white">Performance Highlights</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Top Performer -->
                <?php $topService = $serviceUsageDetails->sortByDesc('completion_rate')->first(); ?>
                <div class="relative bg-gradient-to-br from-green-900/50 to-green-800/30 rounded-lg p-4 border border-green-600/30 overflow-hidden group hover:border-green-500/50 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-green-500/10 rounded-full -mr-12 -mt-12 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-trophy text-lg text-green-400"></i>
                            </div>
                            <span class="px-2.5 py-0.5 bg-green-500/20 text-green-300 text-[10px] font-bold rounded-full">BEST</span>
                        </div>
                        <h6 class="text-green-300 font-semibold text-xs uppercase tracking-wider mb-1.5">Top Service</h6>
                        <p class="text-white font-bold text-base mb-1"><?php echo e($topService->name); ?></p>
                        <div class="flex items-center">
                            <div class="flex-1">
                                <div class="bg-green-500/20 rounded-full h-1.5">
                                    <div class="bg-green-400 h-1.5 rounded-full" style="width: <?php echo e($topService->completion_rate); ?>%"></div>
                                </div>
                            </div>
                            <span class="ml-2 text-green-400 font-bold text-sm"><?php echo e($topService->completion_rate); ?>%</span>
                        </div>
                    </div>
                </div>

                <!-- Most Popular -->
                <?php $popularService = $serviceUsageDetails->sortByDesc('total_requests')->first(); ?>
                <div class="relative bg-gradient-to-br from-blue-900/50 to-blue-800/30 rounded-lg p-4 border border-blue-600/30 overflow-hidden group hover:border-blue-500/50 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/10 rounded-full -mr-12 -mt-12 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-star text-lg text-blue-400"></i>
                            </div>
                            <span class="px-2.5 py-0.5 bg-blue-500/20 text-blue-300 text-[10px] font-bold rounded-full">POPULAR</span>
                        </div>
                        <h6 class="text-blue-300 font-semibold text-xs uppercase tracking-wider mb-1.5">Most Requested</h6>
                        <p class="text-white font-bold text-base mb-1"><?php echo e($popularService->name); ?></p>
                        <div class="flex items-center">
                            <i class="fas fa-clipboard-check text-blue-400 mr-1.5 text-sm"></i>
                            <span class="text-blue-300 font-semibold text-sm"><?php echo e(number_format($popularService->total_requests)); ?> requests</span>
                        </div>
                    </div>
                </div>

                <!-- Needs Attention -->
                <?php $needsAttention = $serviceUsageDetails->sortBy('completion_rate')->first(); ?>
                <div class="relative bg-gradient-to-br from-yellow-900/50 to-yellow-800/30 rounded-lg p-4 border border-yellow-600/30 overflow-hidden group hover:border-yellow-500/50 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-yellow-500/10 rounded-full -mr-12 -mt-12 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-lg text-yellow-400"></i>
                            </div>
                            <span class="px-2.5 py-0.5 bg-yellow-500/20 text-yellow-300 text-[10px] font-bold rounded-full">ALERT</span>
                        </div>
                        <h6 class="text-yellow-300 font-semibold text-xs uppercase tracking-wider mb-1.5">Needs Attention</h6>
                        <p class="text-white font-bold text-base mb-1"><?php echo e($needsAttention->name); ?></p>
                        <div class="flex items-center">
                            <div class="flex-1">
                                <div class="bg-yellow-500/20 rounded-full h-1.5">
                                    <div class="bg-yellow-400 h-1.5 rounded-full" style="width: <?php echo e($needsAttention->completion_rate); ?>%"></div>
                                </div>
                            </div>
                            <span class="ml-2 text-yellow-400 font-bold text-sm"><?php echo e($needsAttention->completion_rate); ?>%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Service Performance Table -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden shadow-2xl">
            <div class="bg-gradient-to-r from-gray-750 to-gray-800 px-6 py-5 border-b border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-list-alt text-gray-300"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Service Performance Details</h3>
                            <p class="text-gray-400 text-sm">Complete breakdown of all service requests</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full" id="servicesTable">
                    <thead class="bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">Service</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-300 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-300 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-300 uppercase tracking-wider">Completed</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-300 uppercase tracking-wider">Pending</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-300 uppercase tracking-wider">Cancelled</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-300 uppercase tracking-wider">Performance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php $__empty_1 = true; $__currentLoopData = $serviceUsageDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-750/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3
                                        <?php echo e($service->category == 'spa' ? 'bg-purple-500/20' : ''); ?>

                                        <?php echo e($service->category == 'dining' ? 'bg-green-500/20' : ''); ?>

                                        <?php echo e($service->category == 'activities' ? 'bg-yellow-500/20' : ''); ?>

                                        <?php echo e($service->category == 'transportation' ? 'bg-blue-500/20' : ''); ?>

                                        <?php echo e($service->category == 'room_service' ? 'bg-red-500/20' : ''); ?>">
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
                                        <h6 class="text-white font-semibold group-hover:text-green-400 transition-colors"><?php echo e($service->name); ?></h6>
                                        <?php if($service->description): ?>
                                            <small class="text-gray-500 text-xs"><?php echo e(Str::limit($service->description, 40)); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-700/50 text-gray-300">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $service->category))); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                    <?php echo e(number_format($service->total_requests)); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-green-500/20 text-green-300 border border-green-500/30">
                                    <?php echo e(number_format($service->completed_requests)); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-yellow-500/20 text-yellow-300 border border-yellow-500/30">
                                    <?php echo e(number_format($service->pending_requests)); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold bg-red-500/20 text-red-300 border border-red-500/30">
                                    <?php echo e(number_format($service->cancelled_requests)); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-3">
                                    <div class="w-24">
                                        <div class="bg-gray-700 rounded-full h-2 overflow-hidden">
                                            <div class="h-2 rounded-full transition-all duration-300
                                                <?php echo e($service->completion_rate >= 80 ? 'bg-gradient-to-r from-green-400 to-green-500' : 
                                                   ($service->completion_rate >= 60 ? 'bg-gradient-to-r from-yellow-400 to-yellow-500' : 'bg-gradient-to-r from-red-400 to-red-500')); ?>" 
                                                 style="width: <?php echo e($service->completion_rate); ?>%;">
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold
                                        <?php echo e($service->completion_rate >= 80 ? 'text-green-400' : 
                                           ($service->completion_rate >= 60 ? 'text-yellow-400' : 'text-red-400')); ?>">
                                        <?php echo e($service->completion_rate); ?>%
                                    </span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-inbox text-4xl text-gray-500"></i>
                                    </div>
                                    <h5 class="text-gray-300 font-semibold text-lg mb-2">No Service Data Available</h5>
                                    <p class="text-gray-500">No service requests were made during the selected period.</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    var table = $('#servicesTable').DataTable({
        "pageLength": 10,
        "order": [[ 2, "desc" ]], // Sort by total requests descending
        "columnDefs": [
            { "orderable": false, "targets": [6] } // Disable sorting on performance bar column
        ],
        "language": {
            "search": "",
            "searchPlaceholder": "Search services...",
            "lengthMenu": "_MENU_",
            "info": "Showing _START_ to _END_ of _TOTAL_ services",
            "infoEmpty": "No services found",
            "infoFiltered": "(filtered from _MAX_ total services)",
            "paginate": {
                "previous": "<i class='fas fa-chevron-left'></i>",
                "next": "<i class='fas fa-chevron-right'></i>"
            },
            "emptyTable": "No service data available"
        },
        "dom": '<"px-6 pt-4 pb-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"lf>rt<"px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"ip>',
        "drawCallback": function() {
            // Style pagination buttons after draw
            $('.dataTables_paginate .paginate_button').addClass('pagination-btn');
            $('.dataTables_paginate .paginate_button.current').addClass('pagination-btn-active');
        }
    });

    // Category filter functionality
    $('.category-filter').on('click', function() {
        var category = $(this).data('category');
        
        // Remove active class from all filters
        $('.category-filter').removeClass('active')
            .removeClass('border-green-500')
            .removeClass('shadow-green-500/30')
            .removeClass('border-purple-500')
            .removeClass('shadow-purple-500/30')
            .removeClass('border-yellow-500')
            .removeClass('shadow-yellow-500/30')
            .removeClass('border-blue-500')
            .removeClass('shadow-blue-500/30')
            .removeClass('border-red-500')
            .removeClass('shadow-red-500/30')
            .addClass('border-gray-700');
        
        // Add active class to clicked filter
        $(this).addClass('active');
        
        // Add category-specific active styling
        if (category === 'all') {
            $(this).removeClass('border-gray-700').addClass('border-green-500 shadow-lg shadow-green-500/30');
            table.column(1).search('').draw(); // Clear search on category column
        } else {
            // Map category to appropriate color
            var colorClass = 'border-green-500';
            if (category === 'spa') colorClass = 'border-purple-500 shadow-lg shadow-purple-500/30';
            else if (category === 'activities') colorClass = 'border-yellow-500 shadow-lg shadow-yellow-500/30';
            else if (category === 'transportation') colorClass = 'border-blue-500 shadow-lg shadow-blue-500/30';
            else if (category === 'room_service') colorClass = 'border-red-500 shadow-lg shadow-red-500/30';
            else if (category === 'dining') colorClass = 'border-green-500 shadow-lg shadow-green-500/30';
            
            $(this).removeClass('border-gray-700').addClass(colorClass);
            
            // Filter table by category (search in the category column - index 1)
            var searchTerm = category.replace('_', ' ');
            table.column(1).search(searchTerm, true, false).draw();
        }

        // Smooth scroll to table
        $('html, body').animate({
            scrollTop: $("#servicesTable").offset().top - 100
        }, 500);
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<style>
/* DataTables Custom Styling */
.dataTables_wrapper {
    color: #d1d5db;
}

/* Search Box */
.dataTables_filter {
    float: none !important;
}

.dataTables_filter label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #d1d5db;
    font-weight: 500;
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    padding: 0.5rem 1rem;
    border-radius: 0.75rem;
    border: 1px solid #374151;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
}

.dataTables_filter label::before {
    content: '\f002';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    color: #10b981;
    font-size: 1.125rem;
}

.dataTables_filter input {
    margin-left: 0 !important;
    background-color: #374151 !important;
    border: 2px solid #4b5563 !important;
    color: #fff !important;
    border-radius: 0.5rem;
    padding: 0.75rem 1.25rem !important;
    font-size: 0.875rem;
    width: 320px;
    transition: all 0.3s;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
    font-weight: 500;
}

.dataTables_filter input:focus {
    outline: none !important;
    border-color: #10b981 !important;
    background-color: #1f2937 !important;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.25), inset 0 2px 4px rgba(0, 0, 0, 0.2) !important;
    transform: translateY(-1px);
}

.dataTables_filter input::placeholder {
    color: #9ca3af;
    font-weight: 400;
}

/* Length Menu */
.dataTables_length {
    float: none !important;
}

.dataTables_length label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.875rem;
    color: #d1d5db;
    font-weight: 500;
}

.dataTables_length label::before {
    content: '\f0cb';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    color: #10b981;
    font-size: 1rem;
}

.dataTables_length select {
    background-color: #1f2937;
    border: 1px solid #374151;
    color: #fff;
    border-radius: 0.5rem;
    padding: 0.625rem 2.5rem 0.625rem 1rem;
    font-size: 0.875rem;
    transition: all 0.2s;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%2310b981' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}

.dataTables_length select:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2), 0 1px 3px rgba(0, 0, 0, 0.3);
}

/* Info Text */
.dataTables_info {
    float: none !important;
    color: #9ca3af;
    font-size: 0.875rem;
    font-weight: 500;
}

/* Pagination */
.dataTables_paginate {
    float: none !important;
}

.dataTables_paginate .paginate_button {
    padding: 0.5rem 1rem !important;
    margin: 0 0.25rem !important;
    border-radius: 0.5rem !important;
    border: 2px solid #374151 !important;
    background: #1f2937 !important;
    color: #d1d5db !important;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s !important;
    cursor: pointer !important;
}

.dataTables_paginate .paginate_button:hover {
    background: #374151 !important;
    border-color: #10b981 !important;
    color: #10b981 !important;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
}

.dataTables_paginate .paginate_button.current {
    background: linear-gradient(to right, #10b981, #059669) !important;
    border-color: #10b981 !important;
    color: white !important;
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3) !important;
}

.dataTables_paginate .paginate_button.disabled {
    opacity: 0.4 !important;
    cursor: not-allowed !important;
}

.dataTables_paginate .paginate_button.disabled:hover {
    background: #1f2937 !important;
    border-color: #374151 !important;
    color: #d1d5db !important;
    box-shadow: none !important;
}

/* Remove default DataTables styling */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    margin: 0;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .dataTables_filter input {
        width: 100%;
    }
    
    .dataTables_length select {
        margin: 0.5rem 0;
    }
    
    .dataTables_paginate .paginate_button {
        padding: 0.5rem 0.75rem !important;
        font-size: 0.75rem;
    }
}

/* Hide default sorting icons */
table.dataTable thead th {
    position: relative;
}

table.dataTable thead .sorting:before,
table.dataTable thead .sorting_asc:before,
table.dataTable thead .sorting_desc:before,
table.dataTable thead .sorting:after,
table.dataTable thead .sorting_asc:after,
table.dataTable thead .sorting_desc:after {
    display: none;
}

/* Category filter active state */
.category-filter {
    position: relative;
}

.category-filter.active::after {
    content: '\f00c';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    position: absolute;
    top: 0.375rem;
    right: 0.375rem;
    width: 20px;
    height: 20px;
    background: linear-gradient(135deg, #10b981, #059669);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.625rem;
    box-shadow: 0 2px 6px rgba(16, 185, 129, 0.4);
}
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views/manager/reports/service-usage.blade.php ENDPATH**/ ?>