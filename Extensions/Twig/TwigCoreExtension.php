<?php
namespace Zee\Extensions\Twig;

class TwigCoreExtension extends \Twig_Extension
{
    public function getGlobals()
    {
        global $app;

        return array(
            'app' =>        $app,
            'uri' =>        isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : null,
            'root_uri' =>   ROOT_URI,
            'root_path' =>  ROOT_PATH,
            'now' =>        time(),
        );
    }

    // Utils
    public function getFunctions()
    {
        return array(
            't' =>              new \Twig_Function_Function('t'),
            'print_r' =>        new \Twig_Function_Function('print_r'),
            'var_dump' =>       new \Twig_Function_Function('var_dump'),
            'prettyprint' =>    new \Twig_Function_Function('prettyprint', array('is_safe' => array('html'))),
            'uniqid' =>         new \Twig_Function_Function('uniqid'),
            'json_encode' =>    new \Twig_Function_Function('json_encode'),
            'reset' =>          new \Twig_Function_Function('reset'),
        );
    }

    public function getFilters()
    {
        return array(
            't' =>              new \Twig_Filter_Function('t'),
            'print_r' =>        new \Twig_Filter_Function('print_r'),
            'var_dump' =>       new \Twig_Filter_Function('var_dump'),
            'prettyprint' =>    new \Twig_Filter_Function('prettyprint', array('is_safe' => array('html'))),
            'uniqid' =>         new \Twig_Filter_Function('uniqid'),
            'first' =>          new \Twig_Filter_Function('reset'),
        );
    }

    public function getName()
    {
        return 'app';
    }
}
