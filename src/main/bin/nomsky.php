<?php

use Helstern\Nomsky\Analyze;
use Symfony\Component\Console\Application;

$application = new Application('Nomsky Console', '@package_version@');
$application->add(new Analyze\EbnfCommand());
$application->run();
