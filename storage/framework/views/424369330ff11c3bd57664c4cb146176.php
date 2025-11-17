<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Vales Beach Resort</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
</head>
<body class="min-h-screen bg-gray-900 relative overflow-x-hidden" style="font-family: 'Poppins', sans-serif;">
    <!-- Background decorative blur elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute w-96 h-96 bg-green-800 opacity-30 rounded-full blur-3xl -top-48 -left-48"></div>
        <div class="absolute w-80 h-80 bg-green-700 opacity-20 rounded-full blur-3xl top-1/3 right-1/4"></div>
        <div class="absolute w-72 h-72 bg-green-800 opacity-25 rounded-full blur-3xl bottom-1/4 left-1/3"></div>
    </div>

    <!-- Header -->
    <header class="relative z-10 bg-green-900 shadow-xl">
        <div class="container mx-auto px-4 lg:px-16">
            <div class="flex items-center justify-between h-32">
                <!-- Resort Name -->
                <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-green-50">
                    VALES BEACH RESORT
                </h1>
                
                <!-- Navigation -->
                <div class="flex items-center space-x-6 lg:space-x-8">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="text-green-50 text-lg lg:text-xl font-light hover:text-green-200 transition-colors duration-200">
                        Dashboard
                    </a>
                    <a href="/" class="text-green-50 text-lg lg:text-xl font-light hover:text-green-200 transition-colors duration-200">
                        Home
                    </a>
                    
                    <!-- User Profile & Logout -->
                    <div class="flex items-center space-x-4">
                        <span class="text-green-50 text-sm"><?php echo e(auth()->user()->name); ?></span>
                        <form action="<?php echo e(route('logout')); ?>" method="POST" class="inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="text-green-50 text-sm hover:text-green-200 transition-colors duration-200">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="relative z-10 py-8 lg:py-16">
        <div class="container mx-auto px-4 lg:px-16">
            <!-- Page Title -->
            <div class="text-center mb-8 lg:mb-12">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50 mb-4">
                    User Management System
                </h2>
                <p class="text-green-50 opacity-80 text-lg">
                    Manage user accounts, permissions, and access controls
                </p>
            </div>

            <!-- Action Bar -->
            <div class="bg-gray-800 rounded-lg p-4 lg:p-6 mb-8 shadow-lg">
                <form action="<?php echo e(route('admin.users')); ?>" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search Bar -->
                        <div class="md:col-span-2">
                            <div class="relative">
                                <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                                    placeholder="Search by name or email..." 
                                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <svg class="absolute right-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Role Filter -->
                        <div>
                            <select name="role" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">All Roles</option>
                                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($role); ?>" <?php echo e(request('role') === $role ? 'selected' : ''); ?>>
                                        <?php echo e(ucfirst($role)); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <select name="status" class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">All Statuses</option>
                                <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($status); ?>" <?php echo e(request('status') === $status ? 'selected' : ''); ?>>
                                        <?php echo e(ucfirst($status)); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <!-- Filter/Reset Buttons -->
                        <div class="flex gap-4">
                            <button type="submit" 
                                class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors duration-200 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                                Apply Filters
                            </button>
                            <a href="<?php echo e(route('admin.users')); ?>" 
                               class="px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors duration-200">
                                Reset
                            </a>
                        </div>

                        <!-- Add User Button -->
                        <button type="button" id="addUserBtn" 
                            class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add New User
                        </button>
                    </div>
                </form>
            </div>

            <!-- Success/Error Messages -->
            <div id="messageContainer" class="hidden mb-4">
                <div id="messageContent" class="p-4 rounded-lg text-white"></div>
            </div>

            <!-- Users Table -->
            <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <!-- Table Header -->
                <div class="px-6 py-4 bg-gray-700 border-b border-gray-600">
                    <h3 class="text-xl font-semibold text-white">All Users</h3>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-700">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">User</th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Joined</th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody" class="divide-y divide-gray-600">
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-700 transition-colors duration-200" data-user-id="<?php echo e($user->id); ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-medium">
                                            <?php echo e(strtoupper(substr($user->name, 0, 2))); ?>

                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-white"><?php echo e($user->name); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300"><?php echo e($user->email); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        <?php echo e($user->role === 'admin' ? 'bg-red-600 text-white' : 
                                           ($user->role === 'manager' ? 'bg-blue-600 text-white' : 'bg-gray-600 text-white')); ?>">
                                        <?php echo e(ucfirst($user->role ?? 'staff')); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        <?php echo e(($user->status ?? 'active') === 'active' ? 'bg-green-600 text-white' :
                                           (($user->status ?? 'active') === 'blocked' ? 'bg-red-600 text-white' : 'bg-yellow-600 text-white')); ?>">
                                        <?php echo e(ucfirst($user->status ?? 'active')); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300"><?php echo e($user->created_at->format('M d, Y')); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button onclick="editUser(<?php echo e($user->id); ?>)" class="text-blue-400 hover:text-blue-300 transition-colors duration-200">Edit</button>
                                    <?php if($user->id !== auth()->id()): ?>
                                        <?php if(($user->status ?? 'active') === 'blocked'): ?>
                                            <button onclick="unblockUser(<?php echo e($user->id); ?>)" class="text-green-400 hover:text-green-300 transition-colors duration-200">Unblock</button>
                                        <?php else: ?>
                                            <button onclick="toggleUserStatus(<?php echo e($user->id); ?>)"
                                                class="<?php echo e(($user->status ?? 'active') === 'active' ? 'text-yellow-400 hover:text-yellow-300' : 'text-green-400 hover:text-green-300'); ?> transition-colors duration-200">
                                                <?php echo e(($user->status ?? 'active') === 'active' ? 'Deactivate' : 'Activate'); ?>

                                            </button>
                                            <button onclick="blockUser(<?php echo e($user->id); ?>)" class="text-orange-400 hover:text-orange-300 transition-colors duration-200">Block</button>
                                        <?php endif; ?>
                                        <button onclick="deleteUser(<?php echo e($user->id); ?>)" class="text-red-400 hover:text-red-300 transition-colors duration-200">Delete</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-700 border-t border-gray-600">
                    <?php echo e($users->links()); ?>

                </div>
            </div>
        </div>
    </main>

    <!-- Add/Edit User Modal -->
    <div id="userModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <div class="relative bg-gray-800 rounded-lg max-w-md w-full p-6">
                <h3 id="modalTitle" class="text-lg font-semibold text-white mb-4">Add New User</h3>
                
                <form id="userForm" novalidate>
                    <input type="hidden" id="userId" name="userId">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Full Name</label>
                            <input type="text" id="userName" name="name" required 
                                autocomplete="name"
                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                            <input type="email" id="userEmail" name="email" required
                                autocomplete="email"
                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                            <input type="password" id="userPassword" name="password"
                                autocomplete="new-password"
                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-400 mt-1">Leave blank to keep current password (for edit)</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Confirm Password</label>
                            <input type="password" id="userPasswordConfirm" name="password_confirmation"
                                autocomplete="new-password"
                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Role</label>
                            <select id="userRole" name="role" required
                                autocomplete="organization-title"
                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500">
                                <option value="guest">Guest</option>
                                <option value="staff">Staff</option>
                                <option value="manager">Manager</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Save User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Set up CSRF token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Add event listeners
        document.getElementById('addUserBtn').addEventListener('click', openAddModal);
        document.getElementById('userForm').addEventListener('submit', saveUser);
        
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New User';
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('userModal').classList.remove('hidden');
        }
        
        function editUser(userId) {
            fetch(`/admin/users/${userId}`, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(user => {
                document.getElementById('modalTitle').textContent = 'Edit User';
                document.getElementById('userId').value = user.id;
                document.getElementById('userName').value = user.name;
                document.getElementById('userEmail').value = user.email;
                document.getElementById('userRole').value = user.role || 'staff';
                document.getElementById('userPassword').value = '';
                document.getElementById('userPasswordConfirm').value = '';
                document.getElementById('userModal').classList.remove('hidden');
            })
            .catch(error => showMessage('Error loading user data', 'error'));
        }
        
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                fetch(`/admin/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(data.message, 'success');
                        location.reload(); // Reload page to update table
                    } else {
                        showMessage(data.message, 'error');
                    }
                })
                .catch(error => showMessage('Error deleting user', 'error'));
            }
        }

        function toggleUserStatus(userId) {
            fetch(`/admin/users/${userId}/status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    location.reload(); // Reload page to update table
                } else {
                    showMessage(data.message || 'Error updating user status', 'error');
                }
            })
            .catch(error => {
                console.error('Error details:', error);
                showMessage('Error updating user status', 'error');
            });
        }

        function blockUser(userId) {
            if (confirm('Are you sure you want to block this user?')) {
                fetch(`/admin/users/${userId}/block`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(data.message, 'success');
                        location.reload(); // Reload page to update table
                    } else {
                        showMessage(data.message || 'Error blocking user', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error details:', error);
                    showMessage('Error blocking user', 'error');
                });
            }
        }

        function unblockUser(userId) {
            if (confirm('Are you sure you want to unblock this user?')) {
                fetch(`/admin/users/${userId}/unblock`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(data.message, 'success');
                        location.reload(); // Reload page to update table
                    } else {
                        showMessage(data.message || 'Error unblocking user', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error details:', error);
                    showMessage('Error unblocking user', 'error');
                });
            }
        }
        
        function saveUser(event) {
            event.preventDefault();

            const formData = new FormData(event.target);
            const userId = document.getElementById('userId').value;
            const url = userId ? `/admin/users/${userId}` : '/admin/users';
            const method = userId ? 'PUT' : 'POST';

            // Convert FormData to regular object for JSON
            const data = {};
            formData.forEach((value, key) => {
                if (key !== 'userId') data[key] = value;
            });

            // Clear any empty password fields for updates
            if (userId && (!data.password || data.password.trim() === '')) {
                delete data.password;
                delete data.password_confirmation;
            }

            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    closeModal();
                    location.reload(); // Reload page to update table
                } else {
                    let errorMessage = 'Validation errors occurred';
                    if (data.errors) {
                        errorMessage = Object.values(data.errors).flat().join(', ');
                    }
                    showMessage(errorMessage, 'error');
                }
            })
            .catch(error => {
                console.error('Error details:', error);
                let errorMessage = 'Error saving user';
                if (error.errors) {
                    errorMessage = Object.values(error.errors).flat().join(', ');
                } else if (error.message) {
                    errorMessage = error.message;
                }
                showMessage(errorMessage, 'error');
            });
        }
        
        function closeModal() {
            document.getElementById('userModal').classList.add('hidden');
        }
        
        function showMessage(message, type) {
            const container = document.getElementById('messageContainer');
            const content = document.getElementById('messageContent');
            
            content.textContent = message;
            content.className = `p-4 rounded-lg text-white ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
            container.classList.remove('hidden');
            
            setTimeout(() => {
                container.classList.add('hidden');
            }, 5000);
        }
        
        // Close modal when clicking outside
        document.getElementById('userModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\VALESBEACH_LATEST\ValesBeach\resources\views/admin/user-management-functional.blade.php ENDPATH**/ ?>