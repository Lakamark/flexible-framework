<?php

namespace FlexibleFramework\Actions;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Add some method to interact with the rooter class
 */
trait RouterAware
{
    /**
     * To redirect on the right url
     *
     * @param string $path
     * @param array $params
     * @return ResponseInterface
     */
    public function redirect(string $path, array $params = []): ResponseInterface
    {
        $redirectUri = $this->router->generateUri($path, $params);
        return (new Response())
            ->withStatus(301)
            ->withHeader('Location', $redirectUri);
    }
}
