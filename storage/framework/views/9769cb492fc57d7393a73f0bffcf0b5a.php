<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 lg:px-8 py-8">
    <!-- Page Title -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Manage Bookings</h1>
            <p class="text-gray-400 mt-2">View and manage all resort bookings</p>
        </div>
        <?php if(in_array(auth()->user()->role, ['admin', 'manager'])): ?>
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
                        <a href="<?php echo e(route('admin.bookings.createFromRoom', $room)); ?>" 
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
            <a href="<?php echo e(route('admin.bookings.create')); ?>" 
               class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors duration-200 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Manual Booking
            </a>
        </div>
        <?php endif; ?>
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

    <!-- Filters -->
    <div class="bg-gray-800 rounded-lg p-6 mb-8">
        <form action="<?php echo e(route('admin.bookings')); ?>" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Search Guest</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                       placeholder="Name or email..."
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                    <option value="">All Statuses</option>
                    <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($status); ?>" <?php echo e(request('status') === $status ? 'selected' : ''); ?>>
                            <?php echo e(ucfirst(str_replace('_', ' ', $status))); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <!-- Date Range -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">From Date</label>
                <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">To Date</label>
                <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
            </div>

            <!-- Filter Buttons -->
            <div class="md:col-span-2 lg:col-span-4 flex justify-end space-x-4">
                <a href="<?php echo e(route('admin.bookings')); ?>" 
                   class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-500 transition-all">
                    Reset
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition-all">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Bookings Table -->
    <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-900 text-white">
                    <tr>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Guest</th>
                        <th class="px-6 py-4">Room</th>
                        <th class="px-6 py-4">Dates & Times</th>
                        <th class="px-6 py-4">Total Amount</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="text-gray-300 hover:bg-gray-700/50">
                        <td class="px-6 py-4">
                            #<?php echo e($booking->id); ?>

                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white"><?php echo e($booking->user->name); ?></div>
                            <div class="text-sm text-gray-400"><?php echo e($booking->user->email); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white"><?php echo e($booking->room->name); ?></div>
                            <div class="text-sm text-gray-400"><?php echo e($booking->guests); ?> guests</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white"><?php echo e($booking->check_in->format('M d, Y')); ?></div>
                            <div class="text-sm text-gray-400"><?php echo e($booking->check_in->format('l \a\t g:i A')); ?></div>
                            <div class="text-white mt-1"><?php echo e($booking->check_out->format('M d, Y')); ?></div>
                            <div class="text-sm text-gray-400"><?php echo e($booking->check_out->format('l \a\t g:i A')); ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-green-400 font-bold text-lg"><?php echo e($booking->formatted_total_price); ?></div>
                            <div class="text-sm text-gray-400">
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
                                <a href="<?php echo e(route('admin.bookings.show', $booking)); ?>" 
                                   class="text-blue-400 hover:text-blue-300 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <button type="button"
                                        onclick="updateStatus('<?php echo e($booking->id); ?>')"
                                        class="text-green-400 hover:text-green-300 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                            No bookings found.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-3 bg-gray-900">
            <?php echo e($bookings->links()); ?>

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
                                <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($status); ?>">
                                        <?php echo e(ucfirst(str_replace('_', ' ', $status))); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
