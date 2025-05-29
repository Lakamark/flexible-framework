<?php

namespace Tests\Blog\Table;

use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use Tests\DatabaseTestCase;

class PostTableTest extends DatabaseTestCase
{
    private PostTable $postTable;

    public function setUp(): void
    {
        parent::setUp();
        $this->postTable = new PostTable($this->pdo);
    }

    public function testFind(): void
    {
        $this->seedDatabase();
        $post = $this->postTable->find(1);
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testNotFoundRecord(): void
    {
        $post = $this->postTable->find(1);
        $this->assertNull($post);
    }

    public function testUpdate(): void
    {
        $this->seedDatabase();

        $this->postTable->update(1, [
            'name' => 'New Title',
            'slug' => 'new-slug',
        ]);
        $post = $this->postTable->find(1);
        $this->assertEquals("New Title", $post->name);
        $this->assertEquals("new-slug", $post->slug);
    }

    public function testInsert(): void
    {
        $this->postTable->insert([
            'name' => 'New Title',
            'slug' => 'new-slug',
        ]);
        $post = $this->postTable->find(1);
        $this->assertEquals("New Title", $post->name);
        $this->assertEquals("new-slug", $post->slug);
    }

    public function testDelete(): void
    {
        $this->postTable->insert([
            'name' => 'New Title',
            'slug' => 'new-slug',
        ]);
        $this->postTable->insert([
            'name' => 'New Title',
            'slug' => 'new-slug',
        ]);
        $count = $this->pdo->query("SELECT COUNT(id) FROM posts")->fetchColumn();
        $this->assertEquals(2, (int) $count);
        $this->postTable->delete($this->pdo->lastInsertId());

        $count = $this->pdo->query("SELECT COUNT(id) FROM posts")->fetchColumn();
        $this->assertEquals(1, (int) $count);
    }
}
