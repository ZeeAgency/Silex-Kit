<?php
namespace Zee;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Silex\Application;

abstract class Controller
{
    /** @var Application $app */
    protected $app;

    /** @var Module $module */
    protected $module;

    /** @var \Twig_Environment $twig */
    protected $twig;

    /** @var EntityManager $em */
    protected $em;

    /** @var EntityRepository $repo */
    protected $repo;

    /**
     * @param Application $app
     * @param Module      $module
     */
    public function __construct(Application $app, Module $module)
    {
        $this->app = $app;
        $this->module = $module;
        $this->twig = $this->getTwig();
        $this->repo = $this->getRepo();
        $this->em = $this->getEm();
    }

    /**
     * @return \Twig_Environment
     */
    protected function getTwig()
    {
        return $this->module['twig'];
    }

    /**
     * @return EntityRepository
     */
    protected function getRepo()
    {
        return $this->module['entity.repository'];
    }

    /**
     * @return EntityManager
     */
    protected function getEm()
    {
        return $this->app['orm.em'];
    }
}
