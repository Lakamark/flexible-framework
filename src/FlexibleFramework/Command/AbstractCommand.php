<?php

namespace FlexibleFramework\Command;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    public function __construct(
        protected readonly ContainerInterface $container
    ) {
        parent::__construct();
    }
}
