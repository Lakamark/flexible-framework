<?php

namespace App\FlexibleFramework\Middleware\KernelMiddleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CombinedMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly array              $middlewares,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $handler = new CombinedMiddlewareDelegate(
            $this->container,
            $this->middlewares,
            $handler
        );
        return $handler->handle($request);
    }
}
