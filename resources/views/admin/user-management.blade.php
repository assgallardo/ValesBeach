<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Vales Beach Resort</title>
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
                    <a href="/admin" class="text-green-50 text-lg lg:text-xl font-light hover:text-green-200 transition-colors duration-200">
                        Dashboard
                    </a>
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
            <div class="bg-white bg-opacity-5 backdrop-blur-sm rounded-lg p-4 lg:p-6 border border-white border-opacity-10 mb-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <!-- Search Bar -->
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <input type="text" placeholder="Search users..." 
                                class="w-full px-4 py-3 bg-white bg-opacity-10 border border-white border-opacity-20 rounded-lg text-green-50 placeholder-green-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <svg class="absolute right-3 top-3.5 w-5 h-5 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Add User Button -->
                    <button class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors duration-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add New User
                    </button>
                </div>
            </div>

            <!-- Users Table -->
            <div class="bg-white bg-opacity-5 backdrop-blur-sm rounded-lg border border-white border-opacity-10 overflow-hidden">
                <!-- Table Header -->
                <div class="px-6 py-4 bg-white bg-opacity-5 border-b border-white border-opacity-10">
                    <h3 class="text-xl font-semibold text-green-50">All Users</h3>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-white bg-opacity-5">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-medium text-green-200 uppercase tracking-wider">User</th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-green-200 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-green-200 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-green-200 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-green-200 uppercase tracking-wider">Joined</th>
                                <th class="px-6 py-4 text-left text-sm font-medium text-green-200 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white divide-opacity-10">
                            <!-- Sample User 1 -->
                            <tr class="hover:bg-white hover:bg-opacity-5 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white font-medium">
                                            JD
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-green-50">John Doe</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-100">john.doe@example.com</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium bg-blue-600 text-white rounded-full">Admin</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium bg-green-600 text-white rounded-full">Active</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-100">Jan 15, 2024</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button class="text-blue-400 hover:text-blue-300 transition-colors duration-200">Edit</button>
                                    <button class="text-red-400 hover:text-red-300 transition-colors duration-200">Delete</button>
                                </td>
                            </tr>

                            <!-- Sample User 2 -->
                            <tr class="hover:bg-white hover:bg-opacity-5 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center text-white font-medium">
                                            JS
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-green-50">Jane Smith</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-100">jane.smith@example.com</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium bg-green-600 text-white rounded-full">Manager</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium bg-green-600 text-white rounded-full">Active</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-100">Jan 10, 2024</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button class="text-blue-400 hover:text-blue-300 transition-colors duration-200">Edit</button>
                                    <button class="text-red-400 hover:text-red-300 transition-colors duration-200">Delete</button>
                                </td>
                            </tr>

                            <!-- Sample User 3 -->
                            <tr class="hover:bg-white hover:bg-opacity-5 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-orange-600 rounded-full flex items-center justify-center text-white font-medium">
                                            MJ
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-green-50">Mike Johnson</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-100">mike.johnson@example.com</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium bg-gray-600 text-white rounded-full">Staff</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium bg-yellow-600 text-white rounded-full">Inactive</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-100">Dec 20, 2023</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button class="text-blue-400 hover:text-blue-300 transition-colors duration-200">Edit</button>
                                    <button class="text-red-400 hover:text-red-300 transition-colors duration-200">Delete</button>
                                </td>
                            </tr>

                            <!-- Sample User 4 -->
                            <tr class="hover:bg-white hover:bg-opacity-5 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-pink-600 rounded-full flex items-center justify-center text-white font-medium">
                                            SW
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-green-50">Sarah Wilson</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-100">sarah.wilson@example.com</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium bg-gray-600 text-white rounded-full">Staff</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium bg-green-600 text-white rounded-full">Active</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-100">Jan 5, 2024</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button class="text-blue-400 hover:text-blue-300 transition-colors duration-200">Edit</button>
                                    <button class="text-red-400 hover:text-red-300 transition-colors duration-200">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer -->
                <div class="px-6 py-4 bg-white bg-opacity-5 border-t border-white border-opacity-10">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-green-200">
                            Showing 1-4 of 4 users
                        </div>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 bg-white bg-opacity-10 text-green-200 rounded border border-white border-opacity-20 hover:bg-opacity-20 transition-colors duration-200">
                                Previous
                            </button>
                            <button class="px-3 py-1 bg-white bg-opacity-10 text-green-200 rounded border border-white border-opacity-20 hover:bg-opacity-20 transition-colors duration-200">
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
