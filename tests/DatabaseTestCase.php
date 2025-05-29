<?php

namespace Tests;

use Phinx\Config\Config;
use Phinx\Migration\Manager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class DatabaseTestCase extends TestCase
{
    /**
     * @var \PDO
     */
    protected \PDO $pdo;

    /**
     * @var Manager
     */
    private Manager $manager;

    public function setUp(): void
    {
        $pdo = new \PDO('sqlite::memory:', null, null, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ]);

        $configArray = require('phinx.php');

        $configArray['environments']['test'] = [
            'adapter' => 'sqlite',
            'connection' => $pdo,
        ];

        $config = new Config($configArray);
        $manager = new Manager($config, new StringInput(''), new NullOutput());
        $manager->migrate('test');

        // Reset the fetch mode after phinx finished migrating the database test
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);

        $this->manager = $manager;
        $this->pdo = $pdo;
    }

    /**
     * To seed the database before to begin testing.
     *
     * Phinx Application can work with objects on seeding command.
     * We should change the PDO fetch mode before and after running the seed command.
     *
     * @return void
     */
    public function seedDatabase(): void
    {
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_BOTH);
        $this->manager->seed('test');
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
    }
}
