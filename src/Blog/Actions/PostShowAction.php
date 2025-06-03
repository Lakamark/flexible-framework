<?php

namespace App\Blog\Actions;

use App\Blog\Table\PostTable;
use FlexibleFramework\Actions\RouterAware;
use FlexibleFramework\Renderer\RendererInterface;
use FlexibleFramework\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostShowAction
{
    use RouterAware;

    public function __construct(
        private readonly RendererInterface $renderer,
        private readonly Router $router,
        private readonly PostTable $postTable,
    ) {}

    public function __invoke(Request $request): string|ResponseInterface
    {
        $slug = $request->getAttribute('slug');

        $post = $this->postTable->findWithCategory($request->getAttribute('id'));

        if ($post->slug !== $slug) {
            return $this->redirect('blog.show', [
                'id' => $post->id,
                'slug' => $post->slug,
            ]);
        }

        return $this->renderer->render('@blog/show', [
            'post' => $post,
        ]);
    }
}
