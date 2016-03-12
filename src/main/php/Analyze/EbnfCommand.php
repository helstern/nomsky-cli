<?php namespace Helstern\Nomsky\Analyze;

use Symfony\Component\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EbnfCommand extends Command\Command
{
    /** @var ConsoleOptions */
    private $conf;

    public function __construct(ConsoleOptions $conf)
    {
        $this->conf = $conf;
        parent::__construct(null);
    }

    protected function configure()
    {
        $this
            ->setName('analyze:ebnf')
            ->setDescription('Say hello')
        ;

        $description = sprintf(
            'the notation used by the grammar. must be one of: %s',
            implode(',', $this->conf->availableNotations())
        );
        $this->addOption('notation', null, InputOption::VALUE_OPTIONAL, $description, $this->conf->defaultNotation());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('analyze ebnf');
    }
}
