<?php

namespace FlexibleFramework\Renderer;

class PHPRenderer
{
    public const string DEFAULT_NAMESPACE = '__FLEXIBLE_FRAMEWORK__';

    private array $paths = [];

    /**
     * @var array Accessible variables for all the views in the application
     */
    private array $globalsVariables = [];

    public function __construct(
        ?string $defaultPath = null,
    ) {
        if (!is_null($defaultPath)) {
            $this->addPath($defaultPath);
        }
    }

    /**
     * To add a path to load views
     *
     * @param string $namespace
     * @param string|null $path
     * @return void
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        if (is_null($path)) {
            $this->paths[self::DEFAULT_NAMESPACE] = $namespace;
        } else {
            $this->paths[$namespace] = $path;
        }
    }

    /**
     * To put the parameters accessible for all views in the application
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function addGlobal(string $key, mixed $value): void
    {
        $this->globalsVariables[$key] = $value;
    }

    /**
     * To render a template with a namespace or just only the view path,
     * $this->render->render('@blog/view')
     * $this->render->render('view')
     *
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string
    {
        if ($this->hasNamespace($view)) {
            $path = $this->replaceNamespace($view) . '.php';
        } else {
            $path = $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . '.php';
        }
        ob_start();
        $renderer = $this;
        extract($this->globalsVariables);
        extract($params);
        include($path);
        return ob_get_clean();
    }

    /**
     * Check if the path begins with a namespace
     *
     * @param string $view
     * @return bool
     */
    private function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }

    /**
     * Retrieve the namespace
     *
     * @param string $view
     * @return string
     */
    private function getNamespace(string $view): string
    {
        return substr($view, 1, strpos($view, '/') - 1);
    }

    /**
     * Replace the namespace
     *
     * @param string $view
     * @return string
     */
    private function replaceNamespace(string $view): string
    {
        $namespace = $this->getNamespace($view);
        return str_replace('@' . $namespace, $this->paths[$namespace], $view);
    }
}
