<?php

namespace Zee;

class Environment
{
    const TEST = 'test';
    const DEV = 'dev';
    const STAGING = 'staging';
    const PROD = 'prod';

    /** @var string */
    protected $environment;

    /** @var string */
    public $rootDir;

    /**
     * @param string|null $environment
     */
    public function __construct($environment = null)
    {
        // TODO: To optimize...
        $path = 'vendor'.DIRECTORY_SEPARATOR.'zee'.DIRECTORY_SEPARATOR.'silex-kit';
        $this->rootDir = strpos(__DIR__, $path) ? str_replace($path, '', __DIR__) : __DIR__;
        $this->rootDir = realpath($this->rootDir) ?: $this->rootDir;

        if ($environment) {
            return $this->setEnvironment($environment);
        }

        return $this->getEnvironment();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getEnvironment();
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        if (!empty($this->environment)) {
            return $this->environment;
        }

        $environment = getenv('APP_ENV');

        if (!$environment) {
            $environmentPath = sprintf('%s/environment.php', $this->rootDir);

            if (file_exists($environmentPath)) {
                $environment = require $environmentPath;
                putenv(sprintf('APP_ENV=%s', $environment));
            }
        }

        return $this->setEnvironment($environment);
    }

    /**
     * @param string $environment
     *
     * @return string
     *
     * @throws \Exception
     */
    public function setEnvironment($environment)
    {
        if (is_string($environment)) {
            $environment = $this->toEnvironmentConstant($environment);
        }

        return $this->environment = $environment;
    }

    /**
     * @param $environment
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function is($environment)
    {
        if (is_string($environment)) {
            $environment = $this->toEnvironmentConstant($environment);
        }

        return $this->environment === $environment;
    }

    /**
     * @param string $environment
     *
     * @return mixed
     *
     * @throws \Exception
     */
    private function toEnvironmentConstant($environment = '')
    {
        if (!is_string($environment) || empty($environment)) {
            throw new \Exception(sprintf('Environment passed to %s must be a string', __METHOD__));
        }

        $environment = sprintf('%s::%s', __CLASS__, strtoupper($environment));

        if (!defined($environment)) {
            throw new \Exception(sprintf('Invalid Environment given: %s', $environment));
        }

        return constant($environment);
    }
}
