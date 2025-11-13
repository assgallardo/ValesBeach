<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 lg:px-8 py-8">
    <!-- Page Title -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-white">Manage Reservations</h1>
                <p class="text-gray-400 mt-2">View and manage all resort reservations</p>
            </div>
            <?php if(in_array(auth()->user()->role, ['admin', 'manager', 'staff'])): ?>
            <div class="flex space-x-3">
                <!-- Quick Room Selection for Booking with Search -->
                <div x-data="{ 
                    open: false, 
                    searchQuery: '',
                    clearSearch() {
                        this.searchQuery = '';
                        this.$refs.searchInput.focus();
                    }
                }" class="relative">
                    <button @click="open = !open; if(open) { $nextTick(() => $refs.searchInput.focus()); }" 
                            class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Quick Book Room
                        <svg class="w-4 h-4 ml-2 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         @click.away="open = false"
                         class="absolute right-0 mt-2 w-80 bg-gray-800 rounded-lg shadow-xl z-50"
                         style="display: none;">
                        
                        <!-- Search Input -->
                        <div class="p-3 border-b border-gray-700">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input x-ref="searchInput"
                                       x-model="searchQuery"
                                       type="text"
                                       placeholder="Search facilities..."
                                       class="w-full pl-10 pr-10 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <button x-show="searchQuery.length > 0"
                                        @click="clearSearch()"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Room List -->
                        <div class="max-h-64 overflow-y-auto">
                            <?php
                                $availableRooms = \App\Models\Room::where('is_available', true)->orderBy('category')->orderBy('name')->get();
                            ?>
                            <?php $__currentLoopData = $availableRooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('admin.reservations.createFromRoom', $room)); ?>" 
                                   x-show="searchQuery === '' || '<?php echo e(strtolower($room->name)); ?> <?php echo e(strtolower($room->category ?? '')); ?> <?php echo e(strtolower($room->type ?? '')); ?>'.includes(searchQuery.toLowerCase())"
                                   class="block px-4 py-3 text-sm text-gray-300 hover:bg-gray-700 hover:text-white border-b border-gray-700 last:border-b-0 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="font-medium text-white"><?php echo e($room->name); ?></div>
                                            <div class="text-xs text-gray-400 mt-1">
                                                <span class="inline-flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                    </svg>
                                                    ₱<?php echo e(number_format((float)$room->price, 2)); ?>/night
                                                </span>
                                                <span class="mx-2">•</span>
                                                <span class="inline-flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                    </svg>
                                                    <?php echo e($room->capacity); ?> guests
                                                </span>
                                            </div>
                                            <?php if($room->category): ?>
                                            <div class="mt-1">
                                                <span class="inline-block px-2 py-0.5 text-xs rounded-full
                                                    <?php echo e($room->category === 'Rooms' ? 'bg-blue-900 text-blue-200' : ''); ?>

                                                    <?php echo e($room->category === 'Cottages' ? 'bg-purple-900 text-purple-200' : ''); ?>

                                                    <?php echo e($room->category === 'Event and Dining' ? 'bg-pink-900 text-pink-200' : ''); ?>">
                                                    <?php echo e($room->category); ?>

                                                </span>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if($availableRooms->isEmpty()): ?>
                                <div class="px-4 py-8 text-sm text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    No available facilities
                                </div>
                            <?php endif; ?>
                            <div x-show="searchQuery !== '' && !<?php echo e($availableRooms->count() > 0 ? 'true' : 'false'); ?>" 
                                 class="px-4 py-8 text-sm text-center text-gray-400"
                                 style="display: none;">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                No facilities match your search
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Original Manual Booking -->
                <a href="<?php echo e(route('admin.reservations.create')); ?>" 
                   class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors duration-200 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Manual Reservation
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Success Message -->
    <?php if(session('success')): ?>
    <div class="bg-green-800 border border-green-600 text-green-100 px-6 py-4 rounded-lg mb-8">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <?php echo e(session('success')); ?>

        </div>
    </div>
    <?php endif; ?>

    <!-- Error Message -->
    <?php if(session('error')): ?>
    <div class="bg-red-800 border border-red-600 text-red-100 px-6 py-4 rounded-lg mb-8">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <?php echo e(session('error')); ?>

        </div>
    </div>
    <?php endif; ?>

    <!-- Validation Errors -->
    <?php if($errors->any()): ?>
    <div class="bg-red-800 border border-red-600 text-red-100 px-6 py-4 rounded-lg mb-8">
        <div class="font-bold mb-2">Please fix the following errors:</div>
        <ul class="list-disc list-inside">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="bg-gray-800 rounded-lg p-6 mb-8">
        <form action="<?php echo e(route('admin.reservations')); ?>" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-300 mb-2">Search</label>
                <input type="text" 
                       name="search" 
                       id="search" 
                       value="<?php echo e(request('search')); ?>"
                       placeholder="Guest name, email, or room..."
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                <select name="status" id="status" 
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($status); ?>" <?php echo e(request('status') === $status ? 'selected' : ''); ?>>
                            <?php echo e(ucfirst(str_replace('_', ' ', $status))); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <!-- Category Filter -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-300 mb-2">Category</label>
                <select name="category" id="category" 
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    <option value="Rooms" <?php echo e(request('category') === 'Rooms' ? 'selected' : ''); ?>>Rooms</option>
                    <option value="Cottages" <?php echo e(request('category') === 'Cottages' ? 'selected' : ''); ?>>Cottages</option>
                    <option value="Event and Dining" <?php echo e(request('category') === 'Event and Dining' ? 'selected' : ''); ?>>Event and Dining</option>
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-300 mb-2">Check-in From</label>
                <input type="date" 
                       name="date_from" 
                       id="date_from" 
                       value="<?php echo e(request('date_from')); ?>"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Date To -->
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-300 mb-2">Check-out To</label>
                <input type="date" 
                       name="date_to" 
                       id="date_to" 
                       value="<?php echo e(request('date_to')); ?>"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Actions -->
            <div class="flex items-end space-x-2">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Filter
                </button>
                <a href="<?php echo e(route('admin.reservations')); ?>" 
                   class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Reservations</p>
                    <p class="text-2xl font-bold"><?php echo e($allBookings->total()); ?></p>
                </div>
                <div class="bg-blue-500 bg-opacity-50 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Active Bookings</p>
                    <p class="text-2xl font-bold"><?php echo e($allBookings->where('status', 'confirmed')->count() + $allBookings->where('status', 'checked_in')->count()); ?></p>
                </div>
                <div class="bg-green-500 bg-opacity-50 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Completed</p>
                    <p class="text-2xl font-bold"><?php echo e($allBookings->where('status', 'completed')->count()); ?></p>
                </div>
                <div class="bg-purple-500 bg-opacity-50 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm">Cancelled</p>
                    <p class="text-2xl font-bold"><?php echo e($allBookings->where('status', 'cancelled')->count()); ?></p>
                </div>
                <div class="bg-red-500 bg-opacity-50 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs for Room/Cottage Bookings -->
    <div x-data="{ activeTab: '<?php echo e(request('type', 'all')); ?>' }" class="mb-8">
        <!-- Tab Navigation -->
        <div class="bg-gray-800 rounded-t-lg">
            <div class="flex border-b border-gray-700 overflow-x-auto">
                <button @click="activeTab = 'all'; window.location.href = '<?php echo e(route('admin.reservations', ['type' => 'all'] + request()->except('type'))); ?>'" 
                        :class="activeTab === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700'"
                        class="px-6 py-4 font-medium transition-colors duration-200 rounded-tl-lg flex items-center space-x-2 whitespace-nowrap">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span>All Bookings (<?php echo e($allBookings->total()); ?>)</span>
                </button>
                <button @click="activeTab = 'room'; window.location.href = '<?php echo e(route('admin.reservations', ['type' => 'room'] + request()->except('type'))); ?>'" 
                        :class="activeTab === 'room' ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700'"
                        class="px-6 py-4 font-medium transition-colors duration-200 flex items-center space-x-2 whitespace-nowrap">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span>Room Bookings (<?php echo e($bookings->total()); ?>)</span>
                </button>
                <button @click="activeTab = 'cottage'; window.location.href = '<?php echo e(route('admin.reservations', ['type' => 'cottage'] + request()->except('type'))); ?>'" 
                        :class="activeTab === 'cottage' ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700'"
                        class="px-6 py-4 font-medium transition-colors duration-200 flex items-center space-x-2 whitespace-nowrap">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Cottage Bookings (<?php echo e($cottageBookings->total()); ?>)</span>
                </button>
                <button @click="activeTab = 'event'; window.location.href = '<?php echo e(route('admin.reservations', ['type' => 'event'] + request()->except('type'))); ?>'" 
                        :class="activeTab === 'event' ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700'"
                        class="px-6 py-4 font-medium transition-colors duration-200 flex items-center space-x-2 whitespace-nowrap">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Events & Dining (<?php echo e($eventDiningBookings->total()); ?>)</span>
                </button>
            </div>
        </div>

        <!-- All Bookings Tab Content -->
        <div x-show="activeTab === 'all'" class="bg-gray-800 rounded-b-lg shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Guest</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Facility</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Dates</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php $__empty_0 = true; $__currentLoopData = $allBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <tr class="hover:bg-gray-700 transition-colors" id="all-booking-row-<?php echo e($booking->id); ?>">
                            <td class="px-6 py-4">
                                <div class="text-white font-medium">#<?php echo e($booking->id); ?></div>
                                <div class="text-xs text-gray-400"><?php echo e($booking->created_at->format('M d, Y')); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white"><?php echo e($booking->user->name ?? 'N/A'); ?></div>
                                <div class="text-sm text-gray-400"><?php echo e($booking->user->email ?? 'N/A'); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white booking-room-name"><?php echo e($booking->room->name ?? 'N/A'); ?></div>
                                <div class="text-sm text-gray-400 booking-guests"><?php echo e($booking->guests); ?> guests</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    <?php echo e($booking->room->category === 'Rooms' ? 'bg-blue-900 text-blue-200' : ''); ?>

                                    <?php echo e($booking->room->category === 'Cottages' ? 'bg-purple-900 text-purple-200' : ''); ?>

                                    <?php echo e($booking->room->category === 'Event and Dining' ? 'bg-pink-900 text-pink-200' : ''); ?>">
                                    <?php echo e($booking->room->category ?? 'N/A'); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 booking-dates">
                                <div class="text-white"><?php echo e($booking->check_in->format('M d, Y')); ?></div>
                                <div class="text-sm text-gray-400"><?php echo e($booking->check_in->format('l')); ?> at <?php echo e($booking->room->check_in_time ? \Carbon\Carbon::parse($booking->room->check_in_time)->format('g:i A') : '12:00 AM'); ?></div>
                                <div class="text-white mt-1"><?php echo e($booking->check_out->format('M d, Y')); ?></div>
                                <div class="text-sm text-gray-400"><?php echo e($booking->check_out->format('l')); ?> at <?php echo e($booking->room->check_out_time ? \Carbon\Carbon::parse($booking->room->check_out_time)->format('g:i A') : '12:00 AM'); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-green-400 font-semibold booking-total">₱<?php echo e(number_format((float)$booking->total_price, 2)); ?></div>
                                <div class="text-sm text-gray-400 booking-nights">
                                    <?php
                                        $checkIn = $booking->check_in->copy()->startOfDay();
                                        $checkOut = $booking->check_out->copy()->startOfDay();
                                        $daysDiff = $checkIn->diffInDays($checkOut);
                                        $nights = $daysDiff === 0 ? 1 : $daysDiff;
                                    ?>
                                    <?php echo e($nights); ?> night(s)
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium booking-status
                                    <?php echo e($booking->status === 'pending' ? 'bg-yellow-900 text-yellow-200' : ''); ?>

                                    <?php echo e($booking->status === 'confirmed' ? 'bg-blue-900 text-blue-200' : ''); ?>

                                    <?php echo e($booking->status === 'checked_in' ? 'bg-green-900 text-green-200' : ''); ?>

                                    <?php echo e($booking->status === 'checked_out' ? 'bg-indigo-900 text-indigo-200' : ''); ?>

                                    <?php echo e($booking->status === 'completed' ? 'bg-purple-900 text-purple-200' : ''); ?>

                                    <?php echo e($booking->status === 'cancelled' ? 'bg-red-900 text-red-200' : ''); ?>">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $booking->status))); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <!-- View Details -->
                                    <a href="<?php echo e(route('admin.reservations.show', $booking)); ?>" 
                                       class="text-blue-400 hover:text-blue-300 transition-colors" title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <?php if(in_array(auth()->user()->role, ['admin', 'manager', 'staff'])): ?>
                                    <!-- Edit Booking Details -->
                                    <button type="button"
                                            onclick='editBookingDetails(<?php echo json_encode($booking->load(["user", "room"]), 512) ?>)'
                                            class="text-green-400 hover:text-green-300 transition-colors" title="Edit Booking">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <!-- Update Status -->
                                    <button type="button"
                                            onclick="updateStatus('<?php echo e($booking->id); ?>')"
                                            class="text-yellow-400 hover:text-yellow-300 transition-colors" title="Update Status">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                No bookings found.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination for All Bookings -->
            <div class="px-6 py-3 bg-gray-900">
                <?php echo e($allBookings->appends(request()->query())->links()); ?>

            </div>
        </div>

        <!-- Room Bookings Tab Content -->
        <div x-show="activeTab === 'room'" class="bg-gray-800 rounded-b-lg shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Guest</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Facility</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Dates</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php $__empty_0 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                    <tr class="hover:bg-gray-700 transition-colors" id="booking-row-<?php echo e($booking->id); ?>">
                        <td class="px-6 py-4">
                            <div class="text-white font-medium">#<?php echo e($booking->id); ?></div>
                            <div class="text-xs text-gray-400"><?php echo e($booking->created_at->format('M d, Y')); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white"><?php echo e($booking->user->name ?? 'N/A'); ?></div>
                            <div class="text-sm text-gray-400"><?php echo e($booking->user->email ?? 'N/A'); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white booking-room-name"><?php echo e($booking->room->name ?? 'N/A'); ?></div>
                            <div class="text-sm text-gray-400 booking-guests"><?php echo e($booking->guests); ?> guests</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                <?php echo e($booking->room->category === 'Rooms' ? 'bg-blue-900 text-blue-200' : ''); ?>

                                <?php echo e($booking->room->category === 'Cottages' ? 'bg-purple-900 text-purple-200' : ''); ?>

                                <?php echo e($booking->room->category === 'Event and Dining' ? 'bg-pink-900 text-pink-200' : ''); ?>">
                                <?php echo e($booking->room->category ?? 'N/A'); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 booking-dates">
                            <div class="text-white"><?php echo e($booking->check_in->format('M d, Y')); ?></div>
                            <div class="text-sm text-gray-400"><?php echo e($booking->check_in->format('l')); ?> at <?php echo e($booking->room->check_in_time ? \Carbon\Carbon::parse($booking->room->check_in_time)->format('g:i A') : '12:00 AM'); ?></div>
                            <div class="text-white mt-1"><?php echo e($booking->check_out->format('M d, Y')); ?></div>
                            <div class="text-sm text-gray-400"><?php echo e($booking->check_out->format('l')); ?> at <?php echo e($booking->room->check_out_time ? \Carbon\Carbon::parse($booking->room->check_out_time)->format('g:i A') : '12:00 AM'); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-green-400 font-semibold booking-total">₱<?php echo e(number_format((float)$booking->total_price, 2)); ?></div>
                            <div class="text-sm text-gray-400 booking-nights">
                                <?php
                                    $checkIn = $booking->check_in->copy()->startOfDay();
                                    $checkOut = $booking->check_out->copy()->startOfDay();
                                    $daysDiff = $checkIn->diffInDays($checkOut);
                                    $nights = $daysDiff === 0 ? 1 : $daysDiff;
                                ?>
                                <?php echo e($nights); ?> night(s)
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-medium booking-status
                                <?php echo e($booking->status === 'pending' ? 'bg-yellow-900 text-yellow-200' : ''); ?>

                                <?php echo e($booking->status === 'confirmed' ? 'bg-blue-900 text-blue-200' : ''); ?>

                                <?php echo e($booking->status === 'checked_in' ? 'bg-green-900 text-green-200' : ''); ?>

                                <?php echo e($booking->status === 'checked_out' ? 'bg-indigo-900 text-indigo-200' : ''); ?>

                                <?php echo e($booking->status === 'completed' ? 'bg-purple-900 text-purple-200' : ''); ?>

                                <?php echo e($booking->status === 'cancelled' ? 'bg-red-900 text-red-200' : ''); ?>">
                                <?php echo e(ucfirst(str_replace('_', ' ', $booking->status))); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <a href="<?php echo e(route('admin.reservations.show', $booking)); ?>" 
                                   class="text-blue-400 hover:text-blue-300 transition-colors"
                                   title="View Details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <?php if(in_array(auth()->user()->role, ['admin', 'manager', 'staff'])): ?>
                                <!-- Edit Booking Details Button -->
                                <button type="button"
                                        onclick='editBookingDetails(<?php echo json_encode($booking->load(["user", "room"]), 512) ?>)'
                                        class="text-green-400 hover:text-green-300 transition-colors"
                                        title="Edit Booking Details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <!-- Status Update Button -->
                                <button type="button"
                                        onclick="updateStatus('<?php echo e($booking->id); ?>')"
                                        class="text-yellow-400 hover:text-yellow-300 transition-colors"
                                        title="Update Status">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                                No room bookings found.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination for Room Bookings -->
            <div class="px-6 py-3 bg-gray-900">
                <?php echo e($bookings->appends(request()->query())->links()); ?>

            </div>
        </div>

        <!-- Cottage Bookings Tab Content -->
        <div x-show="activeTab === 'cottage'" class="bg-gray-800 rounded-b-lg shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Guest</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Facility</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Dates</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php $__empty_0 = true; $__currentLoopData = $cottageBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cottageBooking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <tr class="hover:bg-gray-700 transition-colors" id="cottage-booking-row-<?php echo e($cottageBooking->id); ?>">
                            <td class="px-6 py-4">
                                <div class="text-white font-medium">#<?php echo e($cottageBooking->id); ?></div>
                                <div class="text-xs text-gray-400"><?php echo e($cottageBooking->created_at->format('M d, Y')); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white"><?php echo e($cottageBooking->user->name ?? 'N/A'); ?></div>
                                <div class="text-sm text-gray-400"><?php echo e($cottageBooking->user->email ?? 'N/A'); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white booking-room-name"><?php echo e($cottageBooking->room->name ?? 'N/A'); ?></div>
                                <div class="text-sm text-gray-400 booking-guests"><?php echo e($cottageBooking->guests); ?> guests</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    <?php echo e($cottageBooking->room->category === 'Rooms' ? 'bg-blue-900 text-blue-200' : ''); ?>

                                    <?php echo e($cottageBooking->room->category === 'Cottages' ? 'bg-purple-900 text-purple-200' : ''); ?>

                                    <?php echo e($cottageBooking->room->category === 'Event and Dining' ? 'bg-pink-900 text-pink-200' : ''); ?>">
                                    <?php echo e($cottageBooking->room->category ?? 'N/A'); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 booking-dates">
                                <div class="text-white"><?php echo e($cottageBooking->check_in->format('M d, Y')); ?></div>
                                <div class="text-sm text-gray-400"><?php echo e($cottageBooking->check_in->format('l')); ?> at <?php echo e($cottageBooking->room->check_in_time ? \Carbon\Carbon::parse($cottageBooking->room->check_in_time)->format('g:i A') : '12:00 AM'); ?></div>
                                <div class="text-white mt-1"><?php echo e($cottageBooking->check_out->format('M d, Y')); ?></div>
                                <div class="text-sm text-gray-400"><?php echo e($cottageBooking->check_out->format('l')); ?> at <?php echo e($cottageBooking->room->check_out_time ? \Carbon\Carbon::parse($cottageBooking->room->check_out_time)->format('g:i A') : '12:00 AM'); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-green-400 font-semibold booking-total">₱<?php echo e(number_format((float)$cottageBooking->total_price, 2)); ?></div>
                                <div class="text-sm text-gray-400 booking-nights">
                                    <?php
                                        $checkIn = $cottageBooking->check_in->copy()->startOfDay();
                                        $checkOut = $cottageBooking->check_out->copy()->startOfDay();
                                        $daysDiff = $checkIn->diffInDays($checkOut);
                                        $nights = $daysDiff === 0 ? 1 : $daysDiff;
                                    ?>
                                    <?php echo e($nights); ?> night(s)
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium booking-status
                                    <?php echo e($cottageBooking->status === 'pending' ? 'bg-yellow-900 text-yellow-200' : ''); ?>

                                    <?php echo e($cottageBooking->status === 'confirmed' ? 'bg-blue-900 text-blue-200' : ''); ?>

                                    <?php echo e($cottageBooking->status === 'checked_in' ? 'bg-green-900 text-green-200' : ''); ?>

                                    <?php echo e($cottageBooking->status === 'checked_out' ? 'bg-indigo-900 text-indigo-200' : ''); ?>

                                    <?php echo e($cottageBooking->status === 'completed' ? 'bg-purple-900 text-purple-200' : ''); ?>

                                    <?php echo e($cottageBooking->status === 'cancelled' ? 'bg-red-900 text-red-200' : ''); ?>">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $cottageBooking->status))); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <!-- View Details -->
                                    <a href="<?php echo e(route('admin.reservations.show', $cottageBooking)); ?>" 
                                       class="text-blue-400 hover:text-blue-300 transition-colors" title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <?php if(in_array(auth()->user()->role, ['admin', 'manager', 'staff'])): ?>
                                    <!-- Edit Booking Details -->
                                    <button type="button"
                                            onclick='editBookingDetails(<?php echo json_encode($cottageBooking->load(["user", "room"]), 512) ?>)'
                                            class="text-green-400 hover:text-green-300 transition-colors" title="Edit Booking">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <!-- Update Status -->
                                    <button type="button"
                                            onclick="updateStatus('<?php echo e($cottageBooking->id); ?>')"
                                            class="text-yellow-400 hover:text-yellow-300 transition-colors" title="Update Status">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                                No cottage bookings found.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination for Cottage Bookings -->
            <div class="px-6 py-3 bg-gray-900">
                <?php echo e($cottageBookings->appends(request()->query())->links()); ?>

            </div>
        </div>

        <!-- Events & Dining Bookings Tab Content -->
        <div x-show="activeTab === 'event'" class="bg-gray-800 rounded-b-lg shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Guest</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Facility</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Dates</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php $__empty_0 = true; $__currentLoopData = $eventDiningBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eventBooking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <tr class="hover:bg-gray-700 transition-colors" id="event-booking-row-<?php echo e($eventBooking->id); ?>">
                            <td class="px-6 py-4">
                                <div class="text-white font-medium">#E<?php echo e($eventBooking->id); ?></div>
                                <div class="text-xs text-gray-400"><?php echo e($eventBooking->created_at->format('M d, Y')); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white"><?php echo e($eventBooking->user->name ?? 'N/A'); ?></div>
                                <div class="text-sm text-gray-400"><?php echo e($eventBooking->user->email ?? 'N/A'); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white booking-room-name"><?php echo e($eventBooking->room->name ?? 'N/A'); ?></div>
                                <div class="text-sm text-gray-400 booking-guests"><?php echo e($eventBooking->guests); ?> guests</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-pink-900 text-pink-200">
                                    <?php echo e($eventBooking->room->type ?? 'Event & Dining'); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 booking-dates">
                                <div class="text-white"><?php echo e($eventBooking->check_in->format('M d, Y')); ?></div>
                                <div class="text-sm text-gray-400"><?php echo e($eventBooking->check_in->format('l')); ?> at <?php echo e($eventBooking->room->check_in_time ? \Carbon\Carbon::parse($eventBooking->room->check_in_time)->format('g:i A') : '12:00 AM'); ?></div>
                                <div class="text-white mt-1"><?php echo e($eventBooking->check_out->format('M d, Y')); ?></div>
                                <div class="text-sm text-gray-400"><?php echo e($eventBooking->check_out->format('l')); ?> at <?php echo e($eventBooking->room->check_out_time ? \Carbon\Carbon::parse($eventBooking->room->check_out_time)->format('g:i A') : '12:00 AM'); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-green-400 font-semibold booking-total">₱<?php echo e(number_format((float)$eventBooking->total_price, 2)); ?></div>
                                <div class="text-sm text-gray-400 booking-nights">
                                    <?php
                                        $checkIn = $eventBooking->check_in->copy()->startOfDay();
                                        $checkOut = $eventBooking->check_out->copy()->startOfDay();
                                        $daysDiff = $checkIn->diffInDays($checkOut);
                                        $nights = $daysDiff === 0 ? 1 : $daysDiff;
                                    ?>
                                    <?php echo e($nights); ?> night(s)
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium booking-status
                                    <?php echo e($eventBooking->status === 'pending' ? 'bg-yellow-900 text-yellow-200' : ''); ?>

                                    <?php echo e($eventBooking->status === 'confirmed' ? 'bg-blue-900 text-blue-200' : ''); ?>

                                    <?php echo e($eventBooking->status === 'checked_in' ? 'bg-green-900 text-green-200' : ''); ?>

                                    <?php echo e($eventBooking->status === 'checked_out' ? 'bg-indigo-900 text-indigo-200' : ''); ?>

                                    <?php echo e($eventBooking->status === 'completed' ? 'bg-purple-900 text-purple-200' : ''); ?>

                                    <?php echo e($eventBooking->status === 'cancelled' ? 'bg-red-900 text-red-200' : ''); ?>">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $eventBooking->status))); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <!-- View Details -->
                                    <a href="<?php echo e(route('admin.reservations.show', $eventBooking)); ?>" 
                                       class="text-blue-400 hover:text-blue-300 transition-colors" title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <?php if(in_array(auth()->user()->role, ['admin', 'manager', 'staff'])): ?>
                                    <!-- Edit Booking Details -->
                                    <button type="button"
                                            onclick='editBookingDetails(<?php echo json_encode($eventBooking->load(["user", "room"]), 512) ?>)'
                                            class="text-green-400 hover:text-green-300 transition-colors" title="Edit Booking">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <!-- Update Status -->
                                    <button type="button"
                                            onclick="updateStatus('<?php echo e($eventBooking->id); ?>')"
                                            class="text-yellow-400 hover:text-yellow-300 transition-colors" title="Update Status">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                No event & dining bookings found.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination for Events & Dining Bookings -->
            <div class="px-6 py-3 bg-gray-900">
                <?php echo e($eventDiningBookings->appends(request()->query())->links()); ?>

            </div>
        </div>
    </div>

    <!-- Edit Booking Details Modal -->
    <div id="editBookingModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-white">Edit Booking Details</h3>
                        <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <form id="editBookingForm" method="POST" class="space-y-6">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        
                        <!-- Guest Information (Read-only) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Guest Name</label>
                                <input type="text" id="edit_guest_name" readonly
                                       class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-gray-300 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Guest Email</label>
                                <input type="email" id="edit_guest_email" readonly
                                       class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-gray-300 cursor-not-allowed">
                            </div>
                        </div>

                        <!-- Room Selection -->
                        <div>
                            <label for="edit_room_id" class="block text-sm font-medium text-gray-300 mb-2">Room</label>
                            <select name="room_id" id="edit_room_id" required
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Room</option>
                                <?php
                                    $allRooms = \App\Models\Room::all();
                                ?>
                                <?php $__currentLoopData = $allRooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($room->id); ?>" 
                                            data-price="<?php echo e($room->price); ?>" 
                                            data-capacity="<?php echo e($room->capacity); ?>"
                                            data-check-in-time="<?php echo e($room->check_in_time ?? '00:00:00'); ?>"
                                            data-check-out-time="<?php echo e($room->check_out_time ?? '00:00:00'); ?>">
                                        <?php echo e($room->name); ?> - ₱<?php echo e(number_format($room->price, 2)); ?>/night (Max: <?php echo e($room->capacity); ?> guests)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Booking Dates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="edit_check_in_date" class="block text-sm font-medium text-gray-300 mb-2">Check-in Date</label>
                                <input type="date" name="check_in_date" id="edit_check_in_date" required
                                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p id="edit_check_in_time_display" class="text-sm text-gray-400 mt-2">
                                    <i class="fas fa-clock mr-1"></i>Check-in time: 12:00 AM
                                </p>
                                <input type="hidden" name="check_in" id="edit_check_in">
                            </div>
                            <div>
                                <label for="edit_check_out_date" class="block text-sm font-medium text-gray-300 mb-2">Check-out Date</label>
                                <input type="date" name="check_out_date" id="edit_check_out_date" required
                                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p id="edit_check_out_time_display" class="text-sm text-gray-400 mt-2">
                                    <i class="fas fa-clock mr-1"></i>Check-out time: 12:00 AM
                                </p>
                                <input type="hidden" name="check_out" id="edit_check_out">
                            </div>
                        </div>

                        <!-- Number of Guests -->
                        <div>
                            <label for="edit_guests" class="block text-sm font-medium text-gray-300 mb-2">
                                Number of Guests
                                <span id="edit_capacity_info" class="text-blue-400 text-xs font-normal ml-2"></span>
                            </label>
                            <input type="number" name="guests" id="edit_guests" min="1" max="20" required
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p id="edit_capacity_warning" class="text-yellow-400 text-sm mt-2 hidden">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                Maximum capacity exceeded!
                            </p>
                        </div>

                        <!-- Special Requests -->
                        <div>
                            <label for="edit_special_requests" class="block text-sm font-medium text-gray-300 mb-2">Special Requests</label>
                            <textarea name="special_requests" id="edit_special_requests" rows="3"
                                      class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Any special requests or notes..."></textarea>
                        </div>

                        <!-- Total Price Display -->
                        <div class="bg-gray-700 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-300">Total Amount:</span>
                                <span id="edit_total_display" class="text-green-400 font-bold text-xl">₱0.00</span>
                            </div>
                            <div class="text-sm text-gray-400 mt-2">
                                <span id="edit_nights_display">0 nights</span> × <span id="edit_rate_display">₱0.00/night</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeEditModal()"
                                    class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-500 transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-500 transition-colors">
                                Update Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Update Booking Status</h3>
                    <form id="statusForm" method="POST" class="space-y-4">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                            <select name="status" id="status" onchange="handleStatusChange(this.value)"
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                                <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($status); ?>">
                                        <?php echo e(ucfirst(str_replace('_', ' ', $status))); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        
                        <!-- Checkout Confirmation Notice -->
                        <div id="checkoutNotice" class="hidden bg-purple-900/50 border border-purple-600 rounded-lg p-4">
                            <div class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-purple-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="flex-1">
                                    <h4 class="text-purple-100 font-semibold mb-2">
                                        <i class="fas fa-broom mr-2"></i>Housekeeping Will Be Deployed
                                    </h4>
                                    <p class="text-purple-200 text-sm">
                                        When you confirm checkout, a housekeeping task will be automatically created and sent to the Task Assignment module. A manager can then assign the task to available staff to clean and prepare the room.
                                    </p>
                                    <div class="mt-3 text-xs text-purple-300">
                                        <i class="fas fa-clock mr-1"></i> Task deadline: 2 hours from checkout
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeModal()"
                                    class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-500 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" id="updateStatusBtn"
                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500 transition-colors">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<?php $__env->startPush('scripts'); ?>
