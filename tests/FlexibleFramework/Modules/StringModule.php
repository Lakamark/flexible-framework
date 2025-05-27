<?php

namespace Tests\FlexibleFramework\Modules;

use FlexibleFramework\Router;

class StringModule
{
    public function __construct(Router $router)
    {
        $router->get('/demo', function () {
            return "DEMO";
        }, 'demo');
    }
}
