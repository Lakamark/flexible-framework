<?php

namespace App\Blog;

use FlexibleFramework\Router;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogModule
{
    public function __construct(
        private readonly Router $router,
    ) {
        $this->router->get('/blog', [$this, 'index'], 'blog.index');
        $this->router->get('/blog/{slug:[a-z0-9\-]+}', [$this, 'show'], 'blog.show');
    }

    public function index(Request $request): string
    {
        return '<h1>Blog</h1>';
    }

    public function show(Request $request): string
    {
        $slug = $request->getAttribute('slug');
        return '<h1>Article ' . $slug . '</h1>';
    }
}
