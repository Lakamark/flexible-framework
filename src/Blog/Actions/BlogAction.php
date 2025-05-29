<?php

namespace App\Blog\Actions;

use App\Blog\Table\PostTable;
use FlexibleFramework\Actions\RouterAware;
use FlexibleFramework\Renderer\RendererInterface;
use FlexibleFramework\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogAction
{
    use RouterAware;

    public function __construct(
        private readonly RendererInterface $renderer,
        private readonly Router $router,
        private readonly PostTable $postTable,
    ) {}

    public function __invoke(Request $request)
    {
        if ($request->getAttribute('id')) {
            return $this->show($request);
        }
        return $this->index($request);
    }

    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $posts = $this->postTable->findPaginated(12, $params['p'] ?? 1);
        return $this->renderer->render('@blog/index', ['posts' => $posts]);
    }

    /**
     * Show an article
     *
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function show(Request $request): string|ResponseInterface
    {
        $slug = $request->getAttribute('slug');

        $post = $this->postTable->find($request->getAttribute('id'));

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
