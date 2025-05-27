<?php

chdir(dirname(__DIR__));

require "vendor/autoload.php";

use FlexibleFramework\Kernel;
use GuzzleHttp\Psr7\ServerRequest;

use function Http\Response\send;

$kernel = new Kernel([]);

if (php_sapi_name() !== "cli") {
    $response = $kernel->run(ServerRequest::fromGlobals());
    send($response);
}
