<?php
namespace Zee;

use Pimple;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ServiceProviderInterface;

abstract class Module extends Pimple implements ServiceProviderInterface
{
    protected $app;

    public function setup(Application $app)
    {
        $this->app = $app;

        $this['reflector'] = $this->share(function ($c) {
            return $c->getReflector();
        });

        $this['namespace'] = $this->share(function ($module) {
            return $module['reflector']->getNamespaceName();
        });

        $this['dir'] = $this->share(function ($module) {
            return $module->getDir();
        });

        $this['templates'] = $this->share(function ($c) {
            return $c->getTemplates();
        });

        $this['twig.loader.filesystem'] = $this->share(function ($c) use ($app) {
            return new \Twig_Loader_Filesystem($c['templates']);
        });

        $this['twig'] = $this->share(function ($c) use ($app) {
            $app['twig.loader']->addLoader($c['twig.loader.filesystem']);

            return $app['twig'];
        });

        $this['form.template'] = 'form.twig';

        $this['form.namespace'] = $this->share(function () {
            return $this['namespace'].'\Form';
        });

        $this['entity.namespace'] = $this->share(function () {
            return $this['namespace'].'\Entity';
        });

        $this['entity.path'] = $this->share(function ($module) {
            return realpath($module['dir'].'/Entity');
        });

        $this['entity.repository'] = $this->share(function ($module) use ($app) {
            return $app['orm.em']->getRepository($module['entity.class']);
        });

        $this['entity.mapping'] = $this->share(function ($module) {
            return array(
                'type' => 'annotation',
                'namespace' => $module['entity.namespace'],
                'path' => $module['entity.path'],
                'use_simple_annotation_reader' => false,
            );
        });

        /* Helpers */
        $this['form.render'] = $this->share(function ($module) {
            return function ($form = false) use ($module) {
                if (!$form) {
                    $form = $module['entity.form']();
                }

                return $module['twig']->render($module['form.template'], array(
                    'form' => $form->createView(),
                ));
            };
        });

        $this['entity.form'] = $this->share(function ($module) use ($app) {
            return function ($entity = false, $options = array()) use ($module, $app) {
                if (!$entity) {
                    $entity = new $module['entity.class']();
                }
                $formType = new $module['form.class']();

                return $app['form.factory']->create($formType, $entity, $options);
            };
        });

        // Mount the controller
        if (isset($this['mount']) && $this['mount']) {
            $app->mount($this['mount'], $this->getController($app));
        }
    }

    public function register(Application $app)
    {
        $this->setup($app);
    }

    public function boot(Application $app)
    {
    }

    /**
     * Get Reflector for called class
     *
     * @return \ReflectionClass
     */
    public function getReflector()
    {
        return new \ReflectionClass(get_called_class());
    }

    /**
     * Get called class dirname via Reflection
     *
     * @return string
     */
    public function getDir()
    {
        return dirname($this->getReflector()->getFileName());
    }

    /**
     * Get recursively every existing parent's called class templates folders
     *
     * @return array
     */
    public function getTemplates()
    {
        $parentClass = get_parent_class(get_called_class());
        $reflection = new \ReflectionClass($parentClass);

        if (!$reflection->isAbstract() && is_callable($parentClass.'::getTemplates')) {
            $parent = $reflection->newInstance();
            $dirs = $parent->getTemplates();
        } else {
            $dirs = array(dirname(__DIR__));
        }

        $dir = $this->getDir().'/templates';

        if (is_dir($dir)) {
            array_unshift($dirs, $dir);
        }

        return $dirs;
    }

    /**
     * @param Application $app
     *
     * @return ControllerCollection
     */
    public function getController(Application $app)
    {
        return $app['controllers_factory'];
    }
}
