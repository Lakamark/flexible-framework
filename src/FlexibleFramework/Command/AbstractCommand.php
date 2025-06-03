<?php

namespace FlexibleFramework\Command;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;

abstract class AbstractCommand extends Command
{
    public function __construct(
        protected readonly ContainerInterface $container
    ) {
        parent::__construct();
    }
}
