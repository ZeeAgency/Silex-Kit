<?php

namespace Zee\Tools\Command;

use Monolog\Logger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ClearCacheCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('clear:cache')
            ->setDescription('Clear twig\'s cache in <info>/app/cache/twig</info>')
            ->setHelp(<<<EOT
The <info>%command.full_name%</info> deletes every files created by twig for its caching in <info>/app/cache/twig</info>.

    <info>%command.full_name%</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Logger $logger */
        $logger = $this->getHelper('logger')->getLogger();

        $output->writeln('<info>Clearing twig\'s cache...</info>'.NL);

        $fs = new Filesystem();
        $finder = new Finder();
        $finder->depth('== 0')->directories()->in(ROOT_PATH.'/app/cache/twig');

        /** @var SplFileInfo $dir */
        foreach ($finder as $dir) {
            $dirPath = $dir->getRealpath();
            $fs->remove($dirPath);
            $output->writeln(sprintf('  Cleared: <info>%s</info>', $dirPath));
        }

        $output->writeln(NL.'<info>Cache successfully cleared.</info>');
        $logger->info('Cache cleared by clear-logs command.');
    }
}
