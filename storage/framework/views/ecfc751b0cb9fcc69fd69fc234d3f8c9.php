<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 lg:px-16 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-white">Rooms & Facilities</h2>
        <a href="<?php echo e(route('admin.rooms.create')); ?>" 
           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
            Add New Facility
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="bg-gray-800 rounded-lg p-6 mb-6">
        <form action="<?php echo e(route('admin.rooms.index')); ?>" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-gray-300 mb-2">Search</label>
                <input type="text" 
                       name="search" 
                       value="<?php echo e(request('search')); ?>"
                       placeholder="Search facility..."
                       class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <!-- Type Filter -->
            <div>
                <label class="block text-gray-300 mb-2">Type</label>
                <select name="category" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">All Types</option>
                    <option value="Rooms" <?php echo e(request('category') == 'Rooms' ? 'selected' : ''); ?>>Rooms</option>
                    <option value="Cottages" <?php echo e(request('category') == 'Cottages' ? 'selected' : ''); ?>>Cottages</option>
                    <option value="Event and Dining" <?php echo e(request('category') == 'Event and Dining' ? 'selected' : ''); ?>>Event and Dining</option>
                </select>
            </div>

            <!-- Price Range -->
            <div>
                <label class="block text-gray-300 mb-2">Price Range</label>
                <div class="flex space-x-2">
                    <input type="number" 
                           name="min_price" 
                           value="<?php echo e(request('min_price')); ?>"
                           placeholder="Min"
                           class="w-1/2 bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <input type="number" 
                           name="max_price" 
                           value="<?php echo e(request('max_price')); ?>"
                           placeholder="Max"
                           class="w-1/2 bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
            </div>

            <!-- Availability Filter -->
            <div>
                <label class="block text-gray-300 mb-2">Status</label>
                <select name="is_available" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <option value="">All Status</option>
                    <option value="1" <?php echo e(request('is_available') === '1' ? 'selected' : ''); ?>>Available</option>
                    <option value="0" <?php echo e(request('is_available') === '0' ? 'selected' : ''); ?>>Unavailable</option>
                </select>
            </div>

            <!-- Filter Actions -->
            <div class="md:col-span-2 lg:col-span-4 flex justify-end space-x-2">
                <button type="submit" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Apply Filters
                </button>
                <a href="<?php echo e(route('admin.rooms.index')); ?>" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Results Count -->
    <div class="text-gray-300 mb-4">
        Found <?php echo e($rooms->total()); ?> rooms
    </div>

    <!-- Rooms Table -->
    <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-700 text-white">
                    <tr>
                        <th class="px-6 py-4">Room Name</th>
                        <th class="px-6 py-4">Key #</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Price</th>
                        <th class="px-6 py-4">Capacity</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-600">
                    <?php $__empty_0 = true; $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <tr class="text-gray-300 hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <a href="<?php echo e(route('admin.rooms.show', $room)); ?>" 
                                   class="text-blue-400 hover:text-blue-300 hover:underline">
                                    <?php echo e($room->name); ?>

                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <?php if($room->key_number): ?>
                                    <span class="px-2 py-1 bg-gray-700 rounded font-mono text-sm"><?php echo e($room->key_number); ?></span>
                                <?php else: ?>
                                    <span class="text-gray-500">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4"><?php echo e($room->category ?? 'Rooms'); ?></td>
                            <td class="px-6 py-4">₱<?php echo e(number_format($room->price, 2)); ?></td>
                            <td class="px-6 py-4"><?php echo e($room->capacity); ?> guests</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    <?php echo e($room->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                                    <?php echo e($room->is_available ? 'Available' : 'Unavailable'); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-3">
                                    <?php if($room->is_available): ?>
                                    <a href="<?php echo e(route('admin.reservations.createFromRoom', $room)); ?>" 
                                       class="text-green-400 hover:text-green-300 font-medium">
                                        Book
                                    </a>
                                    <?php endif; ?>
                                    <a href="<?php echo e(route('admin.rooms.edit', $room)); ?>" 
                                       class="text-blue-400 hover:text-blue-300">
                                        Edit
                                    </a>
                                    <button onclick="toggleAvailability(<?php echo e($room->id); ?>, <?php echo e($room->is_available); ?>)"
                                            class="text-yellow-400 hover:text-yellow-300">
                                        <?php echo e($room->is_available ? 'Unavailable' : 'Available'); ?>

                                    </button>
                                    <form action="<?php echo e(route('admin.rooms.destroy', $room)); ?>" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this room?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-400 hover:text-red-300">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-400">
                                No rooms found. Add your first room to get started.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        <?php echo e($rooms->links()); ?>

    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function toggleAvailability(roomId, currentStatus) {
    if (confirm('Are you sure you want to change the room availability?')) {
        fetch(`/admin/rooms/${roomId}/toggle-availability`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ is_available: !currentStatus })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\sethy\ValesBeach\resources\views\admin\rooms\index.blade.php ENDPATH**/ ?>