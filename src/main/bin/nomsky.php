<?php

use Helstern\Nomsky\Analyze;
use Helstern\Nomsky\Application\CliApplicationFactory;
use Helstern\Nomsky\DependencyInjection\PhpConfigResourceLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;

$container = new ContainerBuilder();
$loader = new PhpConfigResourceLoader($container, new FileLocator(dirname(__DIR__).'/resources'));
$loader->load('services.php');

/** @var CliApplicationFactory $factory */
$factory = $container->get('application_factory');
$application = $factory->createApplication('Nomsky Console', '@package_version@');
$application->run();
