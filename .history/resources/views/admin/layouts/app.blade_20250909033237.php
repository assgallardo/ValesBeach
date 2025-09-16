<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Vales Beach Resort</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="min-h-screen bg-gray-900" style="font-family: 'Poppins', sans-serif;">
    <!-- Background decorative blur elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute w-96 h-96 bg-green-800 opacity-30 rounded-full blur-3xl -top-48 -left-48"></div>
        <div class="absolute w-80 h-80 bg-green-700 opacity-20 rounded-full blur-3xl top-1/3 right-1/4"></div>
        <div class="absolute w-72 h-72 bg-green-800 opacity-25 rounded-full blur-3xl bottom-1/4 left-1/3"></div>
    </div>

    <!-- Header -->
    <header class="bg-gray-800 shadow-lg relative z-10">
        <div class="container mx-auto px-4 lg:px-16">
            <div class="flex items-center justify-between py-4">
                <h1 class="text-2xl font-bold text-white">
                    VALES BEACH RESORT
                </h1>
                <nav class="hidden md:flex space-x-8">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-300 hover:text-white transition-colors">
                        Dashboard
                    </a>
                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.users') }}" class="text-gray-300 hover:text-white transition-colors">
                        Users
                    </a>
                    @endif
                    @if(in_array(auth()->user()->role, ['admin', 'manager']))
                    <a href="{{ route('admin.bookings') }}" class="text-gray-300 hover:text-white transition-colors">
                        Bookings
                    </a>
                    @endif
                    <div class="relative group">
                        <button class="text-gray-300 hover:text-white transition-colors">
                            {{ auth()->user()->name }}
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-xl py-2 hidden group-hover:block">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-gray-300 hover:text-white hover:bg-gray-700 transition-colors">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="relative z-10">
        @yield('content')
    </main>

    <!-- Flash Messages -->
    @if(session()->has('success'))
    <div class="fixed bottom-4 right-4 px-6 py-3 bg-green-500 text-white rounded-lg shadow-lg" 
         x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 3000)">
        {{ session('success') }}
    </div>
    @endif

    @if(session()->has('error'))
    <div class="fixed bottom-4 right-4 px-6 py-3 bg-red-500 text-white rounded-lg shadow-lg"
         x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 3000)">
        {{ session('error') }}
    </div>
    @endif
</body>
</html>
