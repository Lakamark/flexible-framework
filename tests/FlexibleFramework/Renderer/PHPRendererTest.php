<?php

namespace Tests\FlexibleFramework\Renderer;

use FlexibleFramework\Renderer\PHPRenderer;
use PHPUnit\Framework\TestCase;

class PHPRendererTest extends TestCase
{
    private PHPRenderer $renderer;

    protected function setUp(): void
    {
        $this->renderer = new PHPRenderer(__DIR__ . '/templates');
    }

    public function testRenderTheRightPath(): void
    {
        $this->renderer->addPath('blog', __DIR__ . '/templates');
        $content = $this->renderer->render('@blog/demo');
        $this->assertStringContainsString('Hello everyone!', $content);
    }

    public function testRenderTheDefaultPath(): void
    {
        $content = $this->renderer->render('demo');
        $this->assertStringContainsString('Hello everyone!', $content);
    }

    public function testRenderWithParams(): void
    {
        $content = $this->renderer->render('demoparams', [
            'name' => 'Mark',
        ]);
        $this->assertStringContainsString('Hello Mark', $content);
    }

    public function testGlobalParams(): void
    {
        $this->renderer->addGlobal('name', 'Mark');
        $content = $this->renderer->render('demoparams');
        $this->assertStringContainsString('Hello Mark', $content);
    }
}
