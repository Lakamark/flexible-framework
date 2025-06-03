<?php

namespace FlexibleFramework\Database;

use Pagerfanta\Adapter\AdapterInterface;

class PaginatedQuery implements AdapterInterface
{
    /**
     * @param \PDO $pdo
     * @param string $query The request to get x results
     * @param string $contQuery The count queries the total result numbers
     * @param string|null $entity
     * @param array $params
     */
    public function __construct(
        private readonly \PDO $pdo,
        private readonly string $query,
        private readonly string $contQuery,
        private readonly ?string $entity = null,
        private readonly array $params = []
    ) {}

    public function getNbResults(): int
    {
        if (!empty($this->params)) {
            $query = $this->pdo->prepare($this->contQuery);
            $query->execute($this->params);
            return $query->fetchColumn();
        }
        return $this->pdo->query($this->contQuery)->fetchColumn();
    }

    public function getSlice(int $offset, int $length): iterable
    {
        $statement = $this->pdo->prepare($this->query . ' LIMIT :offset, :length');

        foreach ($this->params as $key => $param) {
            $statement->bindParam($key, $param);
        }
        $statement->bindParam('offset', $offset, \PDO::PARAM_INT);
        $statement->bindParam('length', $length, \PDO::PARAM_INT);

        // If the entity is defined, we change the fetch mode
        // Otherwise we use de default fetch mode
        if ($this->entity) {
            $statement->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        }

        $statement->execute();
        return $statement->fetchAll();
    }
}
