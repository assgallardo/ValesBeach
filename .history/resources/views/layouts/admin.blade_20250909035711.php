@props(['header' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Vales Beach') }} - Admin</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-900 relative overflow-x-hidden" style="font-family: 'Poppins', sans-serif;">
    <!-- Background decorative blur elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute w-96 h-96 bg-green-800 opacity-30 rounded-full blur-3xl -top-48 -left-48"></div>
        <div class="absolute w-80 h-80 bg-green-700 opacity-20 rounded-full blur-3xl top-1/3 right-1/4"></div>
        <div class="absolute w-72 h-72 bg-green-800 opacity-25 rounded-full blur-3xl bottom-1/4 left-1/3"></div>
    </div>

    <div class="min-h-screen relative z-10">
        <!-- Navigation -->
        <header class="bg-green-900 shadow-xl">
            <div class="container mx-auto px-4 lg:px-16">
                <div class="flex items-center justify-between h-32">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('admin.dashboard') }}">
                            <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-white">
                                VALES BEACH ADMIN
                            </h1>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden lg:flex items-center space-x-8">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="text-white hover:text-green-200 transition-colors duration-200">
                            Dashboard
                        </a>
                        @if(auth()->user()->hasAnyRole(['admin', 'manager']))
                        <a href="{{ route('admin.bookings') }}" 
                           class="text-white hover:text-green-200 transition-colors duration-200">
                            Bookings
                        </a>
                        <a href="{{ route('admin.rooms') }}" 
                           class="text-white hover:text-green-200 transition-colors duration-200">
                            Rooms
                        </a>
                        @endif
                        @if(auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.users') }}" 
                           class="text-white hover:text-green-200 transition-colors duration-200">
                            Users
                        </a>
                        @endif
                    </div>

                    <!-- User Menu -->
                    <div class="flex items-center space-x-4">
                        <span class="text-white">{{ auth()->user()->name }}</span>
                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-700 text-white">
                            {{ ucfirst(auth()->user()->role) }}
                        </span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-white hover:text-green-200 transition-colors duration-200">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="py-10">
            <div class="container mx-auto px-4 lg:px-16">
                <!-- Page Heading -->
                @if (isset($header))
                    <div class="text-center mb-8">
                        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4">
                            {{ $header }}
                        </h2>
                    </div>
                @endif

                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="mb-8 bg-green-800 bg-opacity-50 border border-green-600 text-green-100 px-4 py-3 rounded relative" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-8 bg-red-900 bg-opacity-50 border border-red-600 text-red-100 px-4 py-3 rounded relative" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Main Content -->
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
    </div>
</body>
</html>
