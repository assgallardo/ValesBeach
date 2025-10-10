@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 lg:px-16 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-green-50 mb-2">Edit Service Request</h1>
                <p class="text-green-200">Update service request details and assignment.</p>
            </div>
            <a href="{{ route('manager.staff-assignment.index') }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-gray-800 rounded-xl shadow-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700">
            <h2 class="text-xl font-semibold text-green-50">Service Request Details</h2>
        </div>

        <form action="{{ route('manager.staff-assignment.update', $serviceRequest) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Service Type Dropdown -->
                    <div>
                        <label for="service_type" class="block text-gray-300 mb-2">Service Type</label>
                        <select name="service_type" id="service_type" required class="w-full bg-gray-700 text-green-100 rounded-lg p-3">
                            <option value="">Select Service Type</option>
                            @foreach($availableServices as $service)
                            <option value="{{ $service->name }}" 
                                    {{ old('service_type', $serviceRequest->service_type) === $service->name ? 'selected' : '' }}>
                                {{ $service->name }} - ${{ $service->price }}
                            </option>
                            @endforeach
                        </select>
                        @error('service_type')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-gray-300 mb-2">Description</label>
                        <textarea name="description" id="description" rows="4" required
                                  class="w-full bg-gray-700 text-green-100 rounded-lg p-3">{{ old('description', $serviceRequest->description) }}</textarea>
                        @error('description')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div>
                        <label for="priority" class="block text-gray-300 mb-2">Priority</label>
                        <select name="priority" id="priority" required class="w-full bg-gray-700 text-green-100 rounded-lg p-3">
                            <option value="low" {{ old('priority', $serviceRequest->priority) === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', $serviceRequest->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority', $serviceRequest->priority) === 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ old('priority', $serviceRequest->priority) === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        @error('priority')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-gray-300 mb-2">Status</label>
                        <select name="status" id="status" required class="w-full bg-gray-700 text-green-100 rounded-lg p-3">
                            <option value="pending" {{ old('status', $serviceRequest->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ old('status', $serviceRequest->status) === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="assigned" {{ old('status', $serviceRequest->status) === 'assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="in_progress" {{ old('status', $serviceRequest->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status', $serviceRequest->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $serviceRequest->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Assigned To -->
                    <div>
                        <label for="assigned_to" class="block text-gray-300 mb-2">Assigned To</label>
                        <select name="assigned_to" id="assigned_to" class="w-full bg-gray-700 text-green-100 rounded-lg p-3">
                            <option value="">Unassigned</option>
                            @foreach($availableStaff as $staff)
                            <option value="{{ $staff->id }}" 
                                    {{ old('assigned_to', $serviceRequest->assigned_to) == $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Guests Count -->
                    <div>
                        <label for="guests_count" class="block text-gray-300 mb-2">Number of Guests</label>
                        <input type="number" name="guests_count" id="guests_count" min="1"
                               value="{{ old('guests_count', $serviceRequest->guests_count ?? $serviceRequest->guests ?? 1) }}"
                               class="w-full bg-gray-700 text-green-100 rounded-lg p-3">
                        @error('guests_count')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deadline -->
                    <div>
                        <label for="deadline" class="block text-gray-300 mb-2">Deadline</label>
                        <input type="datetime-local" name="deadline" id="deadline"
                               value="{{ old('deadline', $serviceRequest->deadline ? $serviceRequest->deadline->format('Y-m-d\TH:i') : '') }}"
                               class="w-full bg-gray-700 text-green-100 rounded-lg p-3">
                        @error('deadline')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estimated Duration -->
                    <div>
                        <label for="estimated_duration" class="block text-gray-300 mb-2">Estimated Duration</label>
                        <select name="estimated_duration" id="estimated_duration" class="w-full bg-gray-700 text-green-100 rounded-lg p-3">
                            <option value="">No estimate</option>
                            <option value="15" {{ old('estimated_duration', $serviceRequest->estimated_duration) == 15 ? 'selected' : '' }}>15 minutes</option>
                            <option value="30" {{ old('estimated_duration', $serviceRequest->estimated_duration) == 30 ? 'selected' : '' }}>30 minutes</option>
                            <option value="60" {{ old('estimated_duration', $serviceRequest->estimated_duration) == 60 ? 'selected' : '' }}>1 hour</option>
                            <option value="90" {{ old('estimated_duration', $serviceRequest->estimated_duration) == 90 ? 'selected' : '' }}>1.5 hours</option>
                            <option value="120" {{ old('estimated_duration', $serviceRequest->estimated_duration) == 120 ? 'selected' : '' }}>2 hours</option>
                            <option value="240" {{ old('estimated_duration', $serviceRequest->estimated_duration) == 240 ? 'selected' : '' }}>4 hours</option>
                            <option value="480" {{ old('estimated_duration', $serviceRequest->estimated_duration) == 480 ? 'selected' : '' }}>8 hours</option>
                        </select>
                        @error('estimated_duration')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Full Width Fields -->
            <div class="mt-6">
                <!-- Manager Notes -->
                <div>
                    <label for="manager_notes" class="block text-gray-300 mb-2">Manager Notes</label>
                    <textarea name="manager_notes" id="manager_notes" rows="4"
                              placeholder="Add notes for staff members..."
                              class="w-full bg-gray-700 text-green-100 rounded-lg p-3">{{ old('manager_notes', $serviceRequest->manager_notes) }}</textarea>
                    @error('manager_notes')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('manager.staff-assignment.index') }}" 
                   class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Update Service Request
                </button>
            </div>
        </form>
    </div>

    <!-- Request Information -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Guest Information -->
        <div class="bg-gray-800 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-green-50 mb-4">Guest Information</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-400">Guest:</span>
                    <span class="text-green-100">{{ $serviceRequest->guest->name ?? $serviceRequest->guest_name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Email:</span>
                    <span class="text-green-100">{{ $serviceRequest->guest->email ?? $serviceRequest->guest_email ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Room:</span>
                    <span class="text-green-100">{{ $serviceRequest->room->name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Request Timeline -->
        <div class="bg-gray-800 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-green-50 mb-4">Timeline</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-400">Requested:</span>
                    <span class="text-green-100">{{ $serviceRequest->created_at->format('M d, Y H:i') }}</span>
                </div>
                @if($serviceRequest->assigned_at)
                <div class="flex justify-between">
                    <span class="text-gray-400">Assigned:</span>
                    <span class="text-green-100">{{ $serviceRequest->assigned_at->format('M d, Y H:i') }}</span>
                </div>
                @endif
                @if($serviceRequest->completed_at)
                <div class="flex justify-between">
                    <span class="text-gray-400">Completed:</span>
                    <span class="text-green-100">{{ $serviceRequest->completed_at->format('M d, Y H:i') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection