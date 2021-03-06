<?php

namespace Zee\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Igorw\Silex\ConfigServiceProvider;
use Zee\Environment;

class EnvironmentServiceProvider implements ServiceProviderInterface
{
    /** @var Application $app */
    protected $app;

    public function register(Application $app)
    {
        $this->app = $app;
        $this->app['env'] = $this->app->share(function () {
            return new Environment();
        });

        $this->setupConstants();
        $this->loadConfigurations();

        $this->app['debug'] = $this->app['config']['debug'];

        $this->setupSystem();
    }

    public function boot(Application $app)
    {
    }

    protected function setupConstants()
    {
        if (!defined('ROOT_URI')) {
            define('ROOT_URI', str_replace('\\', '', str_replace('//', '/', dirname($_SERVER['SCRIPT_NAME']).'/')));
        }
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', $this->app['env']->rootDir ?: realpath(__DIR__.'/../../../../'));
        }
        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }
        if (!defined('BR')) {
            define('BR', "\n<br/>\n");
        }
        if (!defined('NL')) {
            define('NL', "\n");
        }
        if (!defined('TAB')) {
            define('TAB', "\t");
        }
    }

    protected function loadConfigurations()
    {
        $conf = ROOT_PATH.'/app/config';

        $parametersPath = sprintf('%s/parameters.yml', $conf);

        if (!file_exists($parametersPath)) {
            throw new \Exception("parameters.yml can't be found. You must do a composer install / update", true);
        }

        $this->app->register(new ConfigServiceProvider($parametersPath));

        $replacements = $this->app['parameters'];
        $replacements['root_path'] = ROOT_PATH;

        $this->app->register(new ConfigServiceProvider(sprintf('%s/config.yml', $conf), $replacements));
        $this->app->register(new ConfigServiceProvider(sprintf('%s/config_%s.yml', $conf, $this->app['env']), $replacements));
    }

    protected function setupSystem()
    {
        set_include_path(implode(PATH_SEPARATOR, array(getcwd(), ROOT_PATH, get_include_path())));
        ini_set('display_errors', $this->app['debug'] ? 'On' : 'Off');

        if (isset($this->app['config']['system']['memory_limit'])) {
            ini_set('memory_limit', $this->app['config']['system']['memory_limit']);
        }

        if (isset($this->app['config']['system']['max_execution_time'])) {
            ini_set('max_execution_time', $this->app['config']['system']['max_execution_time']);
        }

        date_default_timezone_set($this->app['config']['timezone']);
    }
}
