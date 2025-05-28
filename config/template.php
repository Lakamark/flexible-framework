<?php

/**
 * The main configuration for your views and your renderer system.
 */

use App\FlexibleFramework\Renderer\TwigRendererFactory;
use App\FlexibleFramework\Router\RouterTwigExtension;
use FlexibleFramework\Renderer\RendererInterface;

use function DI\factory;
use function DI\get;

return [
    'templates.path' => dirname(__DIR__) . '/templates',
    'twig.extensions' => [
        get(RouterTwigExtension::class),
    ],
    RendererInterface::class => factory(new TwigRendererFactory()),
];
