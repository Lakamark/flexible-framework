<?php

namespace App\Blog;

use App\Blog\Table\PostTable;
use FlexibleFramework\Renderer\RendererInterface;
use FlexibleFramework\WidgetInterface;

class BlogWidget implements WidgetInterface
{
    public function __construct(
        private readonly RendererInterface $renderer,
        private readonly PostTable $postTable,
    ) {}
    public function render(): string
    {
        $count = $this->postTable->count();
        return $this->renderer->render('@blog/admin/widget', compact('count'));
    }

    public function renderMenu(): string
    {
        return $this->renderer->render('@blog/admin/menu');
    }
}
