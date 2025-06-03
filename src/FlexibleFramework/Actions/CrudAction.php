<?php

namespace FlexibleFramework\Actions;

use FlexibleFramework\Renderer\RendererInterface;
use FlexibleFramework\Router;
use FlexibleFramework\Session\FlashService;
use FlexibleFramework\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class CrudAction
{
    use RouterAware;
    /**
     * @var string
     */
    protected string $viewPath;

    /**
     * @var string
     */
    protected string $routePrefix;

    /**
     * @var string[]
     */
    protected array $messages = [
        'created' => 'The content was been created.',
        'updated' => 'The content was been updated.',
        'deleted' => 'The content was been deleted.',
    ];

    public function __construct(
        private readonly RendererInterface $renderer,
        private readonly Router            $router,
        private $table,
        private readonly FlashService      $flash,
    ) {}

    public function __invoke(Request $request): string|ResponseInterface
    {
        $this->renderer->addGlobal('viewPath', $this->viewPath);
        $this->renderer->addGlobal('routePrefix', $this->routePrefix);

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

    /**
     * Display items list
     *
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->table->findPaginated(12, $params['p'] ?? 1);
        return $this->renderer->render($this->viewPath . '/index', compact('items'));
    }

    /**
     * Create a new item
     *
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function create(Request $request): ResponseInterface|string
    {
        $item = $this->getNewEntity();
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $validator = $this->getValidator($request);

            if ($validator->isValid()) {
                $this->table->insert($params);
                $this->flash->success($this->messages['created']);
                return $this->redirect($this->routePrefix . '.index');
            }

            $item = $params;
            $errors = $validator->getErrors();
        }
        return $this->renderer->render(
            $this->viewPath . '/create',
            $this->formParams([
                'item' => $item,
                'errors' => $errors ?? '',
            ])
        );
    }

    /**
     * Update an item
     *
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function edit(Request $request): string|ResponseInterface
    {
        $item = $this->table->find($request->getAttribute('id'));
        $errors = [];

        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $params['updated_at'] = date('Y-m-d H:i:s');

            $validator = $this->getValidator($request);
            if ($validator->isValid()) {
                $this->table->update($item->id, $params);
                $this->flash->success($this->messages['updated']);
                return $this->redirect($this->routePrefix . '.index');
            }

            $item->content = $params['content'];
            $item->name = $params['name'];
            $item->slug = $params['slug'];
            $errors = $validator->getErrors();
        }


        return $this->renderer->render(
            $this->viewPath . '/edit',
            $this->formParams(compact('item', 'errors'))
        );
    }

    /**
     * Delete an item
     *
     * @param Request $request
     * @return ResponseInterface
     */
    public function delete(Request $request): ResponseInterface
    {
        $item = $this->table->find($request->getAttribute('id'));
        $this->table->delete($item->id);
        $this->flash->success($this->messages['deleted']);
        return $this->redirect($this->routePrefix . '.index');
    }

    /**
     * Filter allowed parameter to edit
     * @param Request $request
     * @return array
     */
    protected function getParams(Request $request): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, []);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Validate the data
     *
     * @param Request $request
     * @return Validator
     */
    protected function getValidator(Request $request): Validator
    {
        return new Validator($request->getParsedBody());
    }


    protected function getNewEntity(): mixed
    {
        return [];
    }

    /**
     * Retrieve params from form
     *
     * @param array $params
     * @return array
     */
    protected function formParams(array $params): array
    {
        return $params;
    }
}
