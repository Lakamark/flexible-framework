<?php

/**
 * The main configuration for your views and your renderer system.
 */

use FlexibleFramework\Renderer\TwigRendererFactory;
use FlexibleFramework\Router\RouterTwigExtension;
use FlexibleFramework\Renderer\RendererInterface;
use FlexibleFramework\Twig\FlashTwigExtension;
use FlexibleFramework\Twig\PagerFantaExtension;
use FlexibleFramework\Twig\TextTwigExtension;
use FlexibleFramework\Twig\TimeTwigExtension;

use function DI\factory;
use function DI\get;

return [
    'templates.path' => dirname(__DIR__) . '/templates',
    'twig.extensions' => [
        get(RouterTwigExtension::class),
        get(PagerFantaExtension::class),
        get(TimeTwigExtension::class),
        get(TextTwigExtension::class),
        get(FlashTwigExtension::class),
    ],
    RendererInterface::class => factory(new TwigRendererFactory()),
];
