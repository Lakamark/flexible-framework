<?php

namespace Tests\FlexibleFramework\Modules;

use FlexibleFramework\Router;

class ErroredModule
{
    public function __construct(Router $router)
    {
        $router->get('/demo', function () {
            return new \stdClass();
        }, 'demo');
    }
}
