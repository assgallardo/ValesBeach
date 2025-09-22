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
    <!-- Alpine.js for interactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-gray-900" style="font-family: 'Poppins', sans-serif;">
    <!-- Admin Navigation -->
    <nav class="bg-green-900 shadow-lg">
        <div class="container mx-auto px-4 lg:px-16">
            <div class="flex items-center justify-between h-16">
                <!-- Logo/Brand -->
                <div class="flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="text-white font-bold text-xl">
                        @if(auth()->user()->role === 'staff')
                            Vales Beach Staff
                        @elseif(auth()->user()->role === 'manager')
                            Vales Beach Manager
                        @else
                            Vales Beach Admin
                        @endif
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:block">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="text-gray-300 hover:bg-green-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium
                                  {{ request()->routeIs('admin.dashboard') ? 'bg-green-700 text-white' : '' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('admin.rooms.index') }}" 
                           class="text-gray-300 hover:bg-green-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium
                                  {{ request()->routeIs('admin.rooms.*') ? 'bg-green-700 text-white' : '' }}">
                            Rooms
                        </a>
                        
                        <!-- Calendar Function for Admin and Staff -->
                        <a href="{{ route('admin.calendar') }}" 
                           class="inline-flex items-center px-6 py-3 bg-green-50 text-black font-medium rounded-lg hover:bg-white transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Calendar
                        </a>
                        
                        <!-- Add more navigation links as needed -->
                    </div>
                </div>

                <!-- User Menu -->
                <div class="flex items-center">
                    <div class="relative group">
                        <button class="flex items-center text-gray-300 hover:text-white focus:outline-none py-2">
                            <span class="mr-2">{{ auth()->user()->name }}</span>
                            <!-- Role Indicator Badge -->
                            <span class="inline-flex items-center px-2 py-1 mr-2 text-xs font-medium rounded-md
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
                            <svg class="h-4 w-4 transition-transform group-hover:rotate-180" 
                                 fill="none" 
                                 stroke="currentColor" 
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" 
                                      stroke-linejoin="round" 
                                      stroke-width="2" 
                                      d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <div class="absolute right-0 top-full w-48 bg-green-800 rounded-md shadow-lg py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 ease-in-out z-50">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-green-700 hover:text-white transition-colors duration-150">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>