<?php

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use FlexibleFramework\Actions\RouterAware;
use FlexibleFramework\Renderer\RendererInterface;
use FlexibleFramework\Router;
use FlexibleFramework\Session\FlashService;
use FlexibleFramework\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminBlogAction
{
    use RouterAware;

    public function __construct(
        private readonly RendererInterface $renderer,
        private readonly Router $router,
        private readonly PostTable $postTable,
        private readonly FlashService $flash,
    ) {}

    public function __invoke(Request $request): string|ResponseInterface
    {
        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if (str_ends_with((string) $request->getUri(), 'new')) {
            return $this->create($request);
        }
        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }
        return $this->index($request);
    }

    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->postTable->findPaginated(12, $params['p'] ?? 1);
        return $this->renderer->render('@blog/admin/index', compact('items'));
    }

    /**
     * Create a new post
     *
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function create(Request $request): ResponseInterface|string
    {
        $item = new Post();
        $errors = [];
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);

            $item = $params;

            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->postTable->insert($params);
                $this->flash->success('Post created');
                return $this->redirect('post.admin.index');
            }

            $errors = $validator->getErrors();
        }
        return $this->renderer->render('@blog/admin/create', [
            'item' => $item,
            'errors' => $errors,
        ]);
    }

    /**
     * Update a record
     *
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function edit(Request $request): string|ResponseInterface
    {
        $item = $this->postTable->find($request->getAttribute('id'));
        $errors = [];

        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $params['updated_at'] = date('Y-m-d H:i:s');

            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->postTable->update($item->id, $params);
                $this->flash->success('Post edited');
                return $this->redirect('post.admin.index');
            }

            $item->content = $params['content'];
            $item->name = $params['name'];
            $item->slug = $params['slug'];
            $errors = $validator->getErrors();
        }

        return $this->renderer->render('@blog/admin/edit', compact('item', 'errors'));
    }

    public function delete(Request $request): ResponseInterface
    {
        $item = $this->postTable->find($request->getAttribute('id'));
        $this->postTable->delete($item->id);
        $this->flash->success('Post deleted');
        return $this->redirect('post.admin.index');
    }

    private function getParams(Request $request): array
    {
        $params = array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug', 'content', 'created_at']);
        }, ARRAY_FILTER_USE_KEY);
        return array_merge($params, [
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function getValidator(Request $request): Validator
    {
        return (new Validator($request->getParsedBody()))
            ->required('content', 'name', 'slug')
            ->length('content', 10)
            ->length('name', 2, 255)
            ->length('slug', 2, 50)
            ->slug('slug');
    }
}
