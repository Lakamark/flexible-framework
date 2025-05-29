<?php

namespace FlexibleFramework\Database;

use Cake\Datasource\QueryInterface;
use Pagerfanta\Adapter\AdapterInterface;

class PaginatedQuery implements AdapterInterface
{
    /**
     * @param \PDO $pdo
     * @param string $query The request to get x results
     * @param string $contQuery The count queries the total result numbers
     * @param string $entity
     */
    public function __construct(
        private readonly \PDO $pdo,
        private readonly string $query,
        private readonly string $contQuery,
        private readonly string $entity
    ) {}

    public function getNbResults(): int
    {
        return $this->pdo->query($this->contQuery)->fetchColumn();
    }

    public function getSlice(int $offset, int $length): iterable
    {
        $statement = $this->pdo->prepare($this->query . ' LIMIT :offset, :length');
        $statement->bindParam('offset', $offset, \PDO::PARAM_INT);
        $statement->bindParam('length', $length, \PDO::PARAM_INT);
        $statement->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        $statement->execute();
        return $statement->fetchAll();
    }
}
