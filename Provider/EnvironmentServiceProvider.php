<?php
namespace Zee\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Igorw\Silex\ConfigServiceProvider;

class EnvironmentServiceProvider implements ServiceProviderInterface
{
    /** @var Application $app */
    protected $app;

    public function register(Application $app)
    {
        $this->app = $app;
        $this->setupConstants();
        $this->setupEnvironment();
        $this->loadConfs();

        $app['debug'] = $app['config']['debug'];

        $this->setupSystem();
    }

    public function boot(Application $app)
    {
    }

    public function setupConstants()
    {
        if (!defined('ROOT_URI')) {
            define('ROOT_URI', str_replace('\\', '', str_replace('//', '/', dirname($_SERVER['SCRIPT_NAME']).'/')));
        }
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', realpath(__DIR__.'/../../../../'));
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

    public function setupEnvironment()
    {
        $env = getenv('APP_ENV');

        if (!$env) {
            $environment = ROOT_PATH.'/environment.php';

            if (file_exists($environment)) {
                $env = require $environment;
                putenv(sprintf('APP_ENV=%s', $env));
            }
        }

        $this->app['env'] = $this->app->share(function () use ($env) {
            return $env ?: 'prod';
        });
    }

    public function loadConfs()
    {
        $conf = ROOT_PATH.'/app/config';

        if (!file_exists($conf.'/parameters.yml')) {
            throw new \Exception("parameters.yml can't be found. You must do a composer install / update.", true);
        }

        $this->app->register(new ConfigServiceProvider($conf.'/parameters.yml'));

        $replacements = $this->app['parameters'];
        $replacements['root_path'] = ROOT_PATH;

        $this->app->register(new ConfigServiceProvider($conf.'/config.yml', $replacements));
        $this->app->register(new ConfigServiceProvider($conf.'/config_'.$this->app['env'].'.yml', $replacements));
    }

    public function setupSystem()
    {
        set_include_path(implode(PATH_SEPARATOR, array(getcwd(), ROOT_PATH, get_include_path())));
        ini_set('display_errors', $this->app['debug'] ? 'On' : 'Off');
        ini_set('memory_limit', $this->app['config']['system']['memory_limit']);
        ini_set('max_execution_time', $this->app['config']['system']['max_execution_time']);
        date_default_timezone_set($this->app['config']['timezone']);
    }
}
