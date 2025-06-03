<?php

namespace FlexibleFramework\Twig;

use FlexibleFramework\Router;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\TwitterBootstrap5View;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PagerFantaExtension extends AbstractExtension
{
    public function __construct(
        private readonly Router $router,
    ) {}

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('paginate', $this->paginate(...), ['is_safe' => ['html']]),
        ];

    }

    /**
     * Generate the pagination
     *
     * @param Pagerfanta $paginateResults
     * @param string $routeName
     * @param array $routerParams
     * @param array $queryArgs
     * @return string
     */
    public function paginate(Pagerfanta $paginateResults, string $routeName, array $routerParams = [], array
    $queryArgs =
    []): string
    {
        $view = new TwitterBootstrap5View();
        return $view->render($paginateResults, function (int $page) use ($routeName, $routerParams, $queryArgs) {
            if ($page > 1) {
                $queryArgs['p'] = $page;
            }
            return $this->router->generateUri($routeName, $routerParams, $queryArgs);
        });
    }
}
