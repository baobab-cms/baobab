<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Derrière un reverse proxy qui termine le TLS (Cloudflare, HAProxy, load
        // balancer d'hébergeur), décommenter ce bloc : sans confiance explicite dans
        // X-Forwarded-Proto, Laravel croit chaque requête non sécurisée et génère des
        // URLs absolues en http:// (route(), url()), ce qui bloque la page en
        // mixed-content côté navigateur.
        //
        // Renseigner les adresses réelles du proxy plutôt que '*' : trusted proxies
        // à '*' laisse n'importe quel client falsifier son IP via X-Forwarded-For,
        // ce que le journal d'audit et le throttling de Baobab prennent pour argent
        // comptant.
        //
        // $middleware->trustProxies(
        //     at: ['192.0.2.1'],
        //     headers: Request::HEADER_X_FORWARDED_FOR
        //         | Request::HEADER_X_FORWARDED_HOST
        //         | Request::HEADER_X_FORWARDED_PORT
        //         | Request::HEADER_X_FORWARDED_PROTO,
        // );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
