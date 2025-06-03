<?php

namespace App\Blog;

use App\Blog\Actions\CategoryCrudAction;
use App\Blog\Actions\CategoryShowAction;
use App\Blog\Actions\PostCrudAction;
use App\Blog\Actions\PostIndexAction;
use App\Blog\Actions\PostShowAction;
use FlexibleFramework\AbstractModule;
use FlexibleFramework\Renderer\RendererInterface;
use FlexibleFramework\Router;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class BlogModule extends AbstractModule
{
    public const string DEFINITIONS = __DIR__ . '/config/config.php';

    public const string MIGRATIONS = __DIR__ . '/db/migrations';

    public const string SEEDS = __DIR__ . '/db/seeds';

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __construct(
        ContainerInterface $container,
    ) {
        $blogPrefix = $container->get('blog.prefix');
        $router = $container->get(Router::class);
        $container->get(RendererInterface::class)->addPath('blog', __DIR__ . '/templates');
        $router->get($blogPrefix, PostIndexAction::class, 'blog.index');
        $router->get($blogPrefix . '/{slug:[a-z0-9\-]+}-{id:[0-9]+}', PostShowAction::class, 'blog.show');
        $router->get($blogPrefix . '/category/{slug:[a-z0-9\-]+}', CategoryShowAction::class, 'blog.category');

        if ($container->has('admin.prefix')) {
            $prefix = $container->get('admin.prefix');
            $router->crud("$prefix/posts", PostCrudAction::class, 'blog.admin');
            $router->crud("$prefix/categories", CategoryCrudAction::class, 'blog.category.admin');
        }
    }
}
