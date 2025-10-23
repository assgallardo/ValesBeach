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
                <!-- Quick Room Selection for Booking -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" 
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
                         class="absolute right-0 mt-2 w-64 bg-gray-800 rounded-md shadow-lg py-1 z-50 max-h-64 overflow-y-auto"
                         style="display: none;">
                        <div class="px-4 py-2 text-sm text-gray-300 border-b border-gray-700">Select Room to Book:</div>
                        <?php
                            $availableRooms = \App\Models\Room::where('is_available', true)->get();
                        ?>
                        <?php $__currentLoopData = $availableRooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('manager.bookings.createFromRoom', $room)); ?>" 
                               class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">
                                <div class="font-medium"><?php echo e($room->name); ?></div>
                                <div class="text-xs text-gray-400">₱<?php echo e(number_format((float)$room->price, 2)); ?>/night • <?php echo e($room->capacity); ?> guests</div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($availableRooms->isEmpty()): ?>
                            <div class="px-4 py-2 text-sm text-gray-400">No available rooms</div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Original Manual Booking -->
                <a href="<?php echo e(route('manager.bookings.create')); ?>" 
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
        <form action="<?php echo e(route('manager.bookings.index')); ?>" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
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
                    <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                    <option value="confirmed" <?php echo e(request('status') === 'confirmed' ? 'selected' : ''); ?>>Confirmed</option>
                    <option value="checked_in" <?php echo e(request('status') === 'checked_in' ? 'selected' : ''); ?>>Checked In</option>
                    <option value="completed" <?php echo e(request('status') === 'completed' ? 'selected' : ''); ?>>Completed</option>
                    <option value="cancelled" <?php echo e(request('status') === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                </select>
            </div>

            <!-- Room Filter -->
            <div>
                <label for="room_id" class="block text-sm font-medium text-gray-300 mb-2">Room</label>
                <select name="room_id" id="room_id" 
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Rooms</option>
                    <?php
                        $rooms = \App\Models\Room::all();
                    ?>
                    <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($room->id); ?>" <?php echo e(request('room_id') == $room->id ? 'selected' : ''); ?>>
                            <?php echo e($room->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                <a href="<?php echo e(route('manager.bookings.index')); ?>" 
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
                    <p class="text-2xl font-bold"><?php echo e($bookings->total()); ?></p>
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
                    <p class="text-2xl font-bold"><?php echo e($bookings->where('status', 'confirmed')->count() + $bookings->where('status', 'checked_in')->count()); ?></p>
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
                    <p class="text-2xl font-bold"><?php echo e($bookings->where('status', 'completed')->count()); ?></p>
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
                    <p class="text-2xl font-bold"><?php echo e($bookings->where('status', 'cancelled')->count()); ?></p>
                </div>
                <div class="bg-red-500 bg-opacity-50 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservations Table -->
    <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-900">
                    <tr>
                        <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Guest</th>
                        <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Room</th>
                        <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Dates</th>
                        <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-700 transition-colors" id="booking-row-<?php echo e($booking->id); ?>">
                        <td class="px-6 py-4">
                            <div class="text-white font-medium">#<?php echo e($booking->id); ?></div>
                            <div class="text-xs text-gray-400"><?php echo e($booking->created_at->format('M d, Y')); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white"><?php echo e($booking->user->name); ?></div>
                            <div class="text-sm text-gray-400"><?php echo e($booking->user->email); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white booking-room-name"><?php echo e($booking->room->name); ?></div>
                            <div class="text-sm text-gray-400 booking-guests"><?php echo e($booking->guests); ?> guests</div>
                        </td>
                        <td class="px-6 py-4 booking-dates">
                            <div class="text-white"><?php echo e($booking->check_in->format('M d, Y')); ?></div>
                            <div class="text-sm text-gray-400"><?php echo e($booking->check_in->format('l \a\t g:i A')); ?></div>
                            <div class="text-white mt-1"><?php echo e($booking->check_out->format('M d, Y')); ?></div>
                            <div class="text-sm text-gray-400"><?php echo e($booking->check_out->format('l \a\t g:i A')); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-green-400 font-bold text-lg booking-total"><?php echo e($booking->formatted_total_price); ?></div>
                            <div class="text-sm text-gray-400 booking-nights">
                                <?php echo e($booking->check_in->diffInDays($booking->check_out)); ?> night(s)
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'px-3 py-1 rounded-full text-xs font-medium',
                                'bg-yellow-100 text-yellow-800' => $booking->status === 'pending',
                                'bg-green-100 text-green-800' => $booking->status === 'confirmed',
                                'bg-blue-100 text-blue-800' => $booking->status === 'checked_in',
                                'bg-gray-100 text-gray-800' => $booking->status === 'checked_out',
                                'bg-red-100 text-red-800' => $booking->status === 'cancelled',
                                'bg-purple-100 text-purple-800' => $booking->status === 'completed',
                            ]); ?>">
                                <?php echo e(ucfirst(str_replace('_', ' ', $booking->status))); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <a href="<?php echo e(route('manager.bookings.show', $booking)); ?>" 
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
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                            No reservations found.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-3 bg-gray-900">
            <?php echo e($bookings->appends(request()->query())->links()); ?>

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
                                    <option value="<?php echo e($room->id); ?>" data-price="<?php echo e($room->price); ?>" data-capacity="<?php echo e($room->capacity); ?>">
                                        <?php echo e($room->name); ?> - ₱<?php echo e(number_format($room->price, 2)); ?>/night (Max: <?php echo e($room->capacity); ?> guests)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Booking Dates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="edit_check_in" class="block text-sm font-medium text-gray-300 mb-2">Check-in Date & Time</label>
                                <input type="datetime-local" name="check_in" id="edit_check_in" required
                                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="edit_check_out" class="block text-sm font-medium text-gray-300 mb-2">Check-out Date & Time</label>
                                <input type="datetime-local" name="check_out" id="edit_check_out" required
                                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                            <select name="status" id="status"
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="checked_in">Checked In</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeModal()"
                                    class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-500 transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
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
    form.action = `<?php echo e(url('manager/bookings')); ?>/${booking.id}`;
    
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
    }
    
    document.getElementById('edit_guests').value = booking.guests || 1;
    document.getElementById('edit_special_requests').value = booking.special_requests || '';
    
    // Handle dates - convert to local timezone
    if (booking.check_in) {
        try {
            // Handle both Date objects and string formats
            const checkIn = typeof booking.check_in === 'string' 
                ? new Date(booking.check_in) 
                : booking.check_in;
            
            // Format for datetime-local input (YYYY-MM-DDTHH:MM)
            const year = checkIn.getFullYear();
            const month = String(checkIn.getMonth() + 1).padStart(2, '0');
            const day = String(checkIn.getDate()).padStart(2, '0');
            const hours = String(checkIn.getHours()).padStart(2, '0');
            const minutes = String(checkIn.getMinutes()).padStart(2, '0');
            
            document.getElementById('edit_check_in').value = `${year}-${month}-${day}T${hours}:${minutes}`;
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
            const hours = String(checkOut.getHours()).padStart(2, '0');
            const minutes = String(checkOut.getMinutes()).padStart(2, '0');
            
            document.getElementById('edit_check_out').value = `${year}-${month}-${day}T${hours}:${minutes}`;
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

// Handle form submission with enhanced error handling
document.getElementById('editBookingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    
    // Validate dates
    const checkInInput = document.getElementById('edit_check_in');
    const checkOutInput = document.getElementById('edit_check_out');
    
    if (checkInInput.value && checkOutInput.value) {
        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);
        
        if (checkOut <= checkIn) {
            showNotification('Check-out date must be after check-in date!', 'error');
            return;
        }
        
        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        if (nights < 1) {
            showNotification('Booking must be for at least 1 night!', 'error');
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
    const roomSelect = document.getElementById('edit_room_id');
    
    if (roomSelect.value && checkInInput.value && checkOutInput.value) {
        const selectedOption = roomSelect.options[roomSelect.selectedIndex];
        const roomPrice = parseFloat(selectedOption.dataset.price) || 0;
        
        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);
        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        
        if (nights > 0) {
            const total = nights * roomPrice;
            formData.append('total_price', total);
            console.log(`Calculated: ${nights} nights × ₱${roomPrice} = ₱${total}`);
        }
    }
    
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
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        
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
        console.log('Success response:', data);
        
        if (data.success) {
            showNotification('Booking updated successfully!', 'success');
            closeEditModal();
            
            // Update the row in the table instead of reloading
            updateBookingRow(currentBookingId, data.booking);
            
            // Or reload the page to reflect changes
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
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
});

// Show notification function
function showNotification(message, type = 'success') {
    // Remove any existing notifications
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
    
    // Remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Calculate total for edit form
function calculateEditTotal() {
    const roomSelect = document.getElementById('edit_room_id');
    const checkInInput = document.getElementById('edit_check_in');
    const checkOutInput = document.getElementById('edit_check_out');
    const totalDisplay = document.getElementById('edit_total_display');
    const nightsDisplay = document.getElementById('edit_nights_display');
    const rateDisplay = document.getElementById('edit_rate_display');
    
    if (roomSelect.value && checkInInput.value && checkOutInput.value) {
        const selectedOption = roomSelect.options[roomSelect.selectedIndex];
        const roomPrice = parseFloat(selectedOption.dataset.price) || 0;
        
        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);
        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        
        if (nights > 0) {
            const total = nights * roomPrice;
            totalDisplay.textContent = `₱${total.toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
            nightsDisplay.textContent = `${nights} night${nights > 1 ? 's' : ''}`;
            rateDisplay.textContent = `₱${roomPrice.toLocaleString('en-US', { minimumFractionDigits: 2 })}/night`;
        }
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
        
        // Update max attribute
        guestsInput.max = capacity;
        
        // Update capacity info text
        capacityInfo.textContent = `(Maximum: ${capacity} guests)`;
        
        // Check if current value exceeds capacity
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
    
    // Update room name and guests
    const roomNameEl = row.querySelector('.booking-room-name');
    if (roomNameEl && bookingData.room) {
        roomNameEl.textContent = bookingData.room.name;
    }
    
    const guestsEl = row.querySelector('.booking-guests');
    if (guestsEl) {
        guestsEl.textContent = `${bookingData.guests} guests`;
    }
    
    // Update dates
    const datesEl = row.querySelector('.booking-dates');
    if (datesEl && bookingData.check_in && bookingData.check_out) {
        const checkIn = new Date(bookingData.check_in);
        const checkOut = new Date(bookingData.check_out);
        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        
        datesEl.innerHTML = `
            <div class="text-white">${checkIn.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</div>
            <div class="text-sm text-gray-400">${checkIn.toLocaleDateString('en-US', { weekday: 'long' })} at ${checkIn.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}</div>
            <div class="text-white mt-1">${checkOut.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</div>
            <div class="text-sm text-gray-400">${checkOut.toLocaleDateString('en-US', { weekday: 'long' })} at ${checkOut.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}</div>
        `;
    }
    
    // Update total price
    const totalEl = row.querySelector('.booking-total');
    if (totalEl && bookingData.total_price) {
        totalEl.textContent = `₱${parseFloat(bookingData.total_price).toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
    }
    
    const nightsEl = row.querySelector('.booking-nights');
    if (nightsEl && bookingData.check_in && bookingData.check_out) {
        const checkIn = new Date(bookingData.check_in);
        const checkOut = new Date(bookingData.check_out);
        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        nightsEl.textContent = `${nights} night${nights > 1 ? 's' : ''}`;
    }
    
    // Highlight the updated row
    row.classList.add('bg-green-900', 'bg-opacity-30');
    setTimeout(() => {
        row.classList.remove('bg-green-900', 'bg-opacity-30');
    }, 2000);
}

// Add event listeners for real-time calculation and validation
document.getElementById('edit_room_id').addEventListener('change', function() {
    updateEditRoomCapacity();
    calculateEditTotal();
});
document.getElementById('edit_check_in').addEventListener('change', calculateEditTotal);
document.getElementById('edit_check_out').addEventListener('change', calculateEditTotal);
document.getElementById('edit_guests').addEventListener('input', validateEditGuests);

function closeEditModal() {
    document.getElementById('editBookingModal').classList.add('hidden');
}

// Status update functions
function updateStatus(bookingId) {
    const modal = document.getElementById('statusModal');
    const form = document.getElementById('statusForm');
    form.action = `/manager/bookings/${bookingId}/status`;
    modal.classList.remove('hidden');
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

<?php echo $__env->make('layouts.manager', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\valesbeachresort\ValesBeach\resources\views/manager/bookings/index.blade.php ENDPATH**/ ?>