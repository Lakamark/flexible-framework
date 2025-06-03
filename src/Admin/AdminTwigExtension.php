<?php

namespace App\Admin;

use FlexibleFramework\WidgetInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminTwigExtension extends AbstractExtension
{
    public function __construct(
        private array $widgets
    ) {}

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('admin_menu', $this->renderMenu(...), ['is_safe' => ['html']]),
        ];
    }

    /**
     * Return the menu
     * @return string
     */
    public function renderMenu(): string
    {
        return array_reduce($this->widgets, function (string $html, WidgetInterface $widget) {
            return $html . $widget->renderMenu();
        }, "");
    }
}
