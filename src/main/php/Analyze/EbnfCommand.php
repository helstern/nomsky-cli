<?php namespace Helstern\Nomsky\Analyze;

use Helstern\Nomsky\Grammar\GrammarFactory;
use Symfony\Component\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class EbnfCommand extends Command\Command
{
    /** @var ConsoleOptions */
    private $conf;

    /** @var Filesystem */
    private $filesystem;

    public function __construct(ConsoleOptions $conf, Filesystem $filesystem)
    {
        $this->conf = $conf;
        $this->filesystem = $filesystem;
        parent::__construct(null);
    }

    protected function configure()
    {
        $this
            ->setName('analyze:ebnf')
            ->setDescription('Analyze an ebnf grammar')
        ;

        //grammar file
        $description = 'the location of the grammar file';
        $this->addOption('grammar', null, InputOption::VALUE_REQUIRED, $description);

        //notation
        $description = sprintf(
            'the notation used by the grammar. must be one of: %s',
            implode(',', $this->conf->availableNotations())
        );
        $this->addOption('notation', null, InputOption::VALUE_OPTIONAL, $description, $this->conf->defaultNotation());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if(! $this->verifySyntax($input, $output)) {
            return -1;
        }

        $grammarPath = $this->resolveFilePath($input, $output);
        if (empty($grammarPath)) {
            return -1;
        }

        $grammarFactory = new GrammarFactory();
        $grammar = $grammarFactory->ebnfFromFile($grammarPath);

        $analyzer = new LLAnalyzer();
        $lookahead = $analyzer->analyze($grammar);

        $msg = sprintf('grammar is LL(%s)', $lookahead);
        $output->writeln($msg);
    }

    /**
     * Returns the full path to the grammar file or null if path does not exists
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return null|string
     *
     */
    private function resolveFilePath(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getOption('grammar');

        if (! $this->filesystem->isAbsolutePath($path)) {
            $dir = getcwd();
            $resolvedPath = $dir . DIRECTORY_SEPARATOR . $path;
        } else {
            $resolvedPath = $path;
        }

        if (file_exists($resolvedPath) && is_file($resolvedPath)) {
            return $resolvedPath;
        }

        $msg = sprintf('<error>file not found: %s</error>', $resolvedPath);
        $output->writeln($msg);

        return null;
    }

    private function verifySyntax(InputInterface $input, OutputInterface $output)
    {
        $input->validate();

        $option = $input->getOption('grammar');
        if (empty($option)) {
            $msg = sprintf('<error>missing required value for --grammar</error>', $option);
            $output->writeln($msg);
            return false;
        }

        $option = $input->getOption('notation');
        if (! $this->conf->validateNotation($option)) {
            $msg = sprintf('<error>unknown value for --notation: %s</error>', $option);
            $output->writeln($msg);
            return false;
        }
        return true;
    }


}
