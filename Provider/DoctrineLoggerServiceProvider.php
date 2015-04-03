<?php
namespace Zee\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Doctrine\DBAL\Logging\DebugStack;

class DoctrineLoggerServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        if ($app['debug']) {
            $logger = new DebugStack();
            $app['db.config']->setSQLLogger($logger);

            $app->error(function (\Exception $e) use ($app, $logger) {
                if ($e instanceof PDOException && count($logger->queries)) {
                    $query = array_pop($logger->queries);
                    $app['monolog']->err($query['sql'], array(
                        'params' => $query['params'],
                        'types' => $query['types'],
                    ));
                }
            });

            if ($app['config']['doctrine_debug']) {
                $app->after(function () use ($app, $logger) {
                    foreach ($logger->queries as $query) {
                        $app['monolog']->debug($query['sql'], array(
                            'params' => $query['params'],
                            'types' => $query['types'],
                        ));
                    }
                });
            }
        }
    }

    public function boot(Application $app)
    {
    }
}
