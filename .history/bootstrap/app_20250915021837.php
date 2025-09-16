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
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                $statusCode = $e->getStatusCode();
                
                // Use custom error views if they exist
                if (view()->exists("errors.{$statusCode}")) {
                    return response()->view("errors.{$statusCode}", [
                        'exception' => $e
                    ], $statusCode);
                }
            }
            
            // For any other exceptions, use a generic error page
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'An error occurred.',
                    'error' => app()->environment('local') ? $e->getMessage() : 'Server Error'
                ], 500);
            }
            
            return response()->view('errors.500', [
                'exception' => $e
            ], 500);
        });
    })->create();
