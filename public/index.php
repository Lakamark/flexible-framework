<?php

chdir(dirname(__DIR__));

require "vendor/autoload.php";

use App\Admin\AdminModule;
use App\Blog\BlogModule;
use FlexibleFramework\Kernel;
use FlexibleFramework\Middleware\DispatcherMiddleware;
use FlexibleFramework\Middleware\MethodMiddleware;
use FlexibleFramework\Middleware\NotFoundMiddleware;
use FlexibleFramework\Middleware\RendererRequestMiddleware;
use FlexibleFramework\Middleware\RouterMiddleware;
use FlexibleFramework\Middleware\TrailingSlashMiddleware;
use Franzl\Middleware\Whoops\WhoopsMiddleware;
use GuzzleHttp\Psr7\ServerRequest;

use function Http\Response\send;

$kernel = (new Kernel([
    dirname(__DIR__) . '/config/app.php',
    dirname(__DIR__) . '/config/template.php',
    dirname(__DIR__) . '/config/database.php',
]))
    ->addModule(AdminModule::class)
    ->addModule(BlogModule::class);

$kernel
    ->pipe(new WhoopsMiddleware())
    ->pipe(TrailingSlashMiddleware::class)
    ->pipe(RendererRequestMiddleware::class)
    ->pipe(MethodMiddleware::class)
    ->pipe(RouterMiddleware::class)
    ->pipe(DispatcherMiddleware::class)
    ->pipe(NotFoundMiddleware::class)
;

if (php_sapi_name() !== "cli") {
    $response = $kernel->run(ServerRequest::fromGlobals());
    send($response);
}
