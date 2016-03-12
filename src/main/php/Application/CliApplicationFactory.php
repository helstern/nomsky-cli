<?php namespace Helstern\Nomsky\Application;

use Helstern\Nomsky\Analyze\EbnfCommand;
use Symfony\Component\Console\Application;

/**
 * Creates the cli application
 */
class CliApplicationFactory
{
    /** @var EbnfCommand */
    private $ebnfCommand;

    /**
     * @param \Helstern\Nomsky\Analyze\EbnfCommand $ebnfCommand
     */
    public function __construct(EbnfCommand $ebnfCommand)
    {
        $this->ebnfCommand = $ebnfCommand;
    }

    /**
     * Create a new application
     *
     * @param string $name
     * @param string $version
     *
     * @return Application
     */
    public function createApplication($name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        $application = new Application($name, $version);
        $application->add($this->ebnfCommand);
        return $application;
    }
}
