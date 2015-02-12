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

    /**
     * Setup command options
     */
    protected function configure()
    {
        $this->setName('parse')
            ->setDescription('Parse a directory for PHP classes')
            ->addArgument('target', InputArgument::REQUIRED, 'Directory to parse')
            ->addOption('fail-score-below', null, InputOption::VALUE_OPTIONAL, 'Fail if project score is less than the specified value', 0)
            ->addOption('report-json', null, InputOption::VALUE_OPTIONAL, 'Output JSON report to this location')
            ->addOption('report-text', null, InputOption::VALUE_OPTIONAL, 'Output text report to this location')
            ->addOption('no-progress', null, InputOption::VALUE_NONE, 'Omit Progress bars in output');
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

        $output->writeln("\nProcessing Files...");

        //parse the files
        $parser = new CodeParser;

        //setup a progress bar for raw file parsing
        if (!$input->getOption('no-progress')) {
            $prog = new Progress($output, count($filemap));
            $prog->setFormat('verbose');
            $prog->start();

            $parser->attach($prog);
        }

        $rawData = $parser->parseFiles($filemap);
        $output->writeln('');

        $output->writeln("\nAnalysing...");

        //setup some analysers for the raw data
        $analysers = array(
            new Analyser\Overview($rawData),
            new Analyser\DocScore($rawData),
            new Analyser\ProjectScore($rawData),
        );

        $analyser = new Analyser($analysers);
        //set up a progress bar for analysis
        if (!$input->getOption('no-progress')) {
            $prog = new Progress($output, count($analysers));
            $prog->setFormat('verbose');
            $prog->start();

            $analyser->attach($prog);
        }
        $analyser->runAll();

        //always output text one way or another
        $textOutput = new Text($rawData);
        if ($textReportPath = $input->getOption('report-text')) {
            $this->filesystem->putContents($textReportPath, $textOutput->render());
            $output->writeln("\n\nWrote text report to $textReportPath");
        } else {
            $output->writeLn($textOutput->render());
        }

        //optionally output json
        if ($jsonReportPath = $input->getOption('report-json')) {
            $jsonReport = new Json($rawData);
            $this->filesystem->putContents($jsonReportPath, $jsonReport->render());
            $output->writeln("\nWrote json report to $jsonReportPath");
        }

        //fail if score too low
        if ($rawData['project']['score'] < $input->getOption('fail-score-below')) {
            $output->writeln('<error>Project score ('.$rawData['overview']['score'].') was less than minimum of '.$input->getOption('fail-score-below').'</error>');
            return 1;
        }
        return 0;
    }
}
