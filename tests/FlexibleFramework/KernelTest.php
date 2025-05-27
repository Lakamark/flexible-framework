<?php

namespace Tests\FlexibleFramework;

use FlexibleFramework\Kernel;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class KernelTest extends TestCase
{
    private Kernel $kernel;

    public function setUp(): void
    {
        $this->kernel = new Kernel();
    }

    public function testKernel(): void
    {
        $request = new ServerRequest('GET', '/slash/');
        $response = $this->kernel->run($request);
        $this->assertContains('/slash', $response->getHeader('Location'));
        $this->assertEquals(301, $response->getStatusCode());
    }

    public function testBlogKernel(): void
    {
        $request = new ServerRequest('GET', '/blog');
        $response = $this->kernel->run($request);
        $this->assertStringContainsString('<h1>Blog</h1>', (string) $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test404Kernel(): void
    {
        $request = new ServerRequest('GET', '/fake');
        $response = $this->kernel->run($request);
        $this->assertStringContainsString('<h1>404 Not Found</h1>', (string) $response->getBody());
        $this->assertEquals(404, $response->getStatusCode());
    }

}
