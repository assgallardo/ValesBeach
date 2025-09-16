<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - Vales Beach Resort</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            color: white;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            max-width: 600px;
            padding: 2rem;
            background: rgba(31, 41, 55, 0.8);
            border-radius: 1rem;
            border: 2px solid #374151;
            text-align: center;
        }
        h1 {
            color: #ef4444;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        p {
            font-size: 1.1rem;
            color: #d1d5db;
            margin-bottom: 2rem;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #059669;
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #047857;
        }
        .error-details {
            background: #374151;
            border-radius: 0.5rem;
            padding: 1rem;
            margin: 1rem 0;
            text-align: left;
            font-family: monospace;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>500</h1>
        <h2>Server Error</h2>
        <p>Sorry, something went wrong on our server. We're working to fix this issue.</p>
        
        @if(app()->environment('local') && isset($exception))
            <div class="error-details">
                <strong>Error:</strong> {{ $exception->getMessage() }}<br>
                <strong>File:</strong> {{ $exception->getFile() }}<br>
                <strong>Line:</strong> {{ $exception->getLine() }}
            </div>
        @endif
        
        <a href="{{ url('/') }}" class="btn">Go to Homepage</a>
    </div>
</body>
</html>

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