function updateStatus(bookingId) {
    const modal = document.getElementById('statusModal');
    const form = document.getElementById('statusForm');
    form.action = `/admin/bookings/${bookingId}/status`;
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
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-white mb-2">Search Guest</label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                           placeholder="Name or email..."
                           class="w-full px-4 py-2 bg-gray-900 border border-green-700 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500">
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-white mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 bg-gray-900 border border-green-700 rounded-lg text-white focus:border-green-500 focus:ring-1 focus:ring-green-500">
                        <option value="">All Statuses</option>
                        <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($status); ?>" <?php echo e(request('status') === $status ? 'selected' : ''); ?>>
                                <?php echo e(ucfirst($status)); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-white mb-2">From Date</label>
                    <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>"
                           class="w-full px-4 py-2 bg-gray-900 border border-green-700 rounded-lg text-white focus:border-green-500 focus:ring-1 focus:ring-green-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-white mb-2">To Date</label>
                    <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>"
                           class="w-full px-4 py-2 bg-gray-900 border border-green-700 rounded-lg text-white focus:border-green-500 focus:ring-1 focus:ring-green-500">
                </div>

                <!-- Buttons -->
                <div class="md:col-span-2 lg:col-span-4 flex justify-end space-x-4">
                    <a href="<?php echo e(route('admin.bookings')); ?>" 
                       class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-all duration-300">
                        Reset
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-700 text-white rounded-lg hover:bg-green-600 transition-all duration-300">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Bookings Table -->
        <div class="bg-green-800 bg-opacity-50 backdrop-blur-sm rounded-lg border border-green-700 shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-green-900 bg-opacity-50 text-white">
                        <tr>
                            <th class="px-6 py-4">Booking ID</th>
                            <th class="px-6 py-4">Guest</th>
                            <th class="px-6 py-4">Room</th>
                            <th class="px-6 py-4">Dates & Times</th>
                            <th class="px-6 py-4">Total Amount</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-green-700">
                        <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-green-900/30 transition-colors duration-200">
                            <td class="px-6 py-4 text-white">
                                #<?php echo e($booking->id); ?>

                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white"><?php echo e($booking->user->name); ?></div>
                                <div class="text-sm text-green-300"><?php echo e($booking->user->email); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white"><?php echo e($booking->room->name); ?></div>
                                <div class="text-sm text-green-300"><?php echo e($booking->guests); ?> guests</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white"><?php echo e($booking->check_in->format('M d, Y')); ?></div>
                                <div class="text-sm text-green-300"><?php echo e($booking->check_in->format('l \a\t g:i A')); ?></div>
                                <div class="text-white mt-1"><?php echo e($booking->check_out->format('M d, Y')); ?></div>
                                <div class="text-sm text-green-300"><?php echo e($booking->check_out->format('l \a\t g:i A')); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-green-400 font-bold text-lg"><?php echo e($booking->formatted_total_price); ?></div>
                                <div class="text-sm text-green-300">
                                    <?php echo e($booking->check_in->diffInDays($booking->check_out)); ?> night(s)
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    <?php if($booking->status === 'confirmed'): ?> bg-green-100 text-green-800
                                    <?php elseif($booking->status === 'pending'): ?> bg-yellow-100 text-yellow-800
                                    <?php elseif($booking->status === 'cancelled'): ?> bg-red-100 text-red-800
                                    <?php elseif($booking->status === 'checked_in'): ?> bg-blue-100 text-blue-800
                                    <?php elseif($booking->status === 'checked_out'): ?> bg-gray-100 text-gray-800
                                    <?php else: ?> bg-gray-100 text-gray-800
                                    <?php endif; ?>">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $booking->status))); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <!-- View Details -->
                                    <a href="<?php echo e(route('admin.bookings.show', $booking)); ?>" 
                                       class="text-green-400 hover:text-green-300 transition-colors duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    <!-- Status Update -->
                                    <button type="button"
                                            onclick="updateStatus('<?php echo e($booking->id); ?>')"
                                            class="text-blue-400 hover:text-blue-300 transition-colors duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-white">
                                No bookings found.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-3 bg-green-900 bg-opacity-50">
                <?php echo e($bookings->links()); ?>

            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-900 rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Update Booking Status</h3>
                    <form id="statusForm" method="POST" class="space-y-4">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <div>
                            <label for="status" class="block text-sm font-medium text-white mb-2">Status</label>
                            <select name="status" id="status"
                                    class="w-full px-4 py-2 bg-gray-800 border border-green-700 rounded-lg text-white focus:border-green-500 focus:ring-1 focus:ring-green-500">
                                <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($status); ?>"><?php echo e(ucfirst(str_replace('_', ' ', $status))); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeModal()"
                                    class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-600 transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-green-700 text-white rounded hover:bg-green-600 transition-colors">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        function updateStatus(bookingId) {
            const modal = document.getElementById('statusModal');
            const form = document.getElementById('statusForm');
            form.action = `/admin/bookings/${bookingId}/status`;
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
                                    <?php endif; ?>">
                                    <?php echo e(ucfirst($booking->status)); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-4">
                                    <a href="<?php echo e(route('admin.bookings.show', $booking)); ?>" 
                                       class="inline-flex items-center px-4 py-2 bg-green-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        View Details
                                    </a>
                                    <?php if($booking->status !== 'cancelled' && $booking->status !== 'completed'): ?>
                                    <form action="<?php echo e(route('admin.bookings.status', $booking)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <select name="status" onchange="this.form.submit()"
                                                class="bg-gray-900 border border-green-700 text-white rounded px-3 py-1 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500">
                                            <option value="">Change Status</option>
                                            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($status !== $booking->status): ?>
                                                <option value="<?php echo e($status); ?>">
                                                    <?php echo e(ucfirst($status)); ?>

                                                </option>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-green-300">
                                No bookings found matching your criteria.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 bg-green-900 bg-opacity-50 border-t border-green-700">
                <?php echo e($bookings->links()); ?>

            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal)): ?>
<?php $attributes = $__attributesOriginal; ?>
<?php unset($__attributesOriginal); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal)): ?>
<?php $component = $__componentOriginal; ?>
<?php unset($__componentOriginal); ?>
<?php endif; ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\admin\bookings\index.blade.php ENDPATH**/ ?>