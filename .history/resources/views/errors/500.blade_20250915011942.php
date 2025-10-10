@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 lg:px-8 py-8">
    <div class="bg-gray-800 rounded-lg p-6">
        <h1 class="text-2xl font-bold text-white mb-4">Server Error</h1>
        
        @if(app()->environment('local') && isset($exception))
            <div class="bg-gray-700 p-4 rounded-lg mb-4">
                <h2 class="text-xl text-white mb-2">Error Details</h2>
                <div class="text-gray-300">
                    <strong>Message:</strong> {{ $exception->getMessage() }}<br>
                    <strong>File:</strong> {{ $exception->getFile() }}<br>
                    <strong>Line:</strong> {{ $exception->getLine() }}
                </div>
            </div>

            <!-- Request Information -->
            <div class="bg-gray-700 p-4 rounded-lg mb-4">
                <h3 class="text-lg text-white mb-2">Request Information</h3>
                <div class="text-gray-300">
                    <strong>Method:</strong> {{ request()->method() ?? 'Unknown' }}<br>
                    <strong>URL:</strong> {{ request()->url() ?? 'Unknown' }}<br>
                    <strong>IP:</strong> {{ request()->ip() ?? 'Unknown' }}
                </div>
            </div>
        @else
            <p class="text-gray-300">
                An error occurred while processing your request. Please try again later or contact support if the problem persists.
            </p>
        @endif

        <div class="mt-6">
            <a href="{{ url('/') }}" class="inline-flex items-center text-gray-300 hover:text-white">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Go Home
            </a>
        </div>
    </div>
</div>
@endsection

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
