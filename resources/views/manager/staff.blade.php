@extends('layouts.admin')

@section('content')
    <main class="relative z-10 py-8 lg:py-16">
        <div class="container mx-auto px-4 lg:px-16">
            <!-- Page Header -->
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50 mb-4">
                    Staff Management
                </h2>
                <p class="text-green-50 opacity-80 text-lg">
                    Manage staff accounts and permissions
                </p>
                <div class="mt-6">
                    <a href="{{ route('manager.dashboard') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Staff Management Content -->
            <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-green-50">Staff Accounts</h3>
                    <div class="text-green-300">
                        Total Staff: {{ $staff->total() }}
                    </div>
                </div>

                <!-- Staff Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-green-50">
                        <thead class="bg-green-800/50">
                            <tr>
                                <th class="px-4 py-3 text-left">Name</th>
                                <th class="px-4 py-3 text-left">Email</th>
                                <th class="px-4 py-3 text-left">Phone</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Joined</th>
                                <th class="px-4 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-green-700/30">
                            @forelse($staff as $member)
                            <tr class="hover:bg-green-800/30">
                                <td class="px-4 py-3">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-red-600 flex items-center justify-center mr-3">
                                            <span class="text-white font-medium text-sm">{{ substr($member->name, 0, 1) }}</span>
                                        </div>
                                        {{ $member->name }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ $member->email }}</td>
                                <td class="px-4 py-3">{{ $member->phone ?? 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 bg-green-600 text-green-100 rounded text-sm">
                                        {{ ucfirst($member->status ?? 'active') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $member->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs transition-colors">
                                            View
                                        </button>
                                        <button class="bg-yellow-600 hover:bg-yellow-700 text-white px-2 py-1 rounded text-xs transition-colors">
                                            Edit
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-green-300">
                                    No staff found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $staff->links() }}
                </div>
            </div>
        </div>
    </main>
@endsection