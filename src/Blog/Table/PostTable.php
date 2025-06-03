<?php

namespace App\Blog\Table;

use App\Blog\Entity\Post;
use FlexibleFramework\Database\NoRecordException;
use FlexibleFramework\Database\PaginatedQuery;
use FlexibleFramework\Database\Table;
use Pagerfanta\Pagerfanta;

class PostTable extends Table
{
    protected string $entity = Post::class;

    protected string $table = 'posts';

    /**
     * @param int $perPage
     * @param int $currentPage
     * @return Pagerfanta
     */
    public function findPaginatedPublic(int $perPage, int $currentPage): Pagerfanta
    {
        $query =  new PaginatedQuery(
            $this->getPdo(),
            "SELECT p.*, c.name as category_name, c.slug as category_slug
                    FROM posts as p
                    LEFT JOIN categories as c
                    ON c.id = p.category_id
                    ORDER BY p.created_at DESC",
            'SELECT COUNT(id) FROM ' . $this->table,
            $this->entity
        );
        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    /**
     * @param int $perPage
     * @param int $currentPage
     * @param int $categoryId
     * @return Pagerfanta
     */
    public function findPaginatedPublicForCategory(int $perPage, int $currentPage, int $categoryId): Pagerfanta
    {
        $query =  new PaginatedQuery(
            $this->getPdo(),
            "SELECT p.*, c.name as category_name, c.slug as category_slug
                    FROM posts as p
                    LEFT JOIN categories as c
                    ON c.id = p.category_id
                    WHERE p.category_id = :category
                    ORDER BY p.created_at DESC",
            "SELECT COUNT(id) FROM $this->table WHERE category_id = :category",
            $this->entity,
            [
                'category' => $categoryId,
            ]
        );
        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    public function findWithCategory(int $id)
    {
        return $this->fetchOrFail("SELECT p.*, c.name as category_name, c.slug as category_slug
                FROM posts as p
                LEFT JOIN categories as c
                ON c.id = p.category_id
                WHERE p.id = ?", [$id]);
    }

    protected function paginationQuery(): string
    {
        return "SELECT p.id, p.name, c.name as category_name
        FROM {$this->table} as p
        LEFT JOIN categories as c ON p.category_id = c.id
        ORDER BY created_at DESC
        ";
    }
}
