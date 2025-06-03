<?php

namespace App\Blog\Actions;

use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use FlexibleFramework\Actions\RouterAware;
use FlexibleFramework\Renderer\RendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class CategoryShowAction
{
    use RouterAware;

    public function __construct(
        private readonly RendererInterface $renderer,
        private readonly PostTable $postTable,
        private readonly CategoryTable $categoryTable,
    ) {}

    public function __invoke(Request $request): string|ResponseInterface
    {
        $category = $this->categoryTable->findBy('slug', $request->getAttribute('slug'));
        $params = $request->getQueryParams();
        $posts = $this->postTable->findPaginatedPublicForCategory(12, $params['p'] ?? 1, $category->id);
        $categories = $this->categoryTable->findAll();
        return $this->renderer->render('@blog/index', [
            'posts' => $posts,
            'categories' => $categories,
            'category' => $category,
        ]);
    }
}
