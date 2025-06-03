<?php

namespace FlexibleFramework;

use App\FlexibleFramework\Middleware\KernelMiddleware\CallableMiddleware;
use FlexibleFramework\Router\Route;
use Mezzio\Router\FastRouteRouter;
use Mezzio\Router\Route as MezzioRoute;
use Psr\Http\Message\ServerRequestInterface;

class Router
{
    private FastRouteRouter $FastRouteRouter;

    public function __construct()
    {
        $this->FastRouteRouter = new FastRouteRouter();
    }

    /**
     * To register a get route
     *
     * @param string $path
     * @param $callback
     * @param string|null $name
     * @return void
     */
    public function get(string $path, $callback, ?string $name = null): void
    {
        $this->FastRouteRouter->addRoute(new MezzioRoute($path, new CallableMiddleware($callback), ['GET'], $name));
    }

    /**
     * To register a p route
     *
     * @param string $path
     * @param $callback
     * @param string|null $name
     * @return void
     */
    public function post(string $path, $callback, ?string $name = null): void
    {
        $this->FastRouteRouter->addRoute(new MezzioRoute($path, new CallableMiddleware($callback), ['POST'], $name));
    }

    /**
     * To register a delete rooter
     *
     * @param string $path
     * @param $callback
     * @param string|null $name
     * @return void
     */
    public function delete(string $path, $callback, ?string $name = null): void
    {
        $this->FastRouteRouter->addRoute(new MezzioRoute($path, new CallableMiddleware($callback), ['DELETE'], $name));
    }

    /**
     * Add the crud rooter
     *
     * @param string $prefixPath
     * @param $callable
     * @param string $prefixName
     * @return void
     */
    public function crud(string $prefixPath, $callable, string $prefixName)
    {
        $this->get("$prefixPath", $callable, "$prefixName.index");
        $this->get("$prefixPath/new", $callable, "$prefixName.create");
        $this->post("$prefixPath/new", $callable);
        $this->get("$prefixPath/{id:\d+}", $callable, "$prefixName.edit");
        $this->post("$prefixPath/{id:\d+}", $callable);
        $this->delete("$prefixPath/{id:\d+}", $callable, "$prefixName.delete");
    }

    /**
     * Generate an uri from a route name with his params
     *
     * @param string $name
     * @param array $params
     * @param array $queryParams
     * @return string|null
     */
    public function generateUri(string $name, array $params = [], array $queryParams = []): ?string
    {
        $uri = $this->FastRouteRouter->generateUri($name, $params);
        if (!empty($queryParams)) {
            return $uri . '?' . http_build_query($queryParams);
        }
        return $uri;
    }

    /**
     * Match a route
     *
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        $result = $this->FastRouteRouter->match($request);
        if ($result->isSuccess()) {
            return new Route(
                $result->getMatchedRouteName(),
                $result->getMatchedRoute()->getMiddleware()->getCallable(),
                $result->getMatchedParams(),
            );
        }
        return null;
    }
}
