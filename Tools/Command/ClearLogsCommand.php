<?php

namespace Zee\Tools\Command;

use Monolog\Logger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ClearLogsCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('clear:logs')
            ->setDescription('Clear log files in <info>/app/logs</info>')
            ->setHelp(<<<EOT
The <info>%command.full_name%</info> deletes every log file in <info>/app/logs</info> directory.

    <info>%command.full_name%</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Logger $logger */
        $logger = $this->getHelper('logger')->getLogger();

        $output->writeln('<info>Clearing logs...</info>'.NL);

        $fs = new Filesystem();
        $finder = new Finder();
        $finder->files()->in(ROOT_PATH.'/app/logs')->name('*.log');

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $fs->remove($file->getRealpath());
            $output->writeln(sprintf('  Cleared: <info>%s</info>', $file->getFilename()));
        }

        $output->writeln(NL.'<info>Logs successfully cleared.</info>');
        $logger->info('Logs cleared by clear:logs command.');
    }
}