<script>
let currentBookingId = null;

// Edit Booking Details Function
function editBookingDetails(booking) {
    console.log('Editing booking:', booking);
    
    const modal = document.getElementById('editBookingModal');
    const form = document.getElementById('editBookingForm');
    
    currentBookingId = booking.id;
    
    // Set form action - use the update route with the booking ID
    form.action = `<?php echo e(url('admin/reservations')); ?>/${booking.id}`;
    
    // Add method spoofing for PUT request
    let methodField = form.querySelector('input[name="_method"]');
    if (!methodField) {
        methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        form.appendChild(methodField);
    }
    methodField.value = 'PUT';
    
    // Populate guest information (read-only)
    document.getElementById('edit_guest_name').value = booking.user?.name || 'N/A';
    document.getElementById('edit_guest_email').value = booking.user?.email || 'N/A';
    
    // Populate editable fields
    const roomSelect = document.getElementById('edit_room_id');
    if (roomSelect && booking.room_id) {
        roomSelect.value = booking.room_id;
        console.log('Set room to:', booking.room_id, 'Current room:', booking.room?.name);
        
        // Update facility time displays
        updateFacilityTimes(booking.room);
    }
    
    document.getElementById('edit_guests').value = booking.guests || 1;
    document.getElementById('edit_special_requests').value = booking.special_requests || '';
    
    // Handle dates - DATE ONLY (facility times are fixed)
    if (booking.check_in) {
        try {
            const checkIn = typeof booking.check_in === 'string' 
                ? new Date(booking.check_in) 
                : booking.check_in;
            
            const year = checkIn.getFullYear();
            const month = String(checkIn.getMonth() + 1).padStart(2, '0');
            const day = String(checkIn.getDate()).padStart(2, '0');
            
            document.getElementById('edit_check_in_date').value = `${year}-${month}-${day}`;
        } catch (e) {
            console.error('Error parsing check-in date:', e);
        }
    }
    
    if (booking.check_out) {
        try {
            const checkOut = typeof booking.check_out === 'string' 
                ? new Date(booking.check_out) 
                : booking.check_out;
            
            const year = checkOut.getFullYear();
            const month = String(checkOut.getMonth() + 1).padStart(2, '0');
            const day = String(checkOut.getDate()).padStart(2, '0');
            
            document.getElementById('edit_check_out_date').value = `${year}-${month}-${day}`;
        } catch (e) {
            console.error('Error parsing check-out date:', e);
        }
    }
    
    // Calculate and display total (after setting all values)
    setTimeout(() => {
        calculateEditTotal();
        updateEditRoomCapacity();
    }, 100);
    
    // Show modal
    modal.classList.remove('hidden');
}

