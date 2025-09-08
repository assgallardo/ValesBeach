@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 lg:px-16 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold text-white mb-6">Create New User</h2>

            @if($errors->any())
            <div class="bg-red-600 text-white p-4 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400"
                           placeholder="Enter user's full name">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400"
                           placeholder="Enter user's email">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400"
                           placeholder="Enter password">
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400"
                           placeholder="Confirm password">
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-300 mb-2">Role</label>
                    <select id="role" name="role" required
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                        <option value="guest" {{ old('role') == 'guest' ? 'selected' : '' }}>Guest</option>
                        <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                    <select id="status" name="status" required
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="blocked" {{ old('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.users') }}" 
                       class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
