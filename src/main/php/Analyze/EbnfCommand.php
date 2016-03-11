<?php

namespace Nomsky\Analyze;

use Symfony\Component\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EbnfCommand extends Command\Command
{
    protected function configure()
    {
        $this
            ->setName('analyze:ebnf')
            ->setDescription('Say hello')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('analyze ebnf');
    }
}
