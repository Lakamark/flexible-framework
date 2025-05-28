<?php

namespace App\Blog\Actions;

use FlexibleFramework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogAction
{
    public function __construct(
        private readonly RendererInterface $renderer,
    ) {}

    public function __invoke(Request $request): string
    {
        $slug = $request->getAttribute('slug');
        if ($slug) {
            return $this->show($slug);
        }
        return $this->index();
    }

    public function index(): string
    {
        return $this->renderer->render('@blog/index');
    }

    public function show(string $slug): string
    {
        return $this->renderer->render('@blog/show', ['slug' => $slug]);
    }

}
