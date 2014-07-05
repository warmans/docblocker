<?php
namespace Docblocker\Console\Command;

use Docblocker\Analyser;
use Docblocker\Console\Progress;
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
        //get file map
        $filemap = $this->filesystem->getFileMap($input->getArgument('target'));

        //setup a progress bar for raw file parsing
        $prog = new Progress($output, count($filemap));
        $prog->setFormat('verbose');
        $prog->start();

        //parse the files
        $parser = new CodeParser($this->filesystem);
        $parser->attach($prog);
        $rawData = $parser->parseFiles($filemap);
        $output->writeln(' File Parsing Complete');

        //setup some analysers for the raw data
        $analysers = array(
            new Analyser\Overview($rawData),
            new Analyser\DocScore($rawData)
        );

        //set up a progress bar for analysis
        $prog = new Progress($output, count($analysers));
        $prog->setFormat('verbose');
        $prog->start();

        $analyser = new Analyser($rawData, $analysers);
        $analyser->attach($prog);
        $analysis = $analyser->getAnalysis();
        $output->writeln(' Analysis Complete');

        //print_r($analysis);
    }
}
