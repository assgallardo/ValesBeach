<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Vales Beach Resort</title>
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
                <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-white">
                    VALES BEACH RESORT
                </h1>
                
                <!-- Navigation -->
                <div class="flex items-center space-x-6 lg:space-x-8">
                    @auth
                        <!-- User Profile & Logout -->
                        <div class="flex items-center space-x-4">
                            <span class="text-white text-lg">{{ auth()->user()->name }}</span>
                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-700 text-white">
                                {{ ucfirst(auth()->user()->role) }}
                            </span>
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" 
                                   class="text-white hover:text-green-200 transition-colors duration-200">
                                    Admin Dashboard
                                </a>
                            @elseif(auth()->user()->role === 'manager')
                                <a href="{{ route('manager.dashboard') }}" 
                                   class="text-white hover:text-green-200 transition-colors duration-200">
                                    Manager Dashboard
                                </a>
                            @elseif(auth()->user()->role === 'staff')
                                <a href="{{ route('staff.dashboard') }}" 
                                   class="text-white hover:text-green-200 transition-colors duration-200">
                                    Staff Dashboard
                                </a>
                            @elseif(auth()->user()->role === 'guest')
                                <a href="{{ route('guest.dashboard') }}" 
                                   class="text-white hover:text-green-200 transition-colors duration-200">
                                    Guest Dashboard
                                </a>
                            @endif
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-white hover:text-green-200 transition-colors duration-200">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-white text-lg hover:text-green-200 transition-colors duration-200">
                            Login
                        </a>
                        <a href="{{ route('signup') }}" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                            Sign Up
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="relative z-10">
        <!-- Hero Section -->
        <section class="py-20 lg:py-32 relative overflow-hidden">
            <div class="container mx-auto px-4 lg:px-16 text-center">
                <h2 class="text-4xl md:text-5xl lg:text-7xl font-bold text-white mb-8">
                    Experience Paradise<br>at Vales Beach
                </h2>
                <p class="text-xl md:text-2xl text-gray-200 max-w-3xl mx-auto mb-12">
                    Discover the perfect blend of luxury and natural beauty at our beachfront resort.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    @guest
                        <a href="{{ route('signup') }}" 
                           class="px-8 py-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 text-lg font-medium">
                            Book Your Stay
                        </a>
                        <a href="{{ route('login') }}" 
                           class="px-8 py-4 bg-green-900 text-white rounded-lg hover:bg-green-800 transition-colors duration-200 text-lg font-medium">
                            Member Login
                        </a>
                    @else
                        <div class="flex flex-col items-center gap-6">
                            <!-- Welcome Message -->
                            <div class="text-white mb-4">
                                <h3 class="text-2xl font-bold mb-2">Welcome, {{ auth()->user()->name }}!</h3>
                                <p class="text-gray-200">What would you like to do today?</p>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex gap-4">
                                @if(auth()->user()->role === 'guest')
                                    <a href="{{ route('guest.rooms.browse') }}" 
                                       class="px-8 py-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 text-lg font-medium">
                                        Browse Rooms
                                    </a>
                                    <a href="{{ route('guest.dashboard') }}" 
                                       class="px-8 py-4 bg-green-900 text-white rounded-lg hover:bg-green-800 transition-colors duration-200 text-lg font-medium">
                                        Go to Dashboard
                                    </a>
                                @elseif(auth()->user()->role === 'staff')
                                    <a href="{{ route('staff.dashboard') }}" 
                                       class="px-8 py-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 text-lg font-medium">
                                        Staff Dashboard
                                    </a>
                                @elseif(auth()->user()->role === 'manager')
                                    <a href="{{ route('manager.dashboard') }}" 
                                       class="px-8 py-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 text-lg font-medium">
                                        Manager Dashboard
                                    </a>
                                @elseif(auth()->user()->role === 'admin')
                                    <a href="{{ route('admin.dashboard') }}" 
                                       class="px-8 py-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 text-lg font-medium">
                                        Admin Dashboard
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-20 bg-green-900/30 backdrop-blur-sm">
            <div class="container mx-auto px-4 lg:px-16">
                <h3 class="text-3xl font-bold text-white text-center mb-12">Why Choose Vales Beach Resort?</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Luxury Accommodations -->
                    <div class="bg-green-800/50 rounded-xl p-8 text-center border-2 border-green-700">
                        <div class="bg-green-700 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h4 class="text-xl font-semibold text-white mb-4">Luxury Accommodations</h4>
                        <p class="text-gray-200">Experience comfort in our meticulously designed rooms with modern amenities.</p>
                    </div>

                    <!-- Private Beach Access -->
                    <div class="bg-green-800/50 rounded-xl p-8 text-center border-2 border-green-700">
                        <div class="bg-green-700 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </div>
                        <h4 class="text-xl font-semibold text-white mb-4">Private Beach Access</h4>
                        <p class="text-gray-200">Enjoy exclusive access to pristine beaches and crystal-clear waters.</p>
                    </div>

                    <!-- World-Class Service -->
                    <div class="bg-green-800/50 rounded-xl p-8 text-center border-2 border-green-700">
                        <div class="bg-green-700 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h4 class="text-xl font-semibold text-white mb-4">World-Class Service</h4>
                        <p class="text-gray-200">Our dedicated staff ensures your stay exceeds all expectations.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action Section -->
        <section class="py-20">
            <div class="container mx-auto px-4 lg:px-16 text-center">
                <h3 class="text-3xl font-bold text-white mb-8">Ready to Experience Paradise?</h3>
                <p class="text-xl text-gray-200 max-w-2xl mx-auto mb-8">
                    Join us at Vales Beach Resort for an unforgettable stay. Book your room today and discover why we're the perfect choice for your next getaway.
                </p>
                @guest
                <a href="{{ route('signup') }}" 
                   class="inline-block px-8 py-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 text-lg font-medium">
                    Start Your Journey
                </a>
                @endguest
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="relative z-10 bg-green-900/80 backdrop-blur-sm mt-16 py-12 border-t border-green-800">
        <div class="container mx-auto px-4 lg:px-16">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div class="text-center md:text-left">
                    <h4 class="text-lg font-semibold text-white mb-4">Contact Us</h4>
                    <p class="text-gray-200">Email: info@valesbeach.com</p>
                    <p class="text-gray-200">Phone: (123) 456-7890</p>
                </div>
                <div class="text-center">
                    <h4 class="text-lg font-semibold text-white mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-200 hover:text-white transition-colors duration-200">About Us</a></li>
                        <li><a href="#" class="text-gray-200 hover:text-white transition-colors duration-200">Terms of Service</a></li>
                        <li><a href="#" class="text-gray-200 hover:text-white transition-colors duration-200">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="text-center md:text-right">
                    <h4 class="text-lg font-semibold text-white mb-4">Follow Us</h4>
                    <div class="flex justify-center md:justify-end space-x-4">
                        <a href="#" class="text-gray-200 hover:text-white transition-colors duration-200">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-200 hover:text-white transition-colors duration-200">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-200 hover:text-white transition-colors duration-200">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="text-center pt-8 border-t border-green-800">
                <p class="text-gray-300">&copy; {{ date('Y') }} Vales Beach Resort. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
