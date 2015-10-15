<?php

namespace Zee\Tools\Helper;

use Silex\Application;
use Symfony\Component\Console\Helper\Helper;

class SilexApplicationHelper extends Helper
{
    protected $_silexApplication;

    /**
     * Constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->_silexApplication = $app;
    }

    /**
     * Retrieves Silex Application.
     *
     * @return Application
     */
    public function getSilexApplication()
    {
        return $this->_silexApplication;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'silex_application';
    }
}
