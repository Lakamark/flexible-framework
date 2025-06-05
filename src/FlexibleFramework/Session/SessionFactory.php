<?php

namespace App\FlexibleFramework\Session;

use FlexibleFramework\Session\ArraySession;
use FlexibleFramework\Session\PHPSession;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class SessionFactory
{
    /**
     * Accord with the app configuration,
     * the user can define the session driver via app.config.
     * If you find any drive available, we use a simple array session.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): PHPSession|ArraySession
    {
        if ($container->get('session.driver') === 'php') {
            return new PHPSession();
        }
        return new ArraySession();
    }
}
