<?php

//this file exists because of the complications of loading a yml or xml configuration file from inside a phar archive

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/** @var ContainerBuilder $container */
$def = $container->register('application_factory', '\Helstern\Nomsky\Application\CliApplicationFactory');
$def->addArgument(new Reference('analyze_ebnf_command'));


/** @var ContainerBuilder $container */
$def = $container->register('analyze_ebnf_command', '\Helstern\Nomsky\Analyze\EbnfCommand');
$def->addArgument(new Reference("console_options"));
$def->addArgument(new Reference('filesystem'));

$def = $container->register('console_options', '\Helstern\Nomsky\Analyze\ConsoleOptions');
$def = $container->register('filesystem', '\Symfony\Component\Filesystem\Filesystem');
