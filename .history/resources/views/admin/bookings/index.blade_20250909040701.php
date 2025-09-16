<x-admin-layout>
    <x-slot name="header">
        Booking Management
    </x-slot>

    <div class="container mx-auto px-4 lg:px-16 py-8">
        <!-- Filters -->
        <div class="bg-green-800 bg-opacity-50 backdrop-blur-sm rounded-lg border border-green-700 p-6 mb-8">
            <form action="{{ route('admin.bookings') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-white mb-2">Search Guest</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Name or email..."
                           class="w-full px-4 py-2 bg-gray-900 border border-green-700 rounded-lg text-white placeholder-gray-400 focus:border-green-500 focus:ring-1 focus:ring-green-500">
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-white mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 bg-gray-900 border border-green-700 rounded-lg text-white focus:border-green-500 focus:ring-1 focus:ring-green-500">
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
                    <label class="block text-sm font-medium text-white mb-2">From Date</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full px-4 py-2 bg-gray-900 border border-green-700 rounded-lg text-white focus:border-green-500 focus:ring-1 focus:ring-green-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-white mb-2">To Date</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full px-4 py-2 bg-gray-900 border border-green-700 rounded-lg text-white focus:border-green-500 focus:ring-1 focus:ring-green-500">
                </div>

                <!-- Buttons -->
                <div class="md:col-span-2 lg:col-span-4 flex justify-end space-x-4">
                    <a href="{{ route('admin.bookings') }}" 
                       class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-all duration-300">
                        Reset
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-700 text-white rounded-lg hover:bg-green-600 transition-all duration-300">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Bookings Table -->
        <div class="bg-green-800 bg-opacity-50 backdrop-blur-sm rounded-lg border border-green-700 shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-green-900 bg-opacity-50 text-white">
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
                    <tbody class="divide-y divide-green-700">
                        @forelse($bookings as $booking)
                        <tr class="hover:bg-green-900/30 transition-colors duration-200">
                            <td class="px-6 py-4 text-white">
                                #{{ $booking->id }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white">{{ $booking->user->name }}</div>
                                <div class="text-sm text-green-300">{{ $booking->user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white">{{ $booking->room->name }}</div>
                                <div class="text-sm text-green-300">{{ $booking->guests }} guests</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white">{{ $booking->check_in->format('M d, Y') }}</div>
                                <div class="text-sm text-green-300">{{ $booking->check_out->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 text-white">
                                ₱{{ number_format($booking->total_price, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                    @elseif($booking->status === 'checked_in') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-4">
                                    <a href="{{ route('admin.bookings.show', $booking) }}" 
                                       class="text-green-300 hover:text-green-200 transition-colors duration-200">
                                        View
                                    </a>
                                    @if($booking->status !== 'cancelled')
                                    <form action="{{ route('admin.bookings.status', $booking) }}" method="POST" class="inline">
                                        @csrf
                                        <select name="status" onchange="this.form.submit()"
                                                class="bg-gray-900 border border-green-700 text-white rounded px-3 py-1 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500">
                                            <option value="">Change Status</option>
                                            @foreach($statuses as $status)
                                                @if($status !== $booking->status)
                                                <option value="{{ $status }}">
                                                    {{ ucfirst($status) }}
                                                </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-green-300">
                                No bookings found matching your criteria.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 bg-green-900 bg-opacity-50 border-t border-green-700">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
