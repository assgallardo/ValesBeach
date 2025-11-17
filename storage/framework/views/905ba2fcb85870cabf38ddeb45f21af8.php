<?php $__env->startSection('title', 'Service Reports Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-900 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-green-50">Reports & Analytics Dashboard</h1>
                    <p class="text-gray-400 mt-2">Booking, Usage, and Performance analytics</p>
                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" 
                            onclick="toggleDateRangeModal()"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Date Range
                    </button>
                    <div class="relative">
                        <button type="button" 
                                onclick="toggleExportDropdown()"
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors w-full sm:w-auto">
                            <i class="fas fa-download mr-2"></i>
                            Export Reports
                            <i class="fas fa-chevron-down ml-2"></i>
                        </button>
                        <div id="exportDropdown" class="absolute right-0 mt-2 w-72 bg-gray-800 border border-gray-700 rounded-lg shadow-xl z-10 hidden max-h-96 overflow-y-auto">
                            <!-- Overview Summary -->
                            <div class="px-3 py-2 bg-gray-750 border-b border-gray-700">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Summary Report</p>
                            </div>
                            <div class="py-1">
                                <a href="<?php echo e(route('manager.reports.export', ['type' => 'overview'] + request()->query())); ?>" 
                                   class="flex items-center px-4 py-2.5 text-gray-300 hover:bg-gray-700 hover:text-green-400 transition-colors">
                                    <i class="fas fa-file-chart-line mr-3 text-yellow-400"></i>
                                    <div>
                                        <div class="font-medium">Overview Report</div>
                                        <div class="text-xs text-gray-500">All metrics summary</div>
                                    </div>
                                </a>
                            </div>

                            <!-- Revenue Reports -->
                            <div class="px-3 py-2 bg-gray-750 border-t border-b border-gray-700">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Revenue Reports</p>
                            </div>
                            <div class="py-1">
                                <a href="<?php echo e(route('manager.reports.export', ['type' => 'room-sales'] + request()->query())); ?>" 
                                   class="flex items-center px-4 py-2.5 text-gray-300 hover:bg-gray-700 hover:text-green-400 transition-colors">
                                    <i class="fas fa-door-open mr-3 text-blue-400"></i>
                                    <div>
                                        <div class="font-medium">Room Sales</div>
                                        <div class="text-xs text-gray-500">Booking revenue</div>
                                    </div>
                                </a>
                                <a href="<?php echo e(route('manager.reports.export', ['type' => 'food-sales'] + request()->query())); ?>" 
                                   class="flex items-center px-4 py-2.5 text-gray-300 hover:bg-gray-700 hover:text-green-400 transition-colors">
                                    <i class="fas fa-utensils mr-3 text-green-400"></i>
                                    <div>
                                        <div class="font-medium">Food Sales</div>
                                        <div class="text-xs text-gray-500">F&B revenue</div>
                                    </div>
                                </a>
                                <a href="<?php echo e(route('manager.reports.export', ['type' => 'service-sales'] + request()->query())); ?>" 
                                   class="flex items-center px-4 py-2.5 text-gray-300 hover:bg-gray-700 hover:text-green-400 transition-colors">
                                    <i class="fas fa-concierge-bell mr-3 text-indigo-400"></i>
                                    <div>
                                        <div class="font-medium">Service Revenue</div>
                                        <div class="text-xs text-gray-500">Service sales</div>
                                    </div>
                                </a>
                            </div>

                            <!-- Operational Reports -->
                            <div class="px-3 py-2 bg-gray-750 border-t border-b border-gray-700">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Operational Reports</p>
                            </div>
                            <div class="py-1">
                                <a href="<?php echo e(route('manager.reports.export', ['type' => 'service-usage'] + request()->query())); ?>" 
                                   class="flex items-center px-4 py-2.5 text-gray-300 hover:bg-gray-700 hover:text-green-400 transition-colors">
                                    <i class="fas fa-chart-bar mr-3 text-purple-400"></i>
                                    <div>
                                        <div class="font-medium">Service Usage</div>
                                        <div class="text-xs text-gray-500">Request trends</div>
                                    </div>
                                </a>
                                <a href="<?php echo e(route('manager.reports.export', ['type' => 'staff-performance'] + request()->query())); ?>" 
                                   class="flex items-center px-4 py-2.5 text-gray-300 hover:bg-gray-700 hover:text-green-400 transition-colors">
                                    <i class="fas fa-users mr-3 text-pink-400"></i>
                                    <div>
                                        <div class="font-medium">Staff Performance</div>
                                        <div class="text-xs text-gray-500">Productivity metrics</div>
                                    </div>
                                </a>
                            </div>

                            <!-- Customer Analytics -->
                            <div class="px-3 py-2 bg-gray-750 border-t border-b border-gray-700">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Customer Analytics</p>
                            </div>
                            <div class="py-1">
                                <a href="<?php echo e(route('manager.reports.export', ['type' => 'repeat-customers'] + request()->query())); ?>" 
                                   class="flex items-center px-4 py-2.5 text-gray-300 hover:bg-gray-700 hover:text-green-400 transition-colors">
                                    <i class="fas fa-user-check mr-3 text-cyan-400"></i>
                                    <div>
                                        <div class="font-medium">Customer Reports</div>
                                        <div class="text-xs text-gray-500">Customer data</div>
                                    </div>
                                </a>
                                <a href="<?php echo e(route('manager.reports.export', ['type' => 'customer-preferences'] + request()->query())); ?>" 
                                   class="flex items-center px-4 py-2.5 text-gray-300 hover:bg-gray-700 hover:text-green-400 transition-colors">
                                    <i class="fas fa-heart mr-3 text-teal-400"></i>
                                    <div>
                                        <div class="font-medium">Customer Preferences</div>
                                        <div class="text-xs text-gray-500">Behavior insights</div>
                                    </div>
                                </a>
                                <a href="<?php echo e(route('manager.reports.export', ['type' => 'payment-methods'] + request()->query())); ?>" 
                                   class="flex items-center px-4 py-2.5 text-gray-300 hover:bg-gray-700 hover:text-green-400 transition-colors">
                                    <i class="fas fa-credit-card mr-3 text-emerald-400"></i>
                                    <div>
                                        <div class="font-medium">Payment Methods</div>
                                        <div class="text-xs text-gray-500">Transaction types</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access to Reports -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <div class="w-1 h-8 bg-gradient-to-b from-green-400 to-green-600 rounded-full mr-3"></div>
                <h2 class="text-2xl font-bold text-white">Quick Access Reports</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                <!-- Room Sales -->
                <a href="<?php echo e(route('manager.reports.room-sales', request()->query())); ?>" 
                   class="group bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg p-5 hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-3 group-hover:bg-white/30 transition-colors">
                            <i class="fas fa-door-open text-white text-2xl"></i>
                        </div>
                        <h3 class="text-white font-bold text-sm mb-1">Room Sales</h3>
                        <p class="text-blue-100 text-xs">Booking Revenue</p>
                    </div>
                </a>

                <!-- Food Sales -->
                <a href="<?php echo e(route('manager.reports.food-sales', request()->query())); ?>" 
                   class="group bg-gradient-to-br from-green-600 to-green-700 rounded-lg p-5 hover:from-green-700 hover:to-green-800 transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-3 group-hover:bg-white/30 transition-colors">
                            <i class="fas fa-utensils text-white text-2xl"></i>
                        </div>
                        <h3 class="text-white font-bold text-sm mb-1">Food Sales</h3>
                        <p class="text-green-100 text-xs">F&B Revenue</p>
                    </div>
                </a>

                <!-- Service Usage -->
                <a href="<?php echo e(route('manager.reports.service-usage', request()->query())); ?>" 
                   class="group bg-gradient-to-br from-purple-600 to-purple-700 rounded-lg p-5 hover:from-purple-700 hover:to-purple-800 transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-3 group-hover:bg-white/30 transition-colors">
                            <i class="fas fa-chart-bar text-white text-2xl"></i>
                        </div>
                        <h3 class="text-white font-bold text-sm mb-1">Service Usage</h3>
                        <p class="text-purple-100 text-xs">Request Trends</p>
                    </div>
                </a>

                <!-- Service Revenue -->
                <a href="<?php echo e(route('manager.reports.service-sales', request()->query())); ?>" 
                   class="group bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-lg p-5 hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-3 group-hover:bg-white/30 transition-colors">
                            <i class="fas fa-concierge-bell text-white text-2xl"></i>
                        </div>
                        <h3 class="text-white font-bold text-sm mb-1">Service Revenue</h3>
                        <p class="text-indigo-100 text-xs">Service Sales</p>
                    </div>
                </a>

                <!-- Staff Performance -->
                <a href="<?php echo e(route('manager.reports.staff-performance', request()->query())); ?>" 
                   class="group bg-gradient-to-br from-pink-600 to-pink-700 rounded-lg p-5 hover:from-pink-700 hover:to-pink-800 transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-3 group-hover:bg-white/30 transition-colors">
                            <i class="fas fa-users text-white text-2xl"></i>
                        </div>
                        <h3 class="text-white font-bold text-sm mb-1">Staff Performance</h3>
                        <p class="text-pink-100 text-xs">Productivity</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Customer Reports Section -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <div class="w-1 h-8 bg-gradient-to-b from-cyan-400 to-cyan-600 rounded-full mr-3"></div>
                <h2 class="text-2xl font-bold text-white">Customer Analytics</h2>
            </div>
            
            <!-- Summary Card -->
            <a href="<?php echo e(route($routePrefix . '.reports.customer-analytics', request()->query())); ?>" 
               class="group bg-gradient-to-br from-cyan-900/40 to-cyan-800/30 rounded-lg border-2 border-cyan-500/50 p-6 mb-6 block hover:border-cyan-400/70 transition-all duration-200 shadow-lg hover:shadow-xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-16 h-16 bg-cyan-600/30 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-chart-line text-cyan-400 text-3xl"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-xl mb-1">Customer Analytics Summary</h3>
                            <p class="text-cyan-200 text-sm">Comprehensive overview of customer behavior, preferences, and payment trends</p>
                        </div>
                    </div>
                    <div class="text-cyan-400 group-hover:text-cyan-300 transition-colors">
                        <i class="fas fa-arrow-right text-2xl"></i>
                    </div>
                </div>
            </a>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Customer Reports -->
                <a href="<?php echo e(route($routePrefix . '.reports.repeat-customers', request()->query())); ?>" 
                   class="group bg-gradient-to-br from-cyan-600 to-cyan-700 rounded-lg p-5 hover:from-cyan-700 hover:to-cyan-800 transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-3 group-hover:bg-white/30 transition-colors">
                            <i class="fas fa-user-check text-white text-2xl"></i>
                        </div>
                        <h3 class="text-white font-bold text-sm mb-1">Customer Reports</h3>
                        <p class="text-cyan-100 text-xs">Customer data & transactions</p>
                    </div>
                </a>

                <!-- Customer Preferences -->
                <a href="<?php echo e(route($routePrefix . '.reports.customer-preferences', request()->query())); ?>" 
                   class="group bg-gradient-to-br from-teal-600 to-teal-700 rounded-lg p-5 hover:from-teal-700 hover:to-teal-800 transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-3 group-hover:bg-white/30 transition-colors">
                            <i class="fas fa-heart text-white text-2xl"></i>
                        </div>
                        <h3 class="text-white font-bold text-sm mb-1">Customer Preferences</h3>
                        <p class="text-teal-100 text-xs">Behavior Insights</p>
                    </div>
                </a>

                <!-- Payment Methods -->
                <a href="<?php echo e(route($routePrefix . '.reports.payment-methods', request()->query())); ?>" 
                   class="group bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-lg p-5 hover:from-emerald-700 hover:to-emerald-800 transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-3 group-hover:bg-white/30 transition-colors">
                            <i class="fas fa-credit-card text-white text-2xl"></i>
                        </div>
                        <h3 class="text-white font-bold text-sm mb-1">Payment Methods</h3>
                        <p class="text-emerald-100 text-xs">Transaction Types</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Overall Performance Metrics -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <div class="w-1 h-8 bg-gradient-to-b from-blue-400 to-blue-600 rounded-full mr-3"></div>
                <h2 class="text-2xl font-bold text-white">Overall Performance Metrics</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gray-800 rounded-lg border border-gray-700 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-blue-600/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clipboard-list text-blue-400 text-xl"></i>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-1"><?php echo e(number_format($stats['total_requests'])); ?></h2>
                    <p class="text-gray-400 text-xs uppercase tracking-wider">Total Requests</p>
                </div>

                <div class="bg-gray-800 rounded-lg border border-gray-700 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-green-600/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-400 text-xl"></i>
                        </div>
                        <?php if($stats['total_requests'] > 0): ?>
                            <span class="px-2 py-1 bg-green-600/20 text-green-400 text-xs font-bold rounded">
                                <?php echo e(round(($stats['completed_requests'] / $stats['total_requests']) * 100, 1)); ?>%
                            </span>
                        <?php endif; ?>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-1"><?php echo e(number_format($stats['completed_requests'])); ?></h2>
                    <p class="text-gray-400 text-xs uppercase tracking-wider">Completed</p>
                </div>

                <div class="bg-gray-800 rounded-lg border border-gray-700 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-yellow-600/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-400 text-xl"></i>
                        </div>
                        <?php if($stats['total_requests'] > 0): ?>
                            <span class="px-2 py-1 bg-yellow-600/20 text-yellow-400 text-xs font-bold rounded">
                                <?php echo e(round(($stats['pending_requests'] / $stats['total_requests']) * 100, 1)); ?>%
                            </span>
                        <?php endif; ?>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-1"><?php echo e(number_format($stats['pending_requests'])); ?></h2>
                    <p class="text-gray-400 text-xs uppercase tracking-wider">Pending</p>
                        </div>

                <div class="bg-gray-800 rounded-lg border border-gray-700 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-purple-600/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tachometer-alt text-purple-400 text-xl"></i>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-1"><?php echo e(round($stats['avg_response_time'], 1)); ?>h</h2>
                    <p class="text-gray-400 text-xs uppercase tracking-wider">Avg Response Time</p>
                </div>
            </div>
        </div>

        <!-- Revenue Highlights Section -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <div class="w-1 h-8 bg-gradient-to-b from-yellow-400 to-yellow-600 rounded-full mr-3"></div>
                <h2 class="text-2xl font-bold text-white">Revenue Highlights</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Rooms/Bookings Revenue -->
                <div class="bg-gray-800 rounded-lg border border-gray-700 p-5 hover:border-blue-500/50 transition-colors">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-blue-600/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-door-open text-blue-400 text-xl"></i>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-1">₱<?php echo e(number_format($revenueStats['rooms_revenue'], 2)); ?></h2>
                    <p class="text-gray-400 text-xs uppercase tracking-wider">Rooms Revenue</p>
                </div>

                <!-- Food Revenue -->
                <div class="bg-gray-800 rounded-lg border border-gray-700 p-5 hover:border-green-500/50 transition-colors">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-green-600/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-utensils text-green-400 text-xl"></i>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-1">₱<?php echo e(number_format($revenueStats['food_revenue'], 2)); ?></h2>
                    <p class="text-gray-400 text-xs uppercase tracking-wider">Food Revenue</p>
                </div>

                <!-- Services Revenue -->
                <div class="bg-gray-800 rounded-lg border border-gray-700 p-5 hover:border-purple-500/50 transition-colors">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-purple-600/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-concierge-bell text-purple-400 text-xl"></i>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-1">₱<?php echo e(number_format($revenueStats['services_revenue'], 2)); ?></h2>
                    <p class="text-gray-400 text-xs uppercase tracking-wider">Services Revenue</p>
                </div>

                <!-- Overall Total Revenue (Highlighted) -->
                <div class="bg-gradient-to-br from-yellow-900/40 to-yellow-800/30 rounded-lg border-2 border-yellow-500/50 p-5 shadow-lg shadow-yellow-500/10">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 bg-yellow-600/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-yellow-400 text-xl"></i>
                        </div>
                        <span class="px-2 py-1 bg-yellow-600/20 text-yellow-400 text-xs font-bold rounded">
                            TOTAL
                        </span>
                    </div>
                    <h2 class="text-3xl font-bold text-yellow-400 mb-1">₱<?php echo e(number_format($revenueStats['total_revenue'], 2)); ?></h2>
                    <p class="text-yellow-200 text-xs uppercase tracking-wider font-semibold">Overall Revenue</p>
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

        <!-- 1. BOOKING & ROOM SALES REPORTS -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <div class="w-1 h-8 bg-gradient-to-b from-blue-400 to-blue-600 rounded-full mr-3"></div>
                <h2 class="text-2xl font-bold text-white">Booking & Room Sales Reports</h2>
            </div>
            
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-900/30 to-blue-800/20 px-6 py-4 border-b border-gray-700 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-600/30 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-door-open text-blue-400 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Room Booking Sales Overview</h3>
                        <p class="text-gray-400 text-xs">Revenue analysis by facility categories</p>
                    </div>
                </div>
                <a href="<?php echo e(route($routePrefix . '.reports.room-sales', request()->query())); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors shadow-lg">
                    <i class="fas fa-chart-bar mr-2"></i>
                    View Full Report
                </a>
            </div>

            <div class="p-6">
                <!-- Quick Stats Grid -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
                        <div class="flex items-center justify-between mb-2">
                            <i class="fas fa-clipboard-list text-blue-400 text-lg"></i>
                        </div>
                        <h4 class="text-2xl font-bold text-green-50"><?php echo e(number_format($roomSalesOverview['total_bookings'])); ?></h4>
                        <p class="text-gray-400 text-xs uppercase tracking-wider mt-1">Total Bookings</p>
                    </div>
                    
                    <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
                        <div class="flex items-center justify-between mb-2">
                            <i class="fas fa-check-circle text-green-400 text-lg"></i>
                        </div>
                        <h4 class="text-2xl font-bold text-green-50"><?php echo e(number_format($roomSalesOverview['completed_bookings'])); ?></h4>
                        <p class="text-gray-400 text-xs uppercase tracking-wider mt-1">Completed</p>
                    </div>
                    
                    <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
                        <div class="flex items-center justify-between mb-2">
                            <i class="fas fa-peso-sign text-purple-400 text-lg"></i>
                        </div>
                        <h4 class="text-2xl font-bold text-green-50">₱<?php echo e(number_format($roomSalesOverview['total_revenue'], 0)); ?></h4>
                        <p class="text-gray-400 text-xs uppercase tracking-wider mt-1">Total Revenue</p>
                    </div>
                    
                    <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
                        <div class="flex items-center justify-between mb-2">
                            <i class="fas fa-chart-line text-yellow-400 text-lg"></i>
                        </div>
                        <h4 class="text-2xl font-bold text-green-50">₱<?php echo e(number_format($roomSalesOverview['avg_booking_value'] ?? 0, 0)); ?></h4>
                        <p class="text-gray-400 text-xs uppercase tracking-wider mt-1">Avg. Value</p>
                    </div>
                </div>

                <!-- Revenue by Categories (Grid Layout) -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <!-- Rooms -->
                    <div class="bg-gray-900 rounded-lg border border-gray-700 overflow-hidden">
                        <div class="px-4 py-3 bg-gradient-to-r from-blue-900/50 to-blue-800/30 border-b border-blue-600/30">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-600/30 rounded-lg flex items-center justify-center mr-2">
                                    <i class="fas fa-door-open text-blue-400 text-sm"></i>
                                </div>
                                <h4 class="text-sm font-semibold text-blue-100 uppercase tracking-wider">Rooms</h4>
                            </div>
                        </div>
                        <div class="p-4">
                            <?php
                                $roomsCategory = $revenueByCategory->firstWhere('category', 'Rooms');
                            ?>
                            <?php if($roomsCategory): ?>
                                <div class="text-center mb-3">
                                    <div class="text-2xl font-bold text-green-400 mb-1">₱<?php echo e(number_format($roomsCategory->total_revenue, 0)); ?></div>
                                    <div class="text-xs text-gray-400"><?php echo e(number_format($roomsCategory->booking_count)); ?> booking<?php echo e($roomsCategory->booking_count != 1 ? 's' : ''); ?></div>
                                </div>
                                <div class="bg-gray-700 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" 
                                         style="width: <?php echo e($roomSalesOverview['total_revenue'] > 0 ? ($roomsCategory->total_revenue / $roomSalesOverview['total_revenue']) * 100 : 0); ?>%;"></div>
                                </div>
                                <div class="text-center text-xs text-gray-400 mt-2">
                                    <?php echo e($roomSalesOverview['total_revenue'] > 0 ? number_format(($roomsCategory->total_revenue / $roomSalesOverview['total_revenue']) * 100, 1) : 0); ?>% of total
                                </div>
                            <?php else: ?>
                                <div class="text-center py-6 text-gray-500">
                                    <i class="fas fa-door-open text-2xl mb-2"></i>
                                    <p class="text-xs">No data</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Cottages -->
                    <div class="bg-gray-900 rounded-lg border border-gray-700 overflow-hidden">
                        <div class="px-4 py-3 bg-gradient-to-r from-amber-900/50 to-amber-800/30 border-b border-amber-600/30">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-amber-600/30 rounded-lg flex items-center justify-center mr-2">
                                    <i class="fas fa-home text-amber-400 text-sm"></i>
                                </div>
                                <h4 class="text-sm font-semibold text-amber-100 uppercase tracking-wider">Cottages</h4>
                            </div>
                        </div>
                        <div class="p-4">
                            <?php
                                $cottagesCategory = $revenueByCategory->firstWhere('category', 'Cottages');
                            ?>
                            <?php if($cottagesCategory): ?>
                                <div class="text-center mb-3">
                                    <div class="text-2xl font-bold text-green-400 mb-1">₱<?php echo e(number_format($cottagesCategory->total_revenue, 0)); ?></div>
                                    <div class="text-xs text-gray-400"><?php echo e(number_format($cottagesCategory->booking_count)); ?> booking<?php echo e($cottagesCategory->booking_count != 1 ? 's' : ''); ?></div>
                                </div>
                                <div class="bg-gray-700 rounded-full h-2">
                                    <div class="bg-amber-500 h-2 rounded-full" 
                                         style="width: <?php echo e($roomSalesOverview['total_revenue'] > 0 ? ($cottagesCategory->total_revenue / $roomSalesOverview['total_revenue']) * 100 : 0); ?>%;"></div>
                                </div>
                                <div class="text-center text-xs text-gray-400 mt-2">
                                    <?php echo e($roomSalesOverview['total_revenue'] > 0 ? number_format(($cottagesCategory->total_revenue / $roomSalesOverview['total_revenue']) * 100, 1) : 0); ?>% of total
                                </div>
                            <?php else: ?>
                                <div class="text-center py-6 text-gray-500">
                                    <i class="fas fa-home text-2xl mb-2"></i>
                                    <p class="text-xs">No data</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Event & Dining -->
                    <div class="bg-gray-900 rounded-lg border border-gray-700 overflow-hidden">
                        <div class="px-4 py-3 bg-gradient-to-r from-purple-900/50 to-purple-800/30 border-b border-purple-600/30">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-purple-600/30 rounded-lg flex items-center justify-center mr-2">
                                    <i class="fas fa-utensils text-purple-400 text-sm"></i>
                                </div>
                                <h4 class="text-sm font-semibold text-purple-100 uppercase tracking-wider">Event & Dining</h4>
                            </div>
                        </div>
                        <div class="p-4">
                            <?php
                                $eventDiningCategory = $revenueByCategory->firstWhere('category', 'Event and Dining');
                            ?>
                            <?php if($eventDiningCategory): ?>
                                <div class="text-center mb-3">
                                    <div class="text-2xl font-bold text-green-400 mb-1">₱<?php echo e(number_format($eventDiningCategory->total_revenue, 0)); ?></div>
                                    <div class="text-xs text-gray-400"><?php echo e(number_format($eventDiningCategory->booking_count)); ?> booking<?php echo e($eventDiningCategory->booking_count != 1 ? 's' : ''); ?></div>
                                </div>
                                <div class="bg-gray-700 rounded-full h-2">
                                    <div class="bg-purple-500 h-2 rounded-full" 
                                         style="width: <?php echo e($roomSalesOverview['total_revenue'] > 0 ? ($eventDiningCategory->total_revenue / $roomSalesOverview['total_revenue']) * 100 : 0); ?>%;"></div>
                                </div>
                                <div class="text-center text-xs text-gray-400 mt-2">
                                    <?php echo e($roomSalesOverview['total_revenue'] > 0 ? number_format(($eventDiningCategory->total_revenue / $roomSalesOverview['total_revenue']) * 100, 1) : 0); ?>% of total
                                </div>
                            <?php else: ?>
                                <div class="text-center py-6 text-gray-500">
                                    <i class="fas fa-utensils text-2xl mb-2"></i>
                                    <p class="text-xs">No data</p>
                                </div>
                    <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <!-- 2. FOOD & BEVERAGE REPORTS -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <div class="w-1 h-8 bg-gradient-to-b from-green-400 to-green-600 rounded-full mr-3"></div>
                <h2 class="text-2xl font-bold text-white">Food & Beverage Reports</h2>
            </div>

            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-green-900/30 to-green-800/20 px-6 py-4 border-b border-gray-700 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-600/30 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-utensils text-green-400 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Food Sales Overview</h3>
                            <p class="text-gray-400 text-xs">Menu items and food order analytics</p>
                        </div>
                    </div>
                    <a href="<?php echo e(route('manager.reports.food-sales', request()->query())); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 transition-colors shadow-lg">
                        <i class="fas fa-chart-pie mr-2"></i>
                        View Full Report
                    </a>
                </div>
                <div class="p-6">
                    <!-- Quick Stats Grid -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
                            <div class="flex items-center justify-between mb-2">
                                <i class="fas fa-shopping-cart text-green-400 text-lg"></i>
                            </div>
                            <h4 class="text-2xl font-bold text-green-50"><?php echo e(number_format($foodSalesOverview['total_orders'])); ?></h4>
                            <p class="text-gray-400 text-xs uppercase tracking-wider mt-1">Total Orders</p>
                        </div>
                        
                        <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
                            <div class="flex items-center justify-between mb-2">
                                <i class="fas fa-check-circle text-green-400 text-lg"></i>
                            </div>
                            <h4 class="text-2xl font-bold text-green-50"><?php echo e(number_format($foodSalesOverview['completed_orders'])); ?></h4>
                            <p class="text-gray-400 text-xs uppercase tracking-wider mt-1">Completed</p>
                        </div>
                        
                        <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
                            <div class="flex items-center justify-between mb-2">
                                <i class="fas fa-peso-sign text-green-400 text-lg"></i>
                            </div>
                            <h4 class="text-2xl font-bold text-green-50">₱<?php echo e(number_format($foodSalesOverview['total_revenue'], 0)); ?></h4>
                            <p class="text-gray-400 text-xs uppercase tracking-wider mt-1">Total Revenue</p>
                        </div>
                        
                        <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
                            <div class="flex items-center justify-between mb-2">
                                <i class="fas fa-chart-line text-green-400 text-lg"></i>
                            </div>
                            <h4 class="text-2xl font-bold text-green-50">₱<?php echo e(number_format($foodSalesOverview['avg_order_value'] ?? 0, 0)); ?></h4>
                            <p class="text-gray-400 text-xs uppercase tracking-wider mt-1">Avg. Order</p>
                        </div>
                    </div>

                    <!-- Top Menu Items -->
                    <?php if($topMenuItems && $topMenuItems->count() > 0): ?>
                    <div class="bg-gray-900 rounded-lg border border-gray-700 overflow-hidden">
                        <div class="px-4 py-3 bg-gradient-to-r from-green-900/50 to-green-800/30 border-b border-green-600/30">
                            <h4 class="text-sm font-semibold text-green-100 flex items-center">
                                <i class="fas fa-star text-yellow-400 mr-2"></i>
                                Top 5 Menu Items
                            </h4>
                        </div>
                        <div class="p-4">
                            <div class="space-y-3">
                                <?php $__currentLoopData = $topMenuItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center flex-1">
                                        <div class="w-8 h-8 bg-green-600/20 rounded-lg flex items-center justify-center mr-3">
                                            <span class="text-green-400 font-bold text-sm"><?php echo e($index + 1); ?></span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-gray-200 text-sm font-medium truncate"><?php echo e($item->name); ?></p>
                                            <p class="text-xs text-gray-400"><?php echo e(number_format($item->total_quantity)); ?> sold</p>
                                        </div>
                                    </div>
                                    <div class="text-right ml-4">
                                        <p class="text-green-400 font-semibold text-sm">₱<?php echo e(number_format($item->total_revenue, 0)); ?></p>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-8">
                        <div class="w-20 h-20 bg-green-600/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-utensils text-green-400 text-3xl"></i>
                        </div>
                        <p class="text-gray-400 text-sm mb-3">No food order data available for this period</p>
                        <a href="<?php echo e(route('manager.reports.food-sales', request()->query())); ?>" 
                           class="inline-flex items-center text-green-400 hover:text-green-300 text-sm font-medium">
                            View Full Report <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 3. SERVICE USAGE & REVENUE REPORTS -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <div class="w-1 h-8 bg-gradient-to-b from-purple-400 to-purple-600 rounded-full mr-3"></div>
                <h2 class="text-2xl font-bold text-white">Service Usage & Revenue Reports</h2>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Service Usage -->
                <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-900/30 to-purple-800/20 px-6 py-4 border-b border-gray-700 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-600/30 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-chart-bar text-purple-400 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-white">Service Usage</h3>
                                <p class="text-gray-400 text-xs">Request trends & categories</p>
                            </div>
                        </div>
                        <a href="<?php echo e(route('manager.reports.service-usage', request()->query())); ?>" 
                           class="inline-flex items-center px-3 py-1.5 bg-purple-600 text-white rounded-lg text-xs hover:bg-purple-700 transition-colors">
                            View
                        </a>
                    </div>
                    <div class="p-6">
                        <div class="relative" style="height: 200px;">
                            <canvas id="serviceUsageChartSmall"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Service Revenue -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-900/30 to-indigo-800/20 px-6 py-4 border-b border-gray-700 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-indigo-600/30 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-concierge-bell text-indigo-400 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-white">Service Revenue</h3>
                                <p class="text-gray-400 text-xs">Service sales & payments</p>
                            </div>
                        </div>
                        <a href="<?php echo e(route('manager.reports.service-sales', request()->query())); ?>" 
                           class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-xs hover:bg-indigo-700 transition-colors">
                            View
                        </a>
                    </div>
                    <div class="p-6">
                        <div class="relative" style="height: 200px;">
                            <canvas id="statusChartSmall"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. STAFF PERFORMANCE REPORTS -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <div class="w-1 h-8 bg-gradient-to-b from-indigo-400 to-indigo-600 rounded-full mr-3"></div>
                <h2 class="text-2xl font-bold text-white">Staff Performance Reports</h2>
        </div>
            
        <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-900/30 to-indigo-800/20 px-6 py-4 border-b border-gray-700 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-indigo-600/30 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-users text-indigo-400 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Staff Performance Overview</h3>
                        <p class="text-gray-400 text-xs">Task assignments and productivity metrics</p>
                    </div>
                </div>
                <a href="<?php echo e(route('manager.reports.staff-performance', request()->query())); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition-colors shadow-lg">
                    <i class="fas fa-users-cog mr-2"></i>
                    View Full Report
                </a>
            </div>
            <div class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-750">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Staff Member
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Assigned Tasks
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Performance
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            <?php $__empty_1 = true; $__currentLoopData = $staffPerformance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-750 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center mr-4">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                        <span class="text-green-100 font-medium"><?php echo e($staff->name); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-600/20 text-blue-400">
                                        <?php echo e($staff->assigned_count); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-1 bg-gray-700 rounded-full h-6 mr-4">
                                            <div class="bg-green-500 h-6 rounded-full flex items-center justify-center text-xs font-medium text-white" 
                                                 style="width: <?php echo e(min($staff->assigned_count * 10, 100)); ?>%;">
                                                <?php if($staff->assigned_count > 0): ?>
                                                    <?php echo e($staff->assigned_count); ?> task<?php echo e($staff->assigned_count != 1 ? 's' : ''); ?>

                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <span class="text-gray-400 text-sm min-w-12"><?php echo e(min($staff->assigned_count * 10, 100)); ?>%</span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <i class="fas fa-users text-4xl text-gray-600 mb-4"></i>
                                    <p class="text-gray-500">No staff performance data available</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>

        <!-- 5. PERFORMANCE ANALYTICS -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <div class="w-1 h-8 bg-gradient-to-b from-green-400 to-green-600 rounded-full mr-3"></div>
                <h2 class="text-2xl font-bold text-white">Performance Analytics</h2>
            </div>
            
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-green-900/30 to-green-800/20 px-6 py-4 border-b border-gray-700 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-600/30 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-chart-line text-green-400 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Daily Request Trends</h3>
                            <p class="text-gray-400 text-xs">Service request patterns over time</p>
                        </div>
                    </div>
                    <a href="<?php echo e(route('manager.reports.performance-metrics', request()->query())); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 transition-colors shadow-lg">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        View Metrics
                    </a>
                </div>
                <div class="p-6">
                    <div class="relative" style="height: 250px;">
                        <canvas id="dailyTrendsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Date Range Modal -->
<div id="dateRangeModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg border border-gray-700 w-full max-w-md">
            <div class="bg-gray-750 px-6 py-4 border-b border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-green-100">Select Date Range</h3>
                    <button onclick="toggleDateRangeModal()" class="text-gray-400 hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <form method="GET" action="<?php echo e(route('manager.reports.index')); ?>">
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-2">Quick Select</label>
                        <select name="period" onchange="toggleCustomDates(this.value)" 
                                class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2 text-gray-300 focus:outline-none focus:border-green-500">
                            <option value="today" <?php echo e(request('period') == 'today' ? 'selected' : ''); ?>>Today</option>
                            <option value="yesterday" <?php echo e(request('period') == 'yesterday' ? 'selected' : ''); ?>>Yesterday</option>
                            <option value="last_7_days" <?php echo e(request('period') == 'last_7_days' ? 'selected' : ''); ?>>Last 7 Days</option>
                            <option value="last_30_days" <?php echo e(request('period', 'last_30_days') == 'last_30_days' ? 'selected' : ''); ?>>Last 30 Days</option>
                            <option value="this_month" <?php echo e(request('period') == 'this_month' ? 'selected' : ''); ?>>This Month</option>
                            <option value="last_month" <?php echo e(request('period') == 'last_month' ? 'selected' : ''); ?>>Last Month</option>
                            <option value="custom" <?php echo e(request('period') == 'custom' ? 'selected' : ''); ?>>Custom Range</option>
                        </select>
                    </div>
                    
                    <div id="customDates" class="space-y-4" style="display: <?php echo e(request('period') == 'custom' ? 'block' : 'none'); ?>;">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Start Date</label>
                            <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>"
                                   class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2 text-gray-300 focus:outline-none focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">End Date</label>
                            <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>"
                                   class="w-full bg-gray-900 border border-gray-600 rounded-lg px-3 py-2 text-gray-300 focus:outline-none focus:border-green-500">
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-750 px-6 py-4 border-t border-gray-700 flex justify-end space-x-3">
                    <button type="button" onclick="toggleDateRangeModal()"
                            class="bg-gray-700 text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        Apply Filter
                    </button>
                </div>
            </form>
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

// Service Usage Chart (Small)
const serviceUsageCtxSmall = document.getElementById('serviceUsageChartSmall').getContext('2d');
new Chart(serviceUsageCtxSmall, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($serviceUsage->pluck('name')->take(5)); ?>,
        datasets: [{
            label: 'Requests',
            data: <?php echo json_encode($serviceUsage->pluck('request_count')->take(5)); ?>,
            backgroundColor: 'rgba(168, 85, 247, 0.8)',
            borderColor: 'rgba(168, 85, 247, 1)',
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
                    color: '#9ca3af',
                    font: { size: 10 }
                },
                grid: {
                    color: '#374151',
                    drawBorder: false
                }
            },
            x: {
                ticks: {
                    color: '#9ca3af',
                    font: { size: 9 },
                    maxRotation: 45
                },
                grid: {
                    display: false
                }
            }
        }
    }
});

