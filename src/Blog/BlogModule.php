<?php

namespace App\Blog;

use App\Blog\Actions\BlogAction;
use FlexibleFramework\AbstractModule;
use FlexibleFramework\Renderer\RendererInterface;
use FlexibleFramework\Router;

class BlogModule extends AbstractModule
{
    public const string DEFINITIONS = __DIR__ . '/config/config.php';

    public function __construct(
        private readonly string $prefix,
        private readonly Router $router,
        private readonly RendererInterface $renderer,
    ) {
        $this->renderer->addPath('blog', __DIR__ . '/templates');
        $this->router->get($this->prefix, BlogAction::class, 'blog.index');
        $this->router->get($this->prefix . '/{slug:[a-z0-9\-]+}', BlogAction::class, 'blog.show');
    }
}
