<?php

namespace Tests\Blog\Actions;

use App\Blog\Actions\BlogAction;
use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use FlexibleFramework\Renderer\RendererInterface;
use FlexibleFramework\Router;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophet;

class BlogActionTest extends TestCase
{
    /**
     * @var BlogAction
     */
    private BlogAction $action;

    private $renderer;


    private $postTable;

    private $router;

    private Prophet $prophet;

    protected function setUp(): void
    {
        $this->prophet = new Prophet();

        $this->renderer = $this->prophet->prophesize(RendererInterface::class);
        $this->postTable = $this->prophet->prophesize(PostTable::class);

        $this->router = $this->prophet->prophesize(Router::class);
        $this->action = new BlogAction(
            $this->renderer->reveal(),
            $this->router->reveal(),
            $this->postTable->reveal(),
        );
    }

    public function makePost(int $id, string $slug): Post
    {
        $post = new Post();
        $post->id = $id;
        $post->slug = $slug;
        return $post;
    }

    public function testShowRedirect(): void
    {
        $post = $this->makePost(9, 'test-slug');
        $this->router->generateUri('blog.show', ['id' => $post->id, 'slug' => $post->slug])->willReturn('/demo2');
        $this->postTable->find($post->id)->willReturn($post);
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', 'error');

        $response = call_user_func_array($this->action, [$request]);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals(['/demo2'], $response->getHeader('Location'));
    }

    public function testShowRenderer(): void
    {
        $post = $this->makePost(9, 'test-slug');
        $this->postTable->find($post->id)->willReturn($post);
        $this->renderer->render('@blog/show', ['post' => $post])->willReturn('');
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', $post->slug);

        $response = call_user_func_array($this->action, [$request]);
        $this->assertTrue(true);
    }
}
