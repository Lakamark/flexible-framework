<?php

namespace App\FlexibleFramework\Loader;

use Dotenv\Dotenv;
use Dotenv\Repository\Adapter\EnvConstAdapter;
use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\AdapterRepository;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Repository\RepositoryInterface;

class EnvLoader
{
    public function __construct(
        private readonly string $projectDirectory,
    ) {
        $repository = $this->loadRepository();

        $dotenv = Dotenv::create($repository, $this->projectDirectory);
        $dotenv->load();
    }

    /**
     * Get a value from an env variables
     *
     * @param string $key
     * @return false|array|string
     */
    public function getValue(string $key): false|array|string
    {
        return getenv($key);
    }

    /**
     * Load and to configure the Dotenv repository
     * @return AdapterRepository|RepositoryInterface
     */
    private function loadRepository(): AdapterRepository|RepositoryInterface
    {
        return RepositoryBuilder::createWithNoAdapters()
            ->addAdapter(EnvConstAdapter::class)
            ->addWriter(PutenvAdapter::class)
            ->immutable()
            ->make();
    }
}
