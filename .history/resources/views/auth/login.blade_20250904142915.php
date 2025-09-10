<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Vales Beach Resort</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
                    <a href="/" class="text-green-50 text-lg lg:text-xl font-light hover:text-green-200 transition-colors duration-200">
                        Home
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="relative z-10 py-8 lg:py-16 flex items-center justify-center min-h-screen">
        <div class="container mx-auto px-4 lg:px-16">
            <!-- Login Form -->
            <div class="max-w-md mx-auto">
                <!-- Login Card -->
                <div class="bg-green-800 rounded-lg shadow-xl p-6 lg:p-8">
                    <h2 class="text-2xl lg:text-3xl font-bold text-green-50 mb-6 text-center">
                        Login to Your Account
                    </h2>

                    @if($errors->any())
                        <div class="bg-red-500 text-white p-4 rounded-lg mb-6">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                        @csrf
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-green-100 mb-2">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                class="w-full bg-green-900 text-white rounded-lg p-2.5 border border-green-700 focus:ring-2 focus:ring-green-600"
                                placeholder="your.email@example.com">
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-green-100 mb-2">Password</label>
                            <input type="password" name="password" id="password" required
                                class="w-full bg-green-900 text-white rounded-lg p-2.5 border border-green-700 focus:ring-2 focus:ring-green-600"
                                placeholder="••••••••">
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center">
                            <input type="checkbox" name="remember" id="remember"
                                class="rounded border-green-700 text-green-600 focus:ring-green-500 bg-green-900">
                            <label for="remember" class="ml-2 text-sm text-green-100">Remember me</label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                            class="w-full py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium">
                            Login
                        </button>

                        <!-- Links -->
                        <div class="text-center">
                            <p class="text-sm text-green-100">
                                Don't have an account?
                                <a href="{{ route('signup') }}" class="text-white hover:text-green-200 transition-colors duration-200">
                                    Sign up
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
