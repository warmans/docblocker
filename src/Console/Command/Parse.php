<?php
namespace Docblocker\Console\Command;

use Docblocker\Analyser;
use Docblocker\Console\Progress;
use Docblocker\Filesystem;
use Docblocker\CodeParser;
use Docblocker\Report\Json;
use Docblocker\Report\Text;
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
        $this->setName('parse')
            ->setDescription('Parse a directory for PHP classes')
            ->addArgument('target', InputArgument::REQUIRED, 'Directory to parse')
            ->addOption('fail-scores-below', null, InputOption::VALUE_OPTIONAL, 'Fail if project score is less than the specified value', 0)
            ->addOption('report-json', null, InputOption::VALUE_OPTIONAL, 'Output JSON report to this location');
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
        $parser = new CodeParser;
        $parser->attach($prog);
        $rawData = $parser->parseFiles($filemap);
        $output->writeln('');

        //setup some analysers for the raw data
        $analysers = array(
            new Analyser\Overview($rawData),
            new Analyser\DocScore($rawData)
        );

        //set up a progress bar for analysis
        $prog = new Progress($output, count($analysers));
        $prog->setFormat('verbose');
        $prog->start();

        $analyser = new Analyser($analysers);
        $analyser->attach($prog);
        $analyser->runAll();

        //always output text
        $textOutput = new Text($rawData);
        $output->writeLn($textOutput->render());

        //optionally output json
        if ($jsonReportPath = $input->geTOption('report-json')) {
            $jsonReport = new Json($rawData);
            $this->filesystem->putContents($jsonReportPath, $jsonReport->render());
            $output->writeln('Write report to '.$jsonReportPath);
        }

        //fail if score too low
        if ($rawData['overview']['score'] < $input->getOption('fail-scores-below')) {
            $output->writeln('<error>Project score ('.$rawData['overview']['score'].') was less than minimum of '.$input->getOption('fail-scores-below').'</error>');
            return 1;
        }
        return 0;
    }
}