// Update facility time displays
function updateFacilityTimes(room) {
    const checkInTimeDisplay = document.getElementById('edit_check_in_time_display');
    const checkOutTimeDisplay = document.getElementById('edit_check_out_time_display');
    
    if (room && room.check_in_time) {
        const checkInTime = new Date('2000-01-01 ' + room.check_in_time);
        const formattedCheckIn = checkInTime.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
        checkInTimeDisplay.innerHTML = `<i class="fas fa-clock mr-1"></i>Check-in time: ${formattedCheckIn}`;
    } else {
        checkInTimeDisplay.innerHTML = '<i class="fas fa-clock mr-1"></i>Check-in time: 12:00 AM';
    }
    
    if (room && room.check_out_time) {
        const checkOutTime = new Date('2000-01-01 ' + room.check_out_time);
        const formattedCheckOut = checkOutTime.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
        checkOutTimeDisplay.innerHTML = `<i class="fas fa-clock mr-1"></i>Check-out time: ${formattedCheckOut}`;
    } else {
        checkOutTimeDisplay.innerHTML = '<i class="fas fa-clock mr-1"></i>Check-out time: 12:00 AM';
    }
}

// Handle form submission with enhanced error handling
document.getElementById('editBookingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    
    // Get date inputs
    const checkInDateInput = document.getElementById('edit_check_in_date');
    const checkOutDateInput = document.getElementById('edit_check_out_date');
    const roomSelect = document.getElementById('edit_room_id');
    
    // Get selected room to retrieve facility times
    const selectedOption = roomSelect.options[roomSelect.selectedIndex];
    const roomId = roomSelect.value;
    
    if (!roomId) {
        showNotification('Please select a room!', 'error');
        return;
    }
    
    // Get facility times from data attributes
    const checkInTime = selectedOption.dataset.checkInTime || '00:00:00';
    const checkOutTime = selectedOption.dataset.checkOutTime || '00:00:00';
    
    // Combine date with facility time
    const checkInDateTime = checkInDateInput.value + ' ' + checkInTime;
    const checkOutDateTime = checkOutDateInput.value + ' ' + checkOutTime;
    
    // Set hidden inputs with combined datetime
    document.getElementById('edit_check_in').value = checkInDateTime;
    document.getElementById('edit_check_out').value = checkOutDateTime;
    
    const formData = new FormData(form);
    
    // Validate dates
    if (checkInDateInput.value && checkOutDateInput.value) {
        // Parse dates at start of day for proper comparison
        const checkInDate = new Date(checkInDateInput.value);
        checkInDate.setHours(0, 0, 0, 0);
        const checkOutDate = new Date(checkOutDateInput.value);
        checkOutDate.setHours(0, 0, 0, 0);
        
        // Allow same day booking (1 night) or future dates
        if (checkOutDate < checkInDate) {
            showNotification('Check-out date cannot be before check-in date!', 'error');
            return;
        }
    }
    
    // Validate room capacity
    const guestsInput = document.getElementById('edit_guests');
    const maxCapacity = parseInt(guestsInput.max) || 20;
    const currentGuests = parseInt(guestsInput.value) || 0;
    
    if (currentGuests > maxCapacity) {
        showNotification(`Number of guests (${currentGuests}) exceeds room capacity (${maxCapacity})!`, 'error');
        return;
    }
    
    if (currentGuests < 1) {
        showNotification('Number of guests must be at least 1!', 'error');
        return;
    }
    
    // Check for booking conflicts (excluding current booking)
    fetch(`<?php echo e(route('admin.reservations.checkAvailability')); ?>`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            room_id: roomId,
            check_in: checkInDateTime,
            check_out: checkOutDateTime,
            booking_id: currentBookingId
        })
    })
    .then(response => response.json())
    .then(availabilityData => {
        if (!availabilityData.available) {
            showNotification('This facility already has a booking for the selected dates. Please choose different dates.', 'error');
            return;
        }
        
        // Proceed with update if available
        proceedWithUpdate(form, formData, checkInDateTime, checkOutDateTime, roomId, selectedOption);
    })
    .catch(error => {
        console.error('Error checking availability:', error);
        // Proceed anyway if availability check fails
        proceedWithUpdate(form, formData, checkInDateTime, checkOutDateTime, roomId, selectedOption);
    });
});

