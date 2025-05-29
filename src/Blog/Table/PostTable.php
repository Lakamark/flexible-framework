<?php

namespace App\Blog\Table;

use App\Blog\Entity\Post;
use FlexibleFramework\Database\PaginatedQuery;
use Pagerfanta\Pagerfanta;

class PostTable
{
    public function __construct(
        private readonly \PDO $pdo
    ) {}


    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        $query =  new PaginatedQuery(
            $this->pdo,
            'SELECT * FROM posts ORDER BY created_At DESC',
            'SELECT COUNT(id) FROM posts',
            Post::class
        );
        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    /**
     * Find a post from his id
     *
     * @param int $id
     * @return Post|null
     */
    public function find(int $id): ?Post
    {
        $query = $this->pdo->prepare('SELECT * FROM posts WHERE id = ?');
        $query->execute([$id]);
        $query->setFetchMode(\PDO::FETCH_CLASS, Post::class);
        return $query->fetch() ?: null;
    }

    /**
     * Insert a new record in the database
     *
     * @param array $params
     * @return bool
     */
    public function insert(array $params): bool
    {
        $fields = array_keys($params);
        $values = array_map(function ($field) {
            return ':' . $field;
        }, $fields);

        $statement = $this->pdo->prepare(
            "INSERT INTO posts (" . join(',', $fields) . ")
            VALUES
            (" . join(',', $values) . ")"
        );
        return $statement->execute($params);
    }

    /**
     * To update a record in the database
     *
     * @param int $id
     * @param array $params
     * @return bool
     */
    public function update(int $id, array $params): bool
    {
        $fieldQuery = $this->buildFieldQuery($params);
        $params['id'] = $id;
        $statement = $this->pdo->prepare("UPDATE posts SET $fieldQuery WHERE id = :id");
        return $statement->execute($params);
    }

    /**
     * Delete a record in the database
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare("DELETE FROM posts WHERE id = ?");
        return $statement->execute([$id]);
    }

    /**
     * @param array $params
     * @return string
     */
    private function buildFieldQuery(array $params): string
    {
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }
}
