<?php

/**
 * The main configuration for your application.
 */

use FlexibleFramework\Router;
use FlexibleFramework\Session\PHPSession;
use FlexibleFramework\Session\SessionInterface;

use function DI\autowire;
use function DI\create;

return [
    'project.directory' => dirname(__DIR__),
    'app.environment' => getenv('APP_ENV') ?: 'prod',
    'session.driver' => 'php',
];
