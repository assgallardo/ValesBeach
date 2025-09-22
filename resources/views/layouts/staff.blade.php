<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Dashboard - Vales Beach Resort</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen bg-gray-900">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 w-64 bg-green-900/50 backdrop-blur-sm">
        <div class="flex items-center justify-center h-16 border-b border-green-800">
            <span class="text-xl font-bold text-white">Vales Beach Resort</span>
        </div>
        
        <nav class="mt-6">
            <div class="px-4 space-y-2">
                <a href="{{ route('staff.dashboard') }}" 
                   class="flex items-center px-4 py-2 rounded-lg {{ request()->routeIs('staff.dashboard') ? 'bg-green-600' : 'hover:bg-green-800/50' }} text-white">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.rooms.index') }}"
                   class="flex items-center px-4 py-2 rounded-lg {{ request()->routeIs('admin.rooms.*') ? 'bg-green-600' : 'hover:bg-green-800/50' }} text-white">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Rooms & Facilities
                </a>

                <a href="{{ route('admin.reservations') }}"
                   class="flex items-center px-4 py-2 rounded-lg {{ request()->routeIs('admin.reservations') ? 'bg-green-600' : 'hover:bg-green-800/50' }} text-white">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Bookings
                </a>

                <a href="{{ route('admin.calendar') }}"
                   class="flex items-center px-4 py-2 rounded-lg {{ request()->routeIs('admin.calendar') ? 'bg-green-600' : 'hover:bg-green-800/50' }} text-white">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Calendar
                </a>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="ml-64">
        <!-- Top Navigation -->
        <div class="bg-green-900/50 backdrop-blur-sm">
            <div class="flex items-center justify-end h-16 px-6">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <span class="text-gray-300">{{ auth()->user()->name }}</span>
                        <span class="ml-2 px-2 py-1 text-xs font-medium bg-green-600 text-white rounded">STAFF</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="px-3 py-1 text-sm text-gray-300 hover:text-white transition-colors">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <main class="p-6">
            @yield('content')
        </main>
    </div>
</body>
</html>