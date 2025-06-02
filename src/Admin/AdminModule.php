<?php

namespace App\Admin;

use FlexibleFramework\AbstractModule;
use FlexibleFramework\Renderer\RendererInterface;

class AdminModule extends AbstractModule
{
    public const string DEFINITIONS =  __DIR__ . '/config/config.php';

    public function __construct(
        private readonly RendererInterface $renderer,
    ) {
        $renderer->addPath('admin', __DIR__ . '/templates');
    }
}
