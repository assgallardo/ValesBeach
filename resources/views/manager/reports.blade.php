@extends('layouts.admin')

@section('content')
    <main class="relative z-10 py-8 lg:py-16">
        <div class="container mx-auto px-4 lg:px-16">
            <!-- Page Header -->
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50 mb-4">
                    Reports & Analytics
                </h2>
                <p class="text-green-50 opacity-80 text-lg">
                    View detailed reports and analytics
                </p>
                <div class="mt-6">
                    <a href="{{ route('manager.dashboard') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Reports Content -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Monthly Bookings -->
                <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-8">
                    <h3 class="text-2xl font-bold text-green-50 mb-6">Monthly Bookings</h3>
                    @if($monthlyBookings->count() > 0)
                    <div class="space-y-4">
                        @foreach($monthlyBookings as $month)
                        <div class="flex justify-between items-center p-4 bg-green-800/50 rounded-lg">
                            <div>
                                <h4 class="text-green-200 font-medium">
                                    {{ \Carbon\Carbon::create($month->year, $month->month)->format('F Y') }}
                                </h4>
                                <p class="text-green-300 text-sm">{{ $month->total_bookings }} bookings</p>
                            </div>
                            <div class="text-right">
                                <p class="text-green-50 font-bold">₱{{ number_format($month->total_revenue ?? 0, 2) }}</p>
                                <p class="text-green-300 text-sm">Revenue</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-green-300">No booking data available.</p>
                    @endif
                </div>

                <!-- Popular Rooms -->
                <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-8">
                    <h3 class="text-2xl font-bold text-green-50 mb-6">Popular Rooms</h3>
                    @if($popularRooms->count() > 0)
                    <div class="space-y-4">
                        @foreach($popularRooms as $room)
                        <div class="flex justify-between items-center p-4 bg-green-800/50 rounded-lg">
                            <div>
                                <h4 class="text-green-200 font-medium">{{ $room->name }}</h4>
                                <p class="text-green-300 text-sm">{{ $room->type ?? 'Standard' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-green-50 font-bold">{{ $room->booking_count ?? 0 }}</p>
                                <p class="text-green-300 text-sm">Bookings</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-green-300">No room data available.</p>
                    @endif
                </div>
            </div>
        </div>
    </main>
@endsection