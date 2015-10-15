<?php
namespace Zee\Task;

use Zee\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Class ClearCache
 * @deprecated
 * @package Zee\Task
 */
class ClearCache extends Task
{
    public function getName()
    {
        return 'clear:cache';
    }

    public function getDescription()
    {
        return 'Clear twig\'s cache in /app/cache/twig';
    }

    public function getHelp()
    {
        return <<<EOF
The <info>clear:cache</info> deletes every files created by twig for its caching in /app/cache/twig.

<info>php bin/console clear:cache</info>
EOF;
    }

    public function getCode()
    {
        $app = $this->app;

        return function (InputInterface $input, OutputInterface $output) use ($app) {
            $output->writeln('<info>Clearing twig\'s cache...</info>'.NL);

            $fs = new Filesystem();
            $finder = new Finder();
            $finder->depth('== 0')->directories()->in(ROOT_PATH.'/app/cache/twig');

            foreach ($finder as $dir) {
                $dirpath = $dir->getRealpath();
                $fs->remove($dirpath);
                $output->writeln('<info>Cleared:</info> '.$dirpath);
            }

            $output->writeln(NL.'<info>Cache successfully cleared.</info>');
            $app['monolog']->info('Cache cleared by clear-logs command.');
        };
    }
}
