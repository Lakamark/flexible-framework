<?php

/**
 * The main configuration for your application.
 */

use FlexibleFramework\Router;

use function DI\autowire;

return [
    Router::class => autowire(),
];
