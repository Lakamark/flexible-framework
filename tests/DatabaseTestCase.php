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
     * @return \PDO
     */
    public function getPdo(): \PDO
    {
        return new \PDO('sqlite::memory:', null, null, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
        ]);
    }

    public function getManager(\PDO $pdo): Manager
    {
        $configArray = require('phinx.php');

        $configArray['environments']['test'] = [
            'adapter' => 'sqlite',
            'connection' => $pdo,
        ];

        $config = new Config($configArray);
        return  new Manager($config, new StringInput(''), new NullOutput());
    }

    /**
     * @param \PDO $pdo
     * @return void
     */
    public function migrateDatabase(\PDO $pdo): void
    {
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_BOTH);
        $this->getManager($pdo)->migrate('test');
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
    }

    /**
     * To seed the database before to begin testing.
     *
     * Phinx Application can work with objects on seeding command.
     * We should change the PDO fetch mode before and after running the seed command.
     *
     * @param \PDO $pdo
     * @return void
     */
    public function seedDatabase(\PDO $pdo): void
    {
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_BOTH);
        $this->getManager($pdo)->migrate('test');
        $this->getManager($pdo)->seed('test');
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
    }
}
