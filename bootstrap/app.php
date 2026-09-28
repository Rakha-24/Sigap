<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => CheckRole::class,
        ]);

        // Vercel memutus TLS di edge; peramban tetap bertukar paket dengan
        // protokol HTTPS lewat X-Forwarded-Proto. Tanpa trust proxy di sini,
        // Laravel mengira semua request datang HTTP → URL "back()" dan arah
        // redirect kembali jadi http:// (memicu lompatan 308).
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
