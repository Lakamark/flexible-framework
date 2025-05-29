<?php

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use FlexibleFramework\Actions\RouterAware;
use FlexibleFramework\Renderer\RendererInterface;
use FlexibleFramework\Router;
use FlexibleFramework\Session\FlashService;
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

    public function __invoke(Request $request)
    {
        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if (substr((string) $request->getUri(), -3) === 'new') {
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
     * @return ResponseInterface
     */
    public function create(Request $request)
    {
        $item = new Post();
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $params = array_merge($params, [
                'updated_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $this->postTable->insert($params);
            $this->flash->success('Post created');
            return $this->redirect('post.admin.index');
        }
        return $this->renderer->render('@blog/admin/create', compact('item'));
    }

    /**
     * Update a record
     *
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function edit(Request $request)
    {
        $item = $this->postTable->find($request->getAttribute('id'));

        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $params['updated_at'] = date('Y-m-d H:i:s');

            $this->postTable->update($item->id, $params);
            $this->flash->success('Post edited');
            return $this->redirect('post.admin.index');
        }

        return $this->renderer->render('@blog/admin/edit', compact('item'));
    }

    public function delete(Request $request)
    {
        $item = $this->postTable->find($request->getAttribute('id'));
        $this->postTable->delete($item->id);
        return $this->redirect('post.admin.index');
    }

    private function getParams(Request $request): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug', 'content']);
        }, ARRAY_FILTER_USE_KEY);
    }
}
