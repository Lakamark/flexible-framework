<?php

namespace FlexibleFramework;

interface WidgetInterface
{
    /**
     * Add a module to the admin menu
     * @return string
     */
    public function renderMenu(): string;

    /**
     * Return the view path of your widget
     * @return string
     */
    public function render(): string;
}
