@extends('layouts.guest')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-green-800 rounded-xl shadow-xl p-8 mb-8">
        <h1 class="text-3xl font-semibold mb-4 text-green-50">Welcome Back, {{ auth()->user()->name }}</h1>
        <p class="text-green-100">Manage your bookings and explore our exclusive offers just for you.</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Quick Actions -->
        <div class="bg-green-900/50 rounded-xl shadow-xl p-6 border border-green-800">
            <h2 class="text-xl font-semibold mb-4 text-green-50">Quick Actions</h2>
            <ul class="space-y-3">
                <li>
                    <a href="{{ route('guest.rooms') }}" 
                       class="flex items-center text-green-100 hover:text-green-200 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Browse Available Rooms
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center text-green-100 hover:text-green-200 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        My Bookings
                    </a>
                </li>
            </ul>
        </div>

        <!-- Latest Updates -->
        <div class="bg-green-900/50 rounded-xl shadow-xl p-6 border border-green-800">
            <h2 class="text-xl font-semibold mb-4 text-green-50">Latest Updates</h2>
            <div class="space-y-4">
                <div class="p-4 bg-green-800/50 rounded-lg">
                    <span class="text-xs font-medium text-green-200">NEW OFFER</span>
                    <p class="text-green-100 mt-2">Get 20% off on weekday bookings this month!</p>
                </div>
                <div class="p-4 bg-green-800/50 rounded-lg">
                    <span class="text-xs font-medium text-green-200">COMING SOON</span>
                    <p class="text-green-100 mt-2">New beachfront villas opening next month.</p>
                </div>
            </div>
        </div>

        <!-- Need Help? -->
        <div class="bg-green-900/50 rounded-xl shadow-xl p-6 border border-green-800">
            <h2 class="text-xl font-semibold mb-4 text-green-50">Need Help?</h2>
            <div class="space-y-4">
                <p class="text-green-100">Our support team is available 24/7</p>
                <div class="space-y-2">
                    <div class="flex items-center text-green-100">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        support@valesbeach.com
                    </div>
                    <div class="flex items-center text-green-100">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        (123) 456-7890
                    </div>
                </div>
                <button class="w-full mt-4 bg-green-700 hover:bg-green-600 text-green-50 py-2 px-4 rounded-lg transition-colors duration-200">
                    Contact Support
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
