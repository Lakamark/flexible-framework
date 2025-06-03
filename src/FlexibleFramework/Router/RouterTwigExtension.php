<?php

namespace FlexibleFramework\Router;

use FlexibleFramework\Router;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RouterTwigExtension extends AbstractExtension
{
    public function __construct(
        private readonly Router $router
    ) {}

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('path', $this->pathFor(...)),
            new TwigFunction('is_subpath', $this->isSubPath(...)),
        ];
    }

    /**
     * Generate the uri from a route
     *
     * @param string $path
     * @param array $params
     * @return string
     */
    public function pathFor(string $path, array $params = []): string
    {
        return $this->router->generateUri($path, $params);
    }

    /**
     * If the path is a sub path
     * @param string $path
     * @return bool
     */
    public function isSubPath(string $path): bool
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $expectedUri = $this->router->generateUri($path);
        return str_contains($uri, $expectedUri);
    }
}
