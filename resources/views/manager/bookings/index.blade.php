@extends('layouts.manager')

@section('content')
<div class="container mx-auto px-4 lg:px-8 py-8">
    <!-- Page Title -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Reservations Management</h1>
            <p class="text-gray-400 mt-2">View and manage all resort bookings</p>
        </div>
        @if(in_array(auth()->user()->role, ['admin', 'manager']))
        <div class="flex space-x-3">
            <!-- Quick Room Selection for Booking (Same as Admin) -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" 
                        class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Quick Book Room
                    <svg class="w-4 h-4 ml-2 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     @click.away="open = false"
                     class="absolute right-0 mt-2 w-64 bg-gray-800 rounded-md shadow-lg py-1 z-50 max-h-64 overflow-y-auto"
                     style="display: none;">
                    <div class="px-4 py-2 text-sm text-gray-300 border-b border-gray-700">Select Room to Book:</div>
                    @php
                        $availableRooms = \App\Models\Room::where('is_available', true)->get();
                    @endphp
                    @foreach($availableRooms as $room)
                        <a href="{{ route('manager.bookings.createFromRoom', $room) }}" 
                           class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">
                            <div class="font-medium">{{ $room->name }}</div>
                            <div class="text-xs text-gray-400">₱{{ number_format((float)$room->price, 2) }}/night • {{ $room->capacity }} guests</div>
                        </a>
                    @endforeach
                    @if($availableRooms->isEmpty())
                        <div class="px-4 py-2 text-sm text-gray-400">No available rooms</div>
                    @endif
                </div>
            </div>

            <!-- Original Manual Booking -->
            <a href="{{ route('manager.bookings.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors duration-200 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Manual Booking
            </a>
        </div>
        @endif
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-800 border border-green-600 text-green-100 px-6 py-4 rounded-lg mb-8">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
    <div class="bg-red-800 border border-red-600 text-red-100 px-6 py-4 rounded-lg mb-8">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-gray-800 rounded-lg p-6 mb-8">
        <form action="{{ route('manager.bookings.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="checked_in" {{ request('status') === 'checked_in' ? 'selected' : '' }}>Checked In</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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

            <!-- Filter Buttons -->
            <div class="md:col-span-2 lg:col-span-4 flex justify-end space-x-4">
                <a href="{{ route('manager.bookings.index') }}" 
                   class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-500 transition-all">
                    Reset
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition-all">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Bookings Table -->
    <div class="bg-gray-800 rounded-lg shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-900 text-white">
                    <tr>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Guest</th>
                        <th class="px-6 py-4">Room</th>
                        <th class="px-6 py-4">Dates & Times</th>
                        <th class="px-6 py-4">Total Amount</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($bookings ?? [] as $booking)
                    <tr class="text-gray-300 hover:bg-gray-700/50" id="booking-row-{{ $booking->id }}">
                        <td class="px-6 py-4">
                            #{{ $booking->id }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white">{{ $booking->user->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-400">{{ $booking->user->email ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white booking-room-name">{{ $booking->room->name ?? 'Room #' . ($booking->room_id ?? 'N/A') }}</div>
                            <div class="text-sm text-gray-400 booking-guests">{{ $booking->guests ?? 'N/A' }} guests</div>
                        </td>
                        <td class="px-6 py-4 booking-dates">
                            @if(isset($booking->check_in) && $booking->check_in && isset($booking->check_out) && $booking->check_out)
                            <div class="text-white">{{ $booking->check_in->format('M d, Y') }}</div>
                            <div class="text-sm text-gray-400">{{ $booking->check_in->format('l \a\t g:i A') }}</div>
                            <div class="text-white mt-1">{{ $booking->check_out->format('M d, Y') }}</div>
                            <div class="text-sm text-gray-400">{{ $booking->check_out->format('l \a\t g:i A') }}</div>
                            @else
                            <div class="text-gray-400">Date not set</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-green-400 font-bold text-lg booking-total">₱{{ number_format($booking->total_price ?? 0, 2) }}</div>
                            @if(isset($booking->check_in) && $booking->check_in && isset($booking->check_out) && $booking->check_out)
                            <div class="text-sm text-gray-400 booking-nights">
                                {{ $booking->check_in->diffInDays($booking->check_out) }} night(s)
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $status = $booking->status ?? 'pending';
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                @if($status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($status === 'confirmed') bg-green-100 text-green-800
                                @elseif($status === 'checked_in') bg-blue-100 text-blue-800
                                @elseif($status === 'checked_out') bg-gray-100 text-gray-800
                                @elseif($status === 'cancelled') bg-red-100 text-red-800
                                @elseif($status === 'completed') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <!-- View Button -->
                                <a href="{{ route('manager.bookings.show', $booking) }}" 
                                   class="text-blue-400 hover:text-blue-300 transition-colors"
                                   title="View Details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                
                                <!-- Edit Booking Details Button -->
                                <button type="button"
                                        onclick="editBookingDetails({{ json_encode($booking) }})"
                                        class="text-green-400 hover:text-green-300 transition-colors"
                                        title="Edit Booking Details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                
                                <!-- Status Update Button -->
                                <button type="button"
                                        onclick="updateStatus('{{ $booking->id }}')"
                                        class="text-yellow-400 hover:text-yellow-300 transition-colors"
                                        title="Update Status">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-lg font-medium mb-2">No bookings found</p>
                                <p class="text-sm text-gray-500 mb-4">Start by creating your first booking.</p>
                                <a href="{{ route('manager.bookings.create') }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                                    Create New Booking
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(isset($bookings) && method_exists($bookings, 'links'))
        <div class="px-6 py-3 bg-gray-900">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>

    <!-- Edit Booking Details Modal -->
    <div id="editBookingModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-white">Edit Booking Details</h3>
                        <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <form id="editBookingForm" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <!-- Guest Information (Read-only) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Guest Name</label>
                                <input type="text" id="edit_guest_name" readonly
                                       class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-gray-300 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Guest Email</label>
                                <input type="email" id="edit_guest_email" readonly
                                       class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-gray-300 cursor-not-allowed">
                            </div>
                        </div>

                        <!-- Room Selection -->
                        <div>
                            <label for="edit_room_id" class="block text-sm font-medium text-gray-300 mb-2">Room</label>
                            <select name="room_id" id="edit_room_id" required
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Room</option>
                                @php
                                    $rooms = \App\Models\Room::all();
                                @endphp
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" data-price="{{ $room->price }}">
                                        {{ $room->name }} - ₱{{ number_format($room->price, 2) }}/night ({{ $room->capacity }} guests)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Booking Dates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="edit_check_in" class="block text-sm font-medium text-gray-300 mb-2">Check-in Date & Time</label>
                                <input type="datetime-local" name="check_in" id="edit_check_in" required
                                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="edit_check_out" class="block text-sm font-medium text-gray-300 mb-2">Check-out Date & Time</label>
                                <input type="datetime-local" name="check_out" id="edit_check_out" required
                                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <!-- Number of Guests -->
                        <div>
                            <label for="edit_guests" class="block text-sm font-medium text-gray-300 mb-2">Number of Guests</label>
                            <input type="number" name="guests" id="edit_guests" min="1" max="20" required
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Special Requests -->
                        <div>
                            <label for="edit_special_requests" class="block text-sm font-medium text-gray-300 mb-2">Special Requests</label>
                            <textarea name="special_requests" id="edit_special_requests" rows="3"
                                      class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Any special requests or notes..."></textarea>
                        </div>

                        <!-- Total Price Display -->
                        <div class="bg-gray-700 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-300">Total Amount:</span>
                                <span id="edit_total_display" class="text-green-400 font-bold text-xl">₱0.00</span>
                            </div>
                            <div class="text-sm text-gray-400 mt-2">
                                <span id="edit_nights_display">0 nights</span> × <span id="edit_rate_display">₱0.00/night</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeEditModal()"
                                    class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-500 transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-500 transition-colors">
                                Update Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-white">Update Booking Status</h3>
                        <button type="button" onclick="closeStatusModal()" class="text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <form id="statusForm" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                            <select name="status" id="status"
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="checked_in">Checked In</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeStatusModal()"
                                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-500 transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500 transition-colors">
                                Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentBookingId = null;

// Edit Booking Details Function
function editBookingDetails(booking) {
    console.log('Editing booking:', booking);
    
    const modal = document.getElementById('editBookingModal');
    const form = document.getElementById('editBookingForm');
    
    currentBookingId = booking.id;
    
    // Set form action - use the update route with the booking ID
    form.action = `{{ url('manager/bookings') }}/${booking.id}`;
    
    // Add method spoofing for PUT request
    let methodField = form.querySelector('input[name="_method"]');
    if (!methodField) {
        methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        form.appendChild(methodField);
    }
    methodField.value = 'PUT';
    
    // Populate guest information (read-only)
    document.getElementById('edit_guest_name').value = booking.user?.name || 'N/A';
    document.getElementById('edit_guest_email').value = booking.user?.email || 'N/A';
    
    // Populate editable fields
    document.getElementById('edit_room_id').value = booking.room_id || '';
    document.getElementById('edit_guests').value = booking.guests || 1;
    document.getElementById('edit_special_requests').value = booking.special_requests || '';
    
    // Handle dates
    if (booking.check_in) {
        const checkIn = new Date(booking.check_in);
        document.getElementById('edit_check_in').value = checkIn.toISOString().slice(0, 16);
    }
    if (booking.check_out) {
        const checkOut = new Date(booking.check_out);
        document.getElementById('edit_check_out').value = checkOut.toISOString().slice(0, 16);
    }
    
    // Calculate and display total
    calculateEditTotal();
    
    // Show modal
    modal.classList.remove('hidden');
}

// Handle form submission with enhanced error handling
document.getElementById('editBookingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Updating...';
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.error('CSRF token not found!');
        showNotification('CSRF token missing. Please refresh the page.', 'error');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        return;
    }
    
    // Calculate total price and add to form data
    const roomSelect = document.getElementById('edit_room_id');
    const checkInInput = document.getElementById('edit_check_in');
    const checkOutInput = document.getElementById('edit_check_out');
    
    if (roomSelect.value && checkInInput.value && checkOutInput.value) {
        const selectedOption = roomSelect.options[roomSelect.selectedIndex];
        const roomPrice = parseFloat(selectedOption.dataset.price) || 0;
        
        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);
        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        
        if (nights > 0) {
            const total = nights * roomPrice;
            formData.append('total_price', total);
        }
    }
    
    console.log('Form action:', form.action);
    console.log('CSRF Token:', csrfToken);
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Error response:', text);
                throw new Error(`HTTP error! status: ${response.status}`);
            });
        }
        
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            return response.text().then(text => {
                console.error('Non-JSON response:', text);
                throw new Error('Server returned non-JSON response');
            });
        }
    })
    .then(data => {
        console.log('Success response:', data);
        
        if (data.success) {
            showNotification('Booking updated successfully!', 'success');
            closeEditModal();
            
            // Update the row in the table instead of reloading
            updateBookingRow(currentBookingId, data.booking);
            
            // Or reload the page to reflect changes
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Update failed');
        }
    })
    .catch(error => {
        console.error('Complete error details:', error);
        showNotification('Failed to update booking: ' + error.message, 'error');
    })
    .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
});

