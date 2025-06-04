<?php

namespace App\FlexibleFramework\Middleware\Generic;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CallableMiddleware implements MiddlewareInterface
{
    public function __construct(
        private $callable
    ) {}

    public function getCallable()
    {
        return $this->callable;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return new Response();
    }
}