function proceedWithUpdate(form, formData, checkInDateTime, checkOutDateTime, roomId, selectedOption) {
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Updating...';
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.error('CSRF token not found!');
        showNotification('CSRF token missing. Please refresh the page.', 'error');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        return;
    }
    
    // Calculate total price and add to form data
    const roomPrice = parseFloat(selectedOption.dataset.price) || 0;
    
    // Parse dates at start of day for accurate day counting
    const checkIn = new Date(checkInDateTime);
    checkIn.setHours(0, 0, 0, 0);
    const checkOut = new Date(checkOutDateTime);
    checkOut.setHours(0, 0, 0, 0);
    
    // Calculate nights: same day = 1 night, next day = 1 night, etc.
    const daysDiff = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
    const nights = daysDiff === 0 ? 1 : daysDiff; // Same day counts as 1 night
    
    const total = nights * roomPrice;
    formData.append('total_price', total);
    console.log(`Calculated: ${nights} night${nights > 1 ? 's' : ''} × ₱${roomPrice} = ₱${total}`);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Error response:', text);
                throw new Error(`HTTP error! status: ${response.status}`);
            });
        }
        
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            return response.text().then(text => {
                console.error('Non-JSON response:', text);
                throw new Error('Server returned non-JSON response');
            });
        }
    })
    .then(data => {
        if (data.success) {
            showNotification('Booking updated successfully!', 'success');
            closeEditModal();
            updateBookingRow(currentBookingId, data.booking);
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Update failed');
        }
    })
    .catch(error => {
        console.error('Complete error details:', error);
        showNotification('Failed to update booking: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
}

// Show notification function
function showNotification(message, type = 'success') {
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 z-50 px-6 py-4 rounded-lg text-white transition-all duration-300 ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success' 
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'
                }
            </svg>
            ${message}
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Calculate total for edit form
function calculateEditTotal() {
    const roomSelect = document.getElementById('edit_room_id');
    const checkInInput = document.getElementById('edit_check_in_date');
    const checkOutInput = document.getElementById('edit_check_out_date');
    const totalDisplay = document.getElementById('edit_total_display');
    const nightsDisplay = document.getElementById('edit_nights_display');
    const rateDisplay = document.getElementById('edit_rate_display');
    
    if (roomSelect.value && checkInInput.value && checkOutInput.value) {
        const selectedOption = roomSelect.options[roomSelect.selectedIndex];
        const roomPrice = parseFloat(selectedOption.dataset.price) || 0;
        
        // Parse dates at start of day for accurate day counting
        const checkIn = new Date(checkInInput.value);
        checkIn.setHours(0, 0, 0, 0);
        const checkOut = new Date(checkOutInput.value);
        checkOut.setHours(0, 0, 0, 0);
        
        // Calculate nights: same day = 1 night, next day = 1 night, etc.
        const daysDiff = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        const nights = daysDiff === 0 ? 1 : daysDiff; // Same day counts as 1 night
        
        const total = nights * roomPrice;
        totalDisplay.textContent = `₱${total.toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
        nightsDisplay.textContent = `${nights} night${nights > 1 ? 's' : ''}`;
        rateDisplay.textContent = `₱${roomPrice.toLocaleString('en-US', { minimumFractionDigits: 2 })}/night`;
    }
}

// Update room capacity constraints
function updateEditRoomCapacity() {
    const roomSelect = document.getElementById('edit_room_id');
    const guestsInput = document.getElementById('edit_guests');
    const capacityInfo = document.getElementById('edit_capacity_info');
    const capacityWarning = document.getElementById('edit_capacity_warning');
    
    if (roomSelect.value) {
        const selectedOption = roomSelect.options[roomSelect.selectedIndex];
        const capacity = parseInt(selectedOption.dataset.capacity) || 20;
        
        guestsInput.max = capacity;
        capacityInfo.textContent = `(Maximum: ${capacity} guests)`;
        
        const currentGuests = parseInt(guestsInput.value) || 0;
        if (currentGuests > capacity) {
            capacityWarning.classList.remove('hidden');
            guestsInput.classList.add('border-yellow-500');
        } else {
            capacityWarning.classList.add('hidden');
            guestsInput.classList.remove('border-yellow-500');
        }
    } else {
        guestsInput.max = 20;
        capacityInfo.textContent = '';
        capacityWarning.classList.add('hidden');
        guestsInput.classList.remove('border-yellow-500');
    }
}

// Validate guests on input
function validateEditGuests() {
    const guestsInput = document.getElementById('edit_guests');
    const capacityWarning = document.getElementById('edit_capacity_warning');
    const maxCapacity = parseInt(guestsInput.max) || 20;
    const currentGuests = parseInt(guestsInput.value) || 0;
    
    if (currentGuests > maxCapacity) {
        capacityWarning.classList.remove('hidden');
        guestsInput.classList.add('border-yellow-500');
    } else {
        capacityWarning.classList.add('hidden');
        guestsInput.classList.remove('border-yellow-500');
    }
}

// Update booking row in the table
function updateBookingRow(bookingId, bookingData) {
    const row = document.getElementById(`booking-row-${bookingId}`);
    if (!row) return;
    
    const roomNameEl = row.querySelector('.booking-room-name');
    if (roomNameEl && bookingData.room) {
        roomNameEl.textContent = bookingData.room.name;
    }
    
    const guestsEl = row.querySelector('.booking-guests');
    if (guestsEl) {
        guestsEl.textContent = `${bookingData.guests} guests`;
    }
    
    const datesEl = row.querySelector('.booking-dates');
    if (datesEl && bookingData.check_in && bookingData.check_out) {
        const checkIn = new Date(bookingData.check_in);
        const checkOut = new Date(bookingData.check_out);
        
        datesEl.innerHTML = `
            <div class="text-white">${checkIn.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</div>
            <div class="text-sm text-gray-400">${checkIn.toLocaleDateString('en-US', { weekday: 'long' })} at ${checkIn.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}</div>
            <div class="text-white mt-1">${checkOut.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</div>
            <div class="text-sm text-gray-400">${checkOut.toLocaleDateString('en-US', { weekday: 'long' })} at ${checkOut.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}</div>
        `;
    }
    
    const totalEl = row.querySelector('.booking-total');
    if (totalEl && bookingData.total_price) {
        totalEl.textContent = `₱${parseFloat(bookingData.total_price).toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
    }
    
    const nightsEl = row.querySelector('.booking-nights');
    if (nightsEl && bookingData.check_in && bookingData.check_out) {
        const checkIn = new Date(bookingData.check_in);
        checkIn.setHours(0, 0, 0, 0);
        const checkOut = new Date(bookingData.check_out);
        checkOut.setHours(0, 0, 0, 0);
        
        // Calculate nights: same day = 1 night, next day = 1 night, etc.
        const daysDiff = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        const nights = daysDiff === 0 ? 1 : daysDiff; // Same day counts as 1 night
        nightsEl.textContent = `${nights} night${nights > 1 ? 's' : ''}`;
    }
    
    row.classList.add('bg-green-900', 'bg-opacity-30');
    setTimeout(() => {
        row.classList.remove('bg-green-900', 'bg-opacity-30');
    }, 2000);
}

// Add event listeners for real-time calculation and validation
document.getElementById('edit_room_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const roomId = this.value;
    
    if (roomId && selectedOption) {
        // Get facility times from data attributes instead of fetching
        const room = {
            check_in_time: selectedOption.dataset.checkInTime || '00:00:00',
            check_out_time: selectedOption.dataset.checkOutTime || '00:00:00'
        };
        
        updateFacilityTimes(room);
        updateEditRoomCapacity();
        calculateEditTotal();
    }
});
document.getElementById('edit_check_in_date').addEventListener('change', calculateEditTotal);
document.getElementById('edit_check_out_date').addEventListener('change', calculateEditTotal);
document.getElementById('edit_guests').addEventListener('input', validateEditGuests);

function closeEditModal() {
    document.getElementById('editBookingModal').classList.add('hidden');
}

// Status update functions
function updateStatus(bookingId) {
    const modal = document.getElementById('statusModal');
    const form = document.getElementById('statusForm');
    form.action = `/admin/reservations/${bookingId}/status`;
    
    // Reset checkout notice visibility
    document.getElementById('checkoutNotice').classList.add('hidden');
    document.getElementById('updateStatusBtn').textContent = 'Update';
    
    modal.classList.remove('hidden');
}

// Handle status change to show checkout confirmation
function handleStatusChange(selectedStatus) {
    const checkoutNotice = document.getElementById('checkoutNotice');
    const updateBtn = document.getElementById('updateStatusBtn');
    
    if (selectedStatus === 'checked_out') {
        checkoutNotice.classList.remove('hidden');
        updateBtn.textContent = 'Confirm Checkout & Deploy Housekeeping';
        updateBtn.classList.remove('bg-blue-600', 'hover:bg-blue-500');
        updateBtn.classList.add('bg-purple-600', 'hover:bg-purple-500');
    } else {
        checkoutNotice.classList.add('hidden');
        updateBtn.textContent = 'Update';
        updateBtn.classList.remove('bg-purple-600', 'hover:bg-purple-500');
        updateBtn.classList.add('bg-blue-600', 'hover:bg-blue-500');
    }
}

function closeModal() {
    const modal = document.getElementById('statusModal');
    modal.classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\admin\reservations\index.blade.php ENDPATH**/ ?>