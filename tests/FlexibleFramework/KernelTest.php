<?php

namespace Tests\FlexibleFramework;

use App\Blog\BlogModule;
use FlexibleFramework\Kernel;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Tests\FlexibleFramework\Modules\ErroredModule;
use Tests\FlexibleFramework\Modules\StringModule;

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
        $kernel = new Kernel([
            BlogModule::class,
        ]);
        $request = new ServerRequest('GET', '/blog');
        $response = $kernel->run($request);

        $requestSingle = new ServerRequest('GET', '/blog/my-article');
        $responseSingle = $kernel->run($requestSingle);

        $this->assertStringContainsString('<h1>Blog</h1>', (string) $response->getBody());
        $this->assertStringContainsString('<h1>Article my-article</h1>', (string) $responseSingle->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test404Kernel(): void
    {
        $request = new ServerRequest('GET', '/fake');
        $response = $this->kernel->run($request);
        $this->assertStringContainsString('<h1>404 Not Found</h1>', (string) $response->getBody());
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testThrowExceptionNoResponse(): void
    {
        $kernel = new Kernel([
            ErroredModule::class,
        ]);
        $request = new ServerRequest('GET', '/demo');
        $this->expectException(\RuntimeException::class);
        $kernel->run($request);
    }

    public function testCovertStringToResponse(): void
    {
        $kernel = new Kernel([
            StringModule::class,
        ]);
        $request = new ServerRequest('GET', '/demo');
        $response = $kernel->run($request);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertStringContainsString("DEMO", (string) $response->getBody());
    }
}
