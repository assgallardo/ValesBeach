@extends('layouts.guest')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-semibold mb-6">Welcome to Vales Beach Resort</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('guest.rooms') }}" class="text-blue-600 hover:underline">Browse Rooms</a>
                </li>
                <li>
                    <a href="#" class="text-blue-600 hover:underline">My Bookings</a>
                </li>
            </ul>
        </div>

        <!-- Latest Updates -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Latest Updates</h2>
            <p class="text-gray-600">Stay tuned for the latest updates and promotions.</p>
        </div>

        <!-- Need Help? -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Need Help?</h2>
            <p class="text-gray-600">Contact our support team:</p>
            <p class="text-gray-600 mt-2">Email: support@valesbeach.com</p>
            <p class="text-gray-600">Phone: (123) 456-7890</p>
        </div>
    </div>
</div>
@endsection
