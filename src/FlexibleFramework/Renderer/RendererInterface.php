<?php

namespace FlexibleFramework\Renderer;

interface RendererInterface
{
    /**
     * To add a path to load views
     *
     * @param string $namespace
     * @param string|null $path
     * @return void
     */
    public function addPath(string $namespace, ?string $path = null): void;

    /**
     * To put the parameters accessible for all views in the application
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function addGlobal(string $key, mixed $value): void;

    /**
     * To render a template with a namespace or just only the view path,
     * $this->render->render('@blog/view')
     * $this->render->render('view')
     *
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string;
}
