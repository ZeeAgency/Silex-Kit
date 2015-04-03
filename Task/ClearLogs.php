<?php
namespace Zee\Task;

use Zee\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ClearLogs extends Task
{
    public function getName()
    {
        return 'clear:logs';
    }

    public function getDescription()
    {
        return 'Clear every log files in /app/logs';
    }

    public function getHelp()
    {
        return <<<EOF
The <info>clear:logs</info> deletes every log files in /app/logs directory.

<info>php bin/console clear:logs</info>
EOF;
    }

    public function getCode()
    {
        $app = $this->app;

        return function (InputInterface $input, OutputInterface $output) use ($app) {
            $output->writeln('<info>Clearing logs...</info>'.NL);

            $fs = new Filesystem();
            $finder = new Finder();
            $finder->files()->in(ROOT_PATH.'/app/logs')->name('*.log');

            foreach ($finder as $file) {
                $fs->remove($file->getRealpath());
                $output->writeln('<info>Cleared:</info> '.$file->getFilename());
            }

            $output->writeln(NL.'<info>Logs successfully cleared.</info>');
            $app['monolog']->info('Logs cleared by clear:logs command.');
        };
    }
}
