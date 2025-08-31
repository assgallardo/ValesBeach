<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Vales Beach Resort</title>
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
                        <svg class="w-6 h-6 lg:w-7 lg:h-7 text-green-900" fill="currentColor" viewBox="0 0 51 51">
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
            <!-- Dashboard Title -->
            <div class="text-center mb-12 lg:mb-16">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50">
                    Admin Dashboard
                </h2>
            </div>

            <!-- Management Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 lg:gap-8 max-w-7xl mx-auto">
                
                <!-- Food Menu Management -->
                <div class="bg-white bg-opacity-5 backdrop-blur-sm rounded-lg p-6 lg:p-8 border border-white border-opacity-10 hover:bg-opacity-10 transition-all duration-300 transform hover:scale-105">
                    <div class="text-center h-full flex flex-col">
                        <h3 class="text-2xl lg:text-3xl font-bold text-green-50 mb-6 leading-tight">
                            Food Menu Management
                        </h3>
                        <p class="text-green-50 text-sm lg:text-base opacity-90 mb-8 flex-grow">
                            Create, read, update, and delete food items available at the resort.
                        </p>
                        <button class="w-full py-3 bg-green-50 text-green-900 font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200">
                            Manage Menu
                        </button>
                    </div>
                </div>

                <!-- Rooms & Facilities -->
                <div class="bg-white bg-opacity-5 backdrop-blur-sm rounded-lg p-6 lg:p-8 border border-white border-opacity-10 hover:bg-opacity-10 transition-all duration-300 transform hover:scale-105">
                    <div class="text-center h-full flex flex-col">
                        <h3 class="text-2xl lg:text-3xl font-bold text-green-50 mb-6 leading-tight">
                            Rooms & Facilities
                        </h3>
                        <p class="text-green-50 text-sm lg:text-base opacity-90 mb-8 flex-grow">
                            Update room availability, rates, and facility details.
                        </p>
                        <button class="w-full py-3 bg-green-50 text-green-900 font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200">
                            Manage Rooms
                        </button>
                    </div>
                </div>

                <!-- Bookings Management -->
                <div class="bg-white bg-opacity-5 backdrop-blur-sm rounded-lg p-6 lg:p-8 border border-white border-opacity-10 hover:bg-opacity-10 transition-all duration-300 transform hover:scale-105">
                    <div class="text-center h-full flex flex-col">
                        <h3 class="text-2xl lg:text-3xl font-bold text-green-50 mb-6 leading-tight">
                            Bookings Management
                        </h3>
                        <p class="text-green-50 text-sm lg:text-base opacity-90 mb-8 flex-grow">
                            Oversee guest reservations, check-ins, and check-outs.
                        </p>
                        <button class="w-full py-3 bg-green-50 text-green-900 font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200">
                            Manage Bookings
                        </button>
                    </div>
                </div>

                <!-- User Management -->
                <div class="bg-white bg-opacity-5 backdrop-blur-sm rounded-lg p-6 lg:p-8 border border-white border-opacity-10 hover:bg-opacity-10 transition-all duration-300 transform hover:scale-105">
                    <div class="text-center h-full flex flex-col">
                        <h3 class="text-2xl lg:text-3xl font-bold text-green-50 mb-6 leading-tight">
                            User Management
                        </h3>
                        <p class="text-green-50 text-sm lg:text-base opacity-90 mb-8 flex-grow">
                            Manage user accounts, permissions, and access controls.
                        </p>
                        <button class="w-full py-3 bg-green-50 text-green-900 font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200">
                            Manage Users
                        </button>
                    </div>
                </div>

                <!-- Reports & Analytics -->
                <div class="bg-white bg-opacity-5 backdrop-blur-sm rounded-lg p-6 lg:p-8 border border-white border-opacity-10 hover:bg-opacity-10 transition-all duration-300 transform hover:scale-105">
                    <div class="text-center h-full flex flex-col">
                        <h3 class="text-2xl lg:text-3xl font-bold text-green-50 mb-6 leading-tight">
                            Reports & Analytics
                        </h3>
                        <p class="text-green-50 text-sm lg:text-base opacity-90 mb-8 flex-grow">
                            Access performance reports and guest insights.
                        </p>
                        <button class="w-full py-3 bg-green-50 text-green-900 font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200">
                            View Reports
                        </button>
                    </div>
                </div>

                <!-- Settings & Configuration -->
                <div class="bg-white bg-opacity-5 backdrop-blur-sm rounded-lg p-6 lg:p-8 border border-white border-opacity-10 hover:bg-opacity-10 transition-all duration-300 transform hover:scale-105">
                    <div class="text-center h-full flex flex-col">
                        <h3 class="text-2xl lg:text-3xl font-bold text-green-50 mb-6 leading-tight">
                            Settings & Configuration
                        </h3>
                        <p class="text-green-50 text-sm lg:text-base opacity-90 mb-8 flex-grow">
                            Configure system settings and resort preferences.
                        </p>
                        <button class="w-full py-3 bg-green-50 text-green-900 font-medium text-lg rounded-lg hover:bg-white transition-colors duration-200">
                            Manage Settings
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </main>
</body>
</html>
