<?php

namespace App\Blog\Actions;

use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use FlexibleFramework\Actions\RouterAware;
use FlexibleFramework\Renderer\RendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostIndexAction
{
    use RouterAware;

    public function __construct(
        private readonly RendererInterface $renderer,
        private readonly PostTable $postTable,
        private readonly CategoryTable $categoryTable,
    ) {}

    public function __invoke(Request $request): string|ResponseInterface
    {
        $params = $request->getQueryParams();
        $posts = $this->postTable->findPaginatedPublic(12, $params['p'] ?? 1);
        $categories = $this->categoryTable->findAll();
        $page = $params['p'] ?? 1;
        return $this->renderer->render('@blog/index', [
            'posts' => $posts,
            'categories' => $categories,
            'page' => $page,
        ]);
    }
}
