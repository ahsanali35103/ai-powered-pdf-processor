<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        \App\Providers\ResponseServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: [
            __DIR__ . '/../routes/auth.php',
            __DIR__ . '/../routes/file.php',
            __DIR__ . '/../routes/service.php',
        ],
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register middleware aliases
        $middleware->alias([
            // Auth middleware
            'check.user.exists' => \App\Http\Middleware\Auth\CheckUserExistMiddleware::class,
            'check.active' => \App\Http\Middleware\Auth\CheckActiveMiddleware::class,
            'check.credentials' => \App\Http\Middleware\Auth\CheckCredentialMiddleware::class,
            'check.token' => \App\Http\Middleware\Auth\CheckTokenMiddleware::class,
            'check.user.exists.forgot' => \App\Http\Middleware\Auth\CheckUserExistForForgetMiddleware::class,
            'check.validation' => \App\Http\Middleware\CheckValidationMiddleware::class,
            'check.verify.signup' => \App\Http\Middleware\Auth\CheckVerifySignupMiddleware::class,

            // File middleware
            'file.upload' => \App\Http\Middleware\File\FileUploadMiddleware::class,
            'file.delete' => \App\Http\Middleware\File\FileDeleteMiddleware::class,

            // Service middleware
            'service.create' => \App\Http\Middleware\Service\ServiceCreateMiddleware::class,
            'service.read' => \App\Http\Middleware\Service\ServiceReadMiddleware::class,
            'service.list' => \App\Http\Middleware\Service\ServiceListMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Global exception handling for API routes
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            // Only handle API routes and JSON requests
            if ($request->is('api/*') || $request->expectsJson()) {

                // Handle validation exceptions
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return response()->validation($e->errors(), 'Validation failed', 400);
                }

                // Handle authentication exceptions
                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    return response()->unauthorized('Authentication required', 401);
                }

                // Handle authorization exceptions
                if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    return response()->forbidden('Access denied', 403);
                }

                // Handle model not found exceptions
                if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                    return response()->notFound('Resource not found', 404);
                }

                // Handle method not allowed exceptions
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
                    return response()->error('Method not allowed', 405);
                }

                // Handle not found exceptions
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    return response()->notFound('Endpoint not found', 404);
                }

                // Get status code
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

                // Handle general exceptions
                $message = $e->getMessage() ?: 'Internal server error';

                // Don't expose sensitive error details in production
                if (app()->environment('production') && $status === 500) {
                    $message = 'Internal server error';
                }

                return response()->error($message, $status);
            }
        });
    })->create();
