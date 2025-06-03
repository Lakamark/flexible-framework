<?php

namespace FlexibleFramework\Database;

use Pagerfanta\Pagerfanta;
use PDO;
use stdClass;

class Table
{
    /**
     * Define the table name in the database
     * @var string
     */
    protected string $table;

    /**
     * Define the entity class
     * @var string
     */
    protected string $entity = stdClass::class;

    public function __construct(
        private readonly \PDO $pdo
    ) {}

    /**
     * Paginate records
     *
     * @param int $perPage
     * @param int $currentPage
     * @return Pagerfanta
     */
    public function findPaginated(int $perPage, int $currentPage): Pagerfanta
    {
        $query =  new PaginatedQuery(
            $this->pdo,
            $this->paginationQuery(),
            'SELECT COUNT(id) FROM ' . $this->table,
            $this->entity
        );
        return (new Pagerfanta($query))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($currentPage);
    }

    /**
     * @return string
     */
    protected function paginationQuery(): string
    {
        return 'SELECT * FROM ' . $this->table;
    }

    /**
     * Get a list key and value from database records
     *
     * @return array
     */
    public function findList(): array
    {
        $results = $this->pdo
            ->query("SELECT id, name FROM $this->table")
            ->fetchAll(\PDO::FETCH_NUM);
        $list = [];
        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }
        return $list;
    }

    /**
     * Get all records in the database
     *
     * @return array
     */
    public function findAll(): array
    {
        $query = $this->pdo->query("SELECT * FROM $this->table");
        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        } else {
            $query->setFetchMode(PDO::FETCH_OBJ);
        }
        return $query->fetchAll();
    }

    /**
     * Get a colon by a field
     *
     * @param string $field
     * @param string $value
     * @return mixed
     * @throws NoRecordException
     */
    public function findBy(string $field, string $value): mixed
    {
        return $this->fetchOrFail("SELECT * FROM $this->table WHERE $field = ?", [$value]);
    }

    /**
     * Find a record from his id
     *
     * @param int $id
     * @return mixed
     * @throws NoRecordException
     */
    public function find(int $id): mixed
    {
        return$this->fetchOrFail("SELECT * FROM $this->table WHERE id = ?", [$id]);
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
        $values =  join(', ', array_map(function ($field) {
            return ':' . $field;
        }, $fields));

        $fields = join(', ', $fields);

        $statement = $this->pdo->prepare("INSERT INTO $this->table ($fields) VALUES ($values)");
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
        $statement = $this->pdo->prepare("UPDATE $this->table SET $fieldQuery WHERE id = :id");
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
        $statement = $this->pdo->prepare("DELETE FROM $this->table WHERE id = ?");
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

    /**
     * Check if a record exists in the database
     *
     * @param $id
     * @return bool
     */
    public function exists($id): bool
    {
        $statement = $this->pdo->prepare("SELECT id FROM $this->table WHERE id = ?");
        $statement->execute([$id]);
        return $statement->fetchColumn() !== false;
    }


    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }


    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    /**
     * To execute a query and to get the first result
     * @param string $query
     * @param array $params
     * @return mixed
     * @throws NoRecordException
     */
    protected function fetchOrFail(string $query, array $params = []): mixed
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);

        // If the entity is defined, we change the fetch mode
        // Otherwise we use de default fetch mode
        if ($this->entity) {
            $query->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }
        $record = $query->fetch();
        if ($record === false) {
            throw new NoRecordException();
        }
        return $record;
    }
}