// Status Distribution Chart (Small)
const statusCtxSmall = document.getElementById('statusChartSmall').getContext('2d');
new Chart(statusCtxSmall, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($performanceMetrics->pluck('status')); ?>,
        datasets: [{
            data: <?php echo json_encode($performanceMetrics->pluck('count')); ?>,
            backgroundColor: [
                'rgba(239, 68, 68, 0.8)',
                'rgba(99, 102, 241, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(34, 197, 94, 0.8)',
                'rgba(168, 85, 247, 0.8)'
            ],
            borderColor: [
                'rgba(239, 68, 68, 1)',
                'rgba(99, 102, 241, 1)',
                'rgba(245, 158, 11, 1)',
                'rgba(34, 197, 94, 1)',
                'rgba(168, 85, 247, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    padding: 10,
                    color: '#9ca3af',
                    usePointStyle: true,
                    pointStyle: 'circle',
                    font: { size: 10 }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(17, 24, 39, 0.95)',
                titleColor: '#f3f4f6',
                bodyColor: '#f3f4f6',
                borderColor: '#374151',
                borderWidth: 1,
                cornerRadius: 8,
                padding: 8,
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += context.parsed;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        label += ' (' + percentage + '%)';
                        return label;
                    }
                }
            }
        }
    }
});

