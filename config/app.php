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
    Router::class => autowire(),
    SessionInterface::class => create(PHPSession::class),
];
