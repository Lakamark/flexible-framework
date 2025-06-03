<?php

namespace FlexibleFramework\Command;

use FlexibleFramework\Kernel;
use FlexibleFramework\Router;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'hello:world')]
class WelcomeCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this->setDescription("Output help");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('<fg=green>Welcome to Flexible Framework!</>');
        $io->text("My name is Flexo. I'm your command assistance.");
        $io->text("Some new features are coming soon.");
        $io->newLine();
        $io->text("The project is open source. Your can participate on this repository:");
        $io->text("https://github.com/Lakamark/flexible-framework-demo-app");
        return Command::SUCCESS;
    }
}
