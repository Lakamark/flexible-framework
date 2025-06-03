<?php

namespace App\Blog\Actions;

use App\Blog\Table\CategoryTable;
use FlexibleFramework\Actions\CrudAction;
use FlexibleFramework\Renderer\RendererInterface;
use FlexibleFramework\Router;
use FlexibleFramework\Session\FlashService;
use FlexibleFramework\Validator;
use Psr\Http\Message\ServerRequestInterface as Request;

class CategoryCrudAction extends CrudAction
{
    protected string $viewPath = '@blog/admin/categories';

    protected string $routePrefix = 'blog.category.admin';

    public function __construct(RendererInterface $renderer, Router $router, CategoryTable $table, FlashService $flash)
    {
        parent::__construct($renderer, $router, $table, $flash);
    }


    protected function getParams(Request $request): array
    {
        return  array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug']);
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function getValidator(Request $request): Validator
    {
        return parent::getValidator($request)
            ->required('name', 'slug')
            ->length('name', 2, 255)
            ->length('slug', 2, 50)
            ->slug('slug');
    }
}
