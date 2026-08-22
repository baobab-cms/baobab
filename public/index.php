<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Garde pré-vol (spec 15 §2.1) : vérifie la version de PHP AVANT de charger
// quoi que ce soit. Sans elle, un hébergement servant un PHP trop ancien
// rendrait une erreur d'analyse opaque au lieu d'un message actionnable.
// Ce fichier et celui qu'il inclut sont les seuls du projet à devoir rester
// analysables par un PHP 5 — ne pas y introduire de syntaxe moderne.
require __DIR__.'/../bootstrap/preflight.php';

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
