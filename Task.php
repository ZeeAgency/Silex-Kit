<?php
namespace Zee;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Task
 * @deprecated
 * @package Zee
 */
abstract class Task
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function getName()
    {
        return 'task';
    }

    public function getDefinition()
    {
        return array();
    }

    public function getDescription()
    {
        return '';
    }

    public function getHelp()
    {
        return '';
    }

    public function getCode()
    {
        $app = $this->app;

        return function (InputInterface $input, OutputInterface $output) use ($app) {

        };
    }
}
