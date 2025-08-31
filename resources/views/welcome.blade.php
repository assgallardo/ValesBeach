<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tailwind Test</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen">
    <!-- Hidden div to force Tailwind to compile admin colors -->
    <div class="hidden bg-gray-900 bg-green-900 bg-green-800 bg-green-700 bg-green-600 bg-green-50 text-green-50 text-green-100 text-green-200 text-green-900 border-green-500 ring-green-500 hover:bg-green-700 hover:bg-green-600 focus:ring-green-500 focus:ring-green-300 bg-white bg-opacity-5 bg-opacity-10 bg-opacity-20 border-white border-opacity-10 border-opacity-20 backdrop-blur-sm text-blue-400 text-red-400 bg-blue-600 bg-purple-600 bg-orange-600 bg-pink-600 bg-yellow-600"></div>

    <div class="text-center">
        <h1 class="text-5xl font-bold text-white mb-6">
            🎉 Tailwind is Working!
        </h1>
        <!-- Login Button -->
        <a href="/login" class="inline-block px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition duration-200 ease-in-out transform hover:scale-105">
            Login
        </a>

        <!-- Sign Up Button -->
        <a href="/signup" class="inline-flex items-center justify-center gap-2 mt-4 px-8 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-300 transition duration-200 ease-in-out">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
            Sign Up
        </a>

        <!-- Admin Dashboard Button -->
        <a href="/admin" class="inline-flex items-center justify-center gap-2 mt-4 px-8 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 focus:ring-4 focus:ring-purple-300 transition duration-200 ease-in-out">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Admin Dashboard
        </a>
    </div>
</body>
</html>
