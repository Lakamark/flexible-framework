<?php

namespace App\Blog;

use App\Blog\Actions\AdminBlogAction;
use App\Blog\Actions\BlogAction;
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
        $router->get($blogPrefix, BlogAction::class, 'blog.index');
        $router->get($blogPrefix . '/{slug:[a-z0-9\-]+}-{id:[0-9]+}', BlogAction::class, 'blog.show');

        if ($container->has('admin.prefix')) {
            $prefix = $container->get('admin.prefix');
            $router->crud("$prefix/posts", AdminBlogAction::class, 'post.admin');
        }
    }
}
