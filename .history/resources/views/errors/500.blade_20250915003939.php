@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 lg:px-8 py-8">
    <div class="bg-gray-800 rounded-lg p-6">
        <h1 class="text-2xl font-bold text-white mb-4">Server Error</h1>
        
        @if(app()->environment('local'))
            <div class="bg-gray-700 p-4 rounded-lg mb-4">
                <h2 class="text-xl text-white mb-2">Error Details</h2>
                @if(isset($exception))
                    <div class="text-gray-300">
                        {{ $exception->getMessage() }}
                    </div>
                @endif
            </div>

            @if(isset($exception))
                <!-- Request Information -->
                <div class="bg-gray-700 p-4 rounded-lg mb-4">
                    <h3 class="text-lg text-white mb-2">Request Headers</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-gray-300">
                            <thead>
                                <tr>
                                    <th class="text-left py-2 px-4">Header</th>
                                    <th class="text-left py-2 px-4">Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($exception->requestHeaders() as $key => $value)
                                    <tr>
                                        <td class="py-2 px-4 border-t border-gray-600">{{ $key }}</td>
                                        <td class="py-2 px-4 border-t border-gray-600">{{ $value }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="py-2 px-4 border-t border-gray-600">No headers available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @else
            <p class="text-gray-300">
                An error occurred while processing your request. Please try again later or contact support if the problem persists.
            </p>
        @endif

        <div class="mt-6">
            <a href="{{ url()->previous() }}" class="inline-flex items-center text-gray-300 hover:text-white">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Go Back
            </a>
        </div>
    </div>
</div>
@endsection
