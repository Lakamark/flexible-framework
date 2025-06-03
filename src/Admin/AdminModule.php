<?php

namespace App\Admin;

use App\Admin\Actions\DashboardAction;
use FlexibleFramework\AbstractModule;
use FlexibleFramework\Renderer\RendererInterface;
use FlexibleFramework\Renderer\TwigRenderer;
use FlexibleFramework\Router;

class AdminModule extends AbstractModule
{
    public const string DEFINITIONS =  __DIR__ . '/config/config.php';

    public function __construct(
        private readonly RendererInterface $renderer,
        private readonly Router $router,
        AdminTwigExtension $adminTwigExtension,
        string $prefix = 'admin'
    ) {
        $renderer->addPath('admin', __DIR__ . '/templates');
        $this->router->get($prefix, DashboardAction::class, 'admin');

        if ($this->renderer instanceof TwigRenderer) {
            $this->renderer->getTwig()->addExtension($adminTwigExtension);
        }
    }
}
