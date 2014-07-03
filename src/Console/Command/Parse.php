<?php
namespace Docblocker\Console\Command;

use Docblocker\Analyzer;
use Docblocker\Filesystem;
use Docblocker\CodeParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Parse extends Command
{
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('parse ')
            ->setDescription('Parse a directory for PHP classes')
            ->addArgument('target', InputArgument::REQUIRED, 'Directory to parse');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $parser = new CodeParser($this->filesystem);
        $rawData = $parser->parseDir($input->getArgument('target'));

        $analyzer = new Analyzer($rawData);
        $analysis = $analyzer->getAnalysis();

        print_r($analysis);
    }
}
