<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckUserRole::class,
            'user.status' => \App\Http\Middleware\CheckUserStatus::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, $request) {
            // Always return raw HTML to avoid view compilation issues
            $errorHtml = '
<!DOCTYPE html>
<html>
<head>
    <title>Server Error</title>
    <style>
        body { font-family: Arial, sans-serif; background: #1f2937; color: white; margin: 0; padding: 40px; }
        .container { max-width: 800px; margin: 0 auto; background: #374151; padding: 30px; border-radius: 8px; }
        h1 { color: #ef4444; margin-bottom: 20px; }
        .error { background: #111827; padding: 15px; border-radius: 5px; margin: 20px 0; }
        pre { white-space: pre-wrap; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Server Error</h1>
        <p>An error occurred while processing your request.</p>';
        
            if (app()->environment('local')) {
                $errorHtml .= '
        <div class="error">
            <strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '<br>
            <strong>File:</strong> ' . htmlspecialchars($e->getFile()) . '<br>
            <strong>Line:</strong> ' . $e->getLine() . '
        </div>';
            }
            
            $errorHtml .= '
        <a href="/" style="color: #10b981; text-decoration: none;">← Go Home</a>
    </div>
</body>
</html>';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'An error occurred.',
                    'error' => app()->environment('local') ? $e->getMessage() : 'Server Error'
                ], 500);
            }
            
            return response($errorHtml, 500);
        });
    })->create();
