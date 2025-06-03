<?php

namespace Tests\FlexibleFramework;

use FlexibleFramework\Exception\KernelException;
use FlexibleFramework\Kernel;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

class KernelTest extends TestCase
{
    private Kernel $kernel;

    public function setUp(): void
    {
        $this->kernel = new Kernel();
    }

    public function testKernel(): void
    {
        $this->kernel->addModule(get_class($this));
        $this->assertEquals([get_class($this)], $this->kernel->getModules());
    }

    public function testAppWithArrayDefinition()
    {
        $app = new Kernel(['a' => 2]);
        $this->assertEquals(2, $app->getContainer()->get('a'));
    }

    public function testPipe(): void
    {
        $middleware = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
        $middleware2 = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $middleware->expects($this->once())->method('process')->willReturn($response);
        $middleware2->expects($this->never())->method('process')->willReturn($response);
        $this->assertEquals($response, $this->kernel->pipe($middleware)->run($request));
    }

    public function testPipeWithClosure()
    {
        $middleware = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $middleware->expects($this->once())->method('process')->willReturn($response);
        $this->kernel
            ->pipe($middleware);
        $this->assertEquals($response, $this->kernel->run($request));
    }

    public function testPipeWithoutMiddleware()
    {
        $this->expectException(KernelException::class);
        $this->kernel->run($this->getMockBuilder(ServerRequestInterface::class)->getMock());
    }

    public function testPipeWithPrefix()
    {
        $middleware = $this->getMockBuilder(MiddlewareInterface::class)->getMock();
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $middleware->expects($this->once())->method('process')->willReturn($response);

        $this->kernel->pipe('/demo', $middleware);
        $this->assertEquals($response, $this->kernel->run(new ServerRequest('GET', '/demo/hello')));
        $this->expectException(KernelException::class);
        $this->assertEquals($response, $this->kernel->run(new ServerRequest('GET', '/hello')));
    }
}
