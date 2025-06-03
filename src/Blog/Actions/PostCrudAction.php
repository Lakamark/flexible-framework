<?php

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use DateTime;
use FlexibleFramework\Actions\CrudAction;
use FlexibleFramework\Renderer\RendererInterface;
use FlexibleFramework\Router;
use FlexibleFramework\Session\FlashService;
use FlexibleFramework\Validator;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostCrudAction extends CrudAction
{
    protected string $viewPath = '@blog/admin/posts';

    protected string $routePrefix = 'blog.admin';

    private $categoryTable;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $table,
        FlashService $flash,
        CategoryTable $categoryTable
    ) {
        $this->categoryTable = $categoryTable;
        parent::__construct($renderer, $router, $table, $flash);
    }

    /**
     * @param array $params
     * @return array
     */
    protected function formParams(array $params): array
    {
        $params['categories'] = $this->categoryTable->findList();
        return $params;
    }

    protected function getNewEntity(): Post
    {
        $post = new Post();
        $post->created_at = new DateTime('Y-m-d H:i:s');
        return $post;
    }


    protected function getParams(Request $request): array
    {
        $params = array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug', 'content', 'created_at', 'category_id']);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($params, [
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    protected function getValidator(Request $request): Validator
    {
        return parent::getValidator($request)
            ->required('content', 'name', 'slug', 'category_id')
            ->length('content', 10)
            ->length('name', 2, 255)
            ->length('slug', 2, 50)
            ->exists('category_id', $this->categoryTable->getTable(), $this->categoryTable->getPdo())
            ->slug('slug');
    }
}
