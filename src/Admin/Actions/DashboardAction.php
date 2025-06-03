<?php

namespace App\Admin\Actions;

use FlexibleFramework\Renderer\RendererInterface;
use FlexibleFramework\WidgetInterface;

class DashboardAction
{
    /**
     * @var WidgetInterface[]
     */
    private array $widgets;

    public function __construct(
        private readonly RendererInterface $renderer,
        array $widgets
    ) {
        $this->widgets = $widgets;
    }

    public function __invoke()
    {
        $widgets = array_reduce($this->widgets, function (string $html, WidgetInterface $widget) {
            return $html . $widget->render();
        }, "");
        return $this->renderer->render('@admin/dashboard', compact('widgets'));
    }
}
