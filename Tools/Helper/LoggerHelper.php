<?php

namespace Zee\Tools\Helper;

use Monolog\Logger;
use Symfony\Component\Console\Helper\Helper;

class LoggerHelper extends Helper
{
    protected $_logger;

    /**
     * Constructor.
     *
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->_logger = $logger;
    }

    /**
     * Retrieves current Logger.
     *
     * @return Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'logger';
    }
}
