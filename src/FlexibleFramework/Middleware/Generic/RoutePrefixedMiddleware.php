<?php

namespace FlexibleFramework\Middleware\Generic;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RoutePrefixedMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly string $prefix,
        private readonly MiddlewareInterface $middleware,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        if (strpos($path, $this->prefix) === 0) {
            if (is_string($this->middleware)) {
                return $this->container->get($this->middleware)->process($request, $handler);
            } else {
                return $this->middleware->process($request, $handler);
            }
        }
        return $handler->handle($request);
    }
}
