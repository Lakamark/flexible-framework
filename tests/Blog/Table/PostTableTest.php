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
        $pdo = $this->getPdo();
        $this->migrateDatabase($pdo);
        $this->postTable = new PostTable($pdo);
    }

    public function testFind(): void
    {
        $this->seedDatabase($this->postTable->getPdo());
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
        $this->seedDatabase($this->postTable->getPdo());

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
        $count = $this->postTable->getPdo()->query("SELECT COUNT(id) FROM posts")->fetchColumn();
        $this->assertEquals(2, (int) $count);
        $this->postTable->delete($this->postTable->getPdo()->lastInsertId());

        $count = $this->postTable->getPdo()->query("SELECT COUNT(id) FROM posts")->fetchColumn();
        $this->assertEquals(1, (int) $count);
    }
}