// Show notification function
function showNotification(message, type = 'success') {
    // Remove any existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 z-50 px-6 py-4 rounded-lg text-white transition-all duration-300 ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success' 
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'
                }
            </svg>
            ${message}
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Calculate total for edit form
function calculateEditTotal() {
    const roomSelect = document.getElementById('edit_room_id');
    const checkInInput = document.getElementById('edit_check_in');
    const checkOutInput = document.getElementById('edit_check_out');
    const totalDisplay = document.getElementById('edit_total_display');
    const nightsDisplay = document.getElementById('edit_nights_display');
    const rateDisplay = document.getElementById('edit_rate_display');
    
    if (roomSelect.value && checkInInput.value && checkOutInput.value) {
        const selectedOption = roomSelect.options[roomSelect.selectedIndex];
        const roomPrice = parseFloat(selectedOption.dataset.price) || 0;
        
        const checkIn = new Date(checkInInput.value);
        const checkOut = new Date(checkOutInput.value);
        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        
        if (nights > 0) {
            const total = nights * roomPrice;
            totalDisplay.textContent = `₱${total.toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
            nightsDisplay.textContent = `${nights} night${nights > 1 ? 's' : ''}`;
            rateDisplay.textContent = `₱${roomPrice.toLocaleString('en-US', { minimumFractionDigits: 2 })}/night`;
        }
    }
}

// Add event listeners for real-time calculation
document.getElementById('edit_room_id').addEventListener('change', calculateEditTotal);
document.getElementById('edit_check_in').addEventListener('change', calculateEditTotal);
document.getElementById('edit_check_out').addEventListener('change', calculateEditTotal);

function closeEditModal() {
    document.getElementById('editBookingModal').classList.add('hidden');
}

// Status update functions
function updateStatus(bookingId) {
    const modal = document.getElementById('statusModal');
    const form = document.getElementById('statusForm');
    form.action = `{{ url('manager/bookings') }}/${bookingId}/status`;
    modal.classList.remove('hidden');
}

function closeModal() {
    const modal = document.getElementById('statusModal');
    modal.classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Show notification function
function showNotification(message, type = 'success') {
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 z-50 px-6 py-4 rounded-lg text-white transition-all duration-300 ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success' 
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'
                }
            </svg>
            ${message}
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}
</script>
@endpush
