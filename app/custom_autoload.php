<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Input\ArrayInput;
use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Doctrine\Bundle\FixturesBundle\Command\LoadDataFixturesDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand;

/** @var ClassLoader $loader */
$loader = require __DIR__.'/../vendor/autoload.php';
AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$kernel = new AppKernel('test', true); // create a "test" kernel
$kernel->boot();

// Drop the database
/*$command = new DropDatabaseDoctrineCommand();
$application = new Application($kernel);
$application->add($command);
$input = new ArrayInput(array(
    'command' => 'doctrine:database:drop',
    '--force' => true,
));
$command->run($input, new ConsoleOutput());

// Create the database
$command = new CreateDatabaseDoctrineCommand();
$application->add($command);
$input = new ArrayInput(array(
    'command' => 'doctrine:database:create',
));
$command->run($input, new ConsoleOutput());

// Create the schema
$command = new CreateSchemaDoctrineCommand();
$application->add($command);
$input = new ArrayInput(array(
    'command' => 'doctrine:schema:create',
));
$command->run($input, new ConsoleOutput());

// Load Fixtures
$command = new LoadDataFixturesDoctrineCommand();
$application->add($command);
$input = new ArrayInput(array(
    'command'          => 'doctrine:fixtures:load',
));
$input->setInteractive(false);
$command->run($input, new ConsoleOutput());*/

return $loader;