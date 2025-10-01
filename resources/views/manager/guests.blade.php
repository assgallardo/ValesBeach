@extends('layouts.admin')

@section('content')
<main class="relative z-10 py-8 lg:py-16">
    <div class="container mx-auto px-4 lg:px-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-green-50 mb-4">
                Guests Management
            </h2>
            <p class="text-green-50 opacity-80 text-lg">
                View and manage guest information and booking history.
            </p>
            <div class="mt-6">
                <a href="{{ route('manager.dashboard') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors duration-200">
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Guest List -->
        <div class="bg-green-900/50 backdrop-blur-sm rounded-lg p-6 mb-8">
            <h3 class="text-xl font-bold text-green-50 mb-4">All Guests</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-green-50">
                    <thead>
                        <tr class="border-b border-green-700">
                            <th class="text-left py-3">Name</th>
                            <th class="text-left py-3">Email</th>
                            <th class="text-left py-3">Phone</th>
                            <th class="text-left py-3">Status</th>
                            <th class="text-left py-3">Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($guests as $guest)
                        <tr class="border-b border-green-800">
                            <td class="py-3">{{ $guest->name }}</td>
                            <td class="py-3">{{ $guest->email }}</td>
                            <td class="py-3">{{ $guest->phone ?? 'N/A' }}</td>
                            <td class="py-3">
                                <span class="px-2 py-1 bg-green-600 text-green-100 rounded text-sm">
                                    {{ ucfirst($guest->status ?? 'active') }}
                                </span>
                            </td>
                            <td class="py-3">{{ $guest->created_at->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-green-300">No guests found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($guests->hasPages())
        <div class="flex justify-center">
            {{ $guests->links() }}
        </div>
        @endif
    </div>
</main>
@endsection