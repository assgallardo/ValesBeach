<x-admin-layout>
    <x-slot name="header">
        Booking Management
    </x-slot>
<div class="container mx-auto px-4 lg:px-16 py-8">
    <!-- Page Title -->
    <div class="text-center mb-8">
        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4">
            Booking Management
        </h2>
        <p class="text-xl text-gray-200">
            Manage and monitor all resort bookings
        </p>
    </div>

    <!-- Filters -->
    <div class="bg-gray-800 rounded-lg p-6 mb-8">
        <form action="{{ route('admin.bookings') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Search Guest</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Name or email..."
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date Range -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
            </div>

            <!-- Buttons -->
            <div class="md:col-span-2 lg:col-span-4 flex justify-end space-x-4">
                <a href="{{ route('admin.bookings') }}" 
                   class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    Reset
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Bookings Table -->
    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-700 text-white">
                    <tr>
                        <th class="px-6 py-4">Booking ID</th>
                        <th class="px-6 py-4">Guest</th>
                        <th class="px-6 py-4">Room</th>
                        <th class="px-6 py-4">Dates</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-600">
                    @foreach($bookings as $booking)
                    <tr class="hover:bg-gray-700/50 transition-colors duration-200">
                        <td class="px-6 py-4 text-white">
                            #{{ $booking->id }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white">{{ $booking->user->name }}</div>
                            <div class="text-sm text-gray-400">{{ $booking->user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white">{{ $booking->room->name }}</div>
                            <div class="text-sm text-gray-400">{{ $booking->guests }} guests</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white">{{ $booking->check_in->format('M d, Y') }}</div>
                            <div class="text-sm text-gray-400">{{ $booking->check_out->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 text-white">
                            {{ $booking->formatted_total_price }}
                        </td>
                        <td class="px-6 py-4">
                            {!! $booking->status_badge !!}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-4">
                                <!-- Status Update Form -->
                                <form action="{{ route('admin.bookings.status', $booking) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()"
                                            class="px-3 py-1 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm">
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}" {{ $booking->status === $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>

                                <!-- View Details Button -->
                                <a href="{{ route('guest.bookings.show', $booking) }}" 
                                   class="text-blue-400 hover:text-blue-300 transition-colors duration-200">
                                    View
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-700 border-t border-gray-600">
            {{ $bookings->links() }}
        </div>
    </div>
</div>
@endsection
