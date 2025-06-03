#!/usr/bin/env php
<?php

include dirname(__DIR__) . '/public/index.php';

use FlexibleFramework\Command\WelcomeCommand;
use Symfony\Component\Console\Application;

$defaultCommand = new WelcomeCommand($kernel->getContainer());


$application = new Application('Flexible Framework');

$application->add($defaultCommand);
$application->setDefaultCommand($defaultCommand->getName());

$application->run();
