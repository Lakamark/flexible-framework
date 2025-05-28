<?php

chdir(dirname(__DIR__));

require "vendor/autoload.php";

use FlexibleFramework\Kernel;
use GuzzleHttp\Psr7\ServerRequest;

use function Http\Response\send;

$modules = [
    \App\Blog\BlogModule::class,
];

$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__) . '/config/app.php');
$builder->addDefinitions(dirname(__DIR__) . '/config/template.php');
$builder->addDefinitions(dirname(__DIR__) . '/config/database.php');

foreach ($modules as $module) {
    if ($module::DEFINITIONS) {
        $builder->addDefinitions($module::DEFINITIONS);
    }
}

$container = $builder->build();

$kernel = new Kernel($container, $modules);

if (php_sapi_name() !== "cli") {
    $response = $kernel->run(ServerRequest::fromGlobals());
    send($response);
}
