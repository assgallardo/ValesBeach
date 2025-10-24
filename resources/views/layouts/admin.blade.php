<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @if(auth()->user()->role === 'staff')
            Staff Dashboard - Vales Beach Resort
        @elseif(auth()->user()->role === 'manager')
            Manager Dashboard - Vales Beach Resort
        @else
            Admin Dashboard - Vales Beach Resort
        @endif
    </title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js for interactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased min-h-screen bg-gray-900 relative overflow-x-hidden" style="font-family: 'Poppins', sans-serif;">
    <!-- Admin Navigation -->
    <nav class="relative z-10 bg-green-900 shadow-xl">
        <div class="container mx-auto px-4 lg:px-16">
            <div class="flex justify-between items-center h-16">
                <!-- Logo/Brand - Updated to match your style -->
                <div class="flex">
                    @if(auth()->user()->role === 'manager')
                        @if(Route::has('manager.dashboard'))
                            <a href="{{ route('manager.dashboard') }}" class="text-2xl font-bold text-white">
                                ValesBeach Manager
                            </a>
                        @else
                            <a href="#" class="text-2xl font-bold text-white">
                                ValesBeach Manager
                            </a>
                        @endif
                    @elseif(auth()->user()->role === 'staff')
                        @if(Route::has('staff.dashboard'))
                            <a href="{{ route('staff.dashboard') }}" class="text-2xl font-bold text-white">
                                ValesBeach Staff
                            </a>
                        @else
                            <a href="#" class="text-2xl font-bold text-white">
                                ValesBeach Staff
                            </a>
                        @endif
                    @else
                        @if(Route::has('admin.dashboard'))
                            <a href="{{ route('admin.dashboard') }}" class="text-2xl font-bold text-white">
                                ValesBeach Admin
                            </a>
                        @else
                            <a href="#" class="text-2xl font-bold text-white">
                                ValesBeach Admin
                            </a>
                        @endif
                    @endif
                </div>

                <!-- Navigation Links - Updated structure -->
                <div class="flex items-center space-x-6">
                    <div class="hidden md:block">
                        <div class="flex items-center space-x-4">
                            <!-- Dashboard link -->
                            @if(auth()->user()->role === 'manager')
                                @if(Route::has('manager.dashboard'))
                                    <a href="{{ route('manager.dashboard') }}" 
                                       class="text-gray-200 hover:text-white transition-colors duration-200 font-medium">
                                        Dashboard
                                    </a>
                                @endif
                            @elseif(auth()->user()->role === 'staff')
                                @if(Route::has('staff.dashboard'))
                                    <a href="{{ route('staff.dashboard') }}" 
                                       class="text-gray-200 hover:text-white transition-colors duration-200 font-medium">
                                        Dashboard
                                    </a>
                                @endif
                                
                                <!-- Tasks link for staff with notification badges -->
                                @php
                                    // Get task counts for the current staff member
                                    $pendingTasks = \App\Models\Task::where('assigned_to', auth()->id())
                                        ->whereIn('status', ['pending', 'assigned'])
                                        ->count();
                                    
                                    $overdueTasks = \App\Models\Task::where('assigned_to', auth()->id())
                                        ->where('due_date', '<', now())
                                        ->whereNotIn('status', ['completed', 'cancelled'])
                                        ->count();
                                    
                                    $totalNotifications = $pendingTasks + $overdueTasks;
                                @endphp
                                
                                <a href="{{ route('staff.tasks.index') }}" 
                                   class="relative text-gray-200 hover:text-white transition-colors duration-200 font-medium inline-flex items-center">
                                    My Tasks
                                    
                                    @if($totalNotifications > 0)
                                        <span class="relative ml-2">
                                            <!-- Notification badge -->
                                            <span class="absolute -top-2 -right-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white 
                                                @if($overdueTasks > 0)
                                                    bg-red-600
                                                @else
                                                    bg-yellow-600
                                                @endif
                                                rounded-full animate-pulse">
                                                {{ $totalNotifications > 99 ? '99+' : $totalNotifications }}
                                            </span>
                                            
                                            <!-- Notification dot for visual enhancement -->
                                            <span class="inline-block w-2 h-2 
                                                @if($overdueTasks > 0)
                                                    bg-red-600
                                                @else
                                                    bg-yellow-600
                                                @endif
                                                rounded-full"></span>
                                        </span>
                                    @endif
                                </a>
                            @else
                                @if(Route::has('admin.dashboard'))
                                    <a href="{{ route('admin.dashboard') }}" 
                                       class="text-gray-200 hover:text-white transition-colors duration-200 font-medium">
                                        Dashboard
                                    </a>
                                @endif
                            @endif

                            <!-- Rooms link - with route check -->
                            @if(Route::has('admin.rooms.index'))
                                <a href="{{ route('admin.rooms.index') }}" 
                                   class="text-gray-200 hover:text-white transition-colors duration-200 font-medium">
                                    Rooms
                                </a>
                            @endif

                            <!-- Payments link - with route check -->
                            @if(Route::has('admin.payments.index'))
                                <a href="{{ route('admin.payments.index') }}" 
                                   class="text-gray-200 hover:text-white transition-colors duration-200 font-medium">
                                    Payments
                                </a>
                            @endif
                            
                            <!-- Calendar Function for Admin and Staff - NEW BUTTON DESIGN -->
                            @if(Route::has('admin.calendar'))
                                <a href="{{ route('admin.calendar') }}" 
                                   class="inline-flex items-center px-6 py-3 bg-green-50 text-black font-medium rounded-lg hover:bg-white transition-colors duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Calendar
                                </a>
                            @else
                                <!-- Temporary placeholder with same design until route is created -->
                                <a href="#" onclick="alert('Calendar feature coming soon!')" 
                                   class="inline-flex items-center px-6 py-3 bg-green-50 text-black font-medium rounded-lg hover:bg-white transition-colors duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Calendar
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div class="flex items-center">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="flex items-center space-x-2 text-gray-200 hover:text-white transition-colors duration-200 font-medium">
                                <span>{{ Auth::user()->name }}</span>
                                <!-- Role Indicator Badge -->
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md
                                    @if(auth()->user()->role === 'admin')
                                        bg-green-600 text-white
                                    @elseif(auth()->user()->role === 'manager')
                                        bg-blue-600 text-white
                                    @elseif(auth()->user()->role === 'staff')
                                        bg-yellow-600 text-white
                                    @else
                                        bg-gray-600 text-white
                                    @endif">
                                    {{ strtoupper(auth()->user()->role) }}
                                </span>
                                <svg class="w-5 h-5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 style="display: none;"
                                 class="absolute right-0 mt-3 w-40 rounded-lg shadow-2xl z-50 overflow-hidden">
                                
                                <!-- Logout Button -->
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit"
                                            class="flex items-center justify-center w-full px-4 py-3 text-sm font-semibold text-white bg-blue-900 hover:bg-blue-800 active:bg-blue-700 transition-colors duration-200 cursor-pointer">
                                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>