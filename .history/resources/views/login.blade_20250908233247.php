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
                    
                    <!-- User Profile Icon -->
                    <div class="w-12 h-12 lg:w-14 lg:h-14 bg-white rounded-full flex items-center justify-center hover:bg-green-50 transition-colors duration-200 cursor-pointer">
                        <svg class="w-6 h-6 lg:w-7 lg:h-7 text-black" fill="currentColor" viewBox="0 0 51 51">
                            <path d="M35.6981 20.4001C35.6981 23.1053 34.6235 25.6997 32.7106 27.6126C30.7977 29.5255 28.2033 30.6001 25.4981 30.6001C22.7929 30.6001 20.1985 29.5255 18.2856 27.6126C16.3727 25.6997 15.2981 23.1053 15.2981 20.4001C15.2981 17.6949 16.3727 15.1005 18.2856 13.1876C20.1985 11.2748 22.7929 10.2001 25.4981 10.2001C28.2033 10.2001 30.7977 11.2748 32.7106 13.1876C34.6235 15.1005 35.6981 17.6949 35.6981 20.4001Z"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M24.4596 50.9796C10.8592 50.4339 0 39.2343 0 25.5C0 11.4164 11.4164 0 25.5 0C39.5836 0 51 11.4164 51 25.5C51 39.5836 39.5836 51 25.5 51H25.1507C24.9194 51 24.6891 50.9932 24.4596 50.9796ZM9.13665 41.5905C8.94599 41.043 8.88109 40.4595 8.94678 39.8835C9.01246 39.3075 9.20704 38.7536 9.51606 38.2631C9.82509 37.7725 10.2406 37.3579 10.7318 37.0499C11.223 36.742 11.7773 36.5486 12.3535 36.4841C22.2934 35.3838 28.7678 35.4832 38.6593 36.5071C39.2362 36.5672 39.7918 36.7581 40.2838 37.0654C40.7758 37.3727 41.1912 37.7881 41.4984 38.2802C41.8055 38.7723 41.9963 39.328 42.0563 39.9049C42.1162 40.4819 42.0437 41.0649 41.8442 41.6096C46.0837 37.3205 48.4579 31.5307 48.45 25.5C48.45 12.8252 38.1748 2.55 25.5 2.55C12.8252 2.55 2.55 12.8252 2.55 25.5C2.55 31.7679 5.06303 37.4493 9.13665 41.5905Z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="relative z-10 py-8 lg:py-16 flex items-center justify-center min-h-screen">
        <div class="container mx-auto px-4 lg:px-16">
            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-600/90 text-white rounded-lg shadow-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Login Form -->
            <div class="max-w-md mx-auto">
                <!-- Login Card -->
                <div class="bg-gray-800 rounded-lg p-8 shadow-xl">
                    <!-- Logo/Title -->
                    <div class="text-center mb-8">
                        <div class="w-20 h-20 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold text-white mb-2">Welcome Back</h2>
                        <p class="text-gray-400">Sign in to your account</p>
                    </div>

                    <!-- Login Form -->
                    <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                        @csrf

                        @if($errors->any())
                            <div class="bg-red-600 text-white p-3 rounded-lg text-sm">
                                @foreach($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif
                        
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                            <input type="email" id="email" name="email" required autocomplete="email" value="{{ old('email') }}"
                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Enter your email">
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                            <input type="password" id="password" name="password" required autocomplete="current-password"
                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Enter your password">
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input type="checkbox" id="remember" name="remember" 
                                    class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                <label for="remember" class="ml-2 block text-sm text-gray-300">
                                    Remember me
                                </label>
                            </div>
                            <a href="#" class="text-sm text-blue-400 hover:text-blue-300 transition-colors duration-200">
                                Forgot password?
                            </a>
                        </div>

                        <!-- Login Button -->
                        <button type="submit"
                            class="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition duration-200 ease-in-out transform hover:scale-105">
                            Sign In
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="my-6">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-600"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-gray-800 text-gray-400">Or continue with</span>
                            </div>
                        </div>
                    </div>

                    <!-- Social Login Options -->
                    <div class="grid grid-cols-2 gap-3">
                        <button class="w-full inline-flex justify-center py-2 px-4 border border-gray-600 rounded-lg bg-gray-700 text-sm font-medium text-gray-300 hover:bg-gray-600 transition duration-200">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                            <span class="ml-2">Google</span>
                        </button>

                        <button class="w-full inline-flex justify-center py-2 px-4 border border-gray-600 rounded-lg bg-gray-700 text-sm font-medium text-gray-300 hover:bg-gray-600 transition duration-200">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            <span class="ml-2">Facebook</span>
                        </button>
                    </div>

                    <!-- Sign Up Link -->
                    <div class="text-center mt-8">
                        <p class="text-gray-400">
                            Don't have an account?
                            <a href="/signup" class="text-blue-400 hover:text-blue-300 font-medium transition-colors duration-200">
                                Sign up
                            </a>
                        </p>
                    </div>
                </div>

                <!-- Additional Links -->
                <div class="text-center mt-6">
                    <p class="text-gray-400 text-sm">
                        Having trouble?
                        <a href="#" class="text-blue-400 hover:text-blue-300 transition-colors duration-200">
                            Contact support
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