// Daily Trends Chart
const dailyTrendsCtx = document.getElementById('dailyTrendsChart').getContext('2d');
new Chart(dailyTrendsCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($dailyTrends->pluck('date')); ?>,
        datasets: [{
            label: 'Daily Requests',
            data: <?php echo json_encode($dailyTrends->pluck('request_count')); ?>,
            fill: true,
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            borderColor: 'rgba(34, 197, 94, 1)',
            borderWidth: 3,
            tension: 0.4,
            pointRadius: 6,
            pointHoverRadius: 8,
            pointBackgroundColor: 'rgba(34, 197, 94, 1)',
            pointBorderColor: '#111827',
            pointBorderWidth: 2
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
                    precision: 0,
                    color: '#9ca3af'
                },
                grid: {
                    color: '#374151',
                    drawBorder: false
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
                }
            }
        }
    }
});

// Modal and dropdown functions
function toggleDateRangeModal() {
    const modal = document.getElementById('dateRangeModal');
    modal.classList.toggle('hidden');
}

function toggleExportDropdown() {
    const dropdown = document.getElementById('exportDropdown');
    dropdown.classList.toggle('hidden');
}

function toggleCustomDates(value) {
    const customDates = document.getElementById('customDates');
    customDates.style.display = value === 'custom' ? 'block' : 'none';
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const exportButton = event.target.closest('[onclick="toggleExportDropdown()"]');
    const exportDropdown = document.getElementById('exportDropdown');
    
    if (!exportButton && !exportDropdown.contains(event.target)) {
        exportDropdown.classList.add('hidden');
    }
});

// Close modal when clicking outside
document.getElementById('dateRangeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        toggleDateRangeModal();
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/manager/reports/index.blade.php ENDPATH**/ ?>