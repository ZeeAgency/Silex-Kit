<?php
namespace Zee\Extensions\Twig;

class TwigMathExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'ceil' =>   new \Twig_Function_Function('ceil'),
            'floor' =>  new \Twig_Function_Function('floor'),
            'round' =>  new \Twig_Function_Function('round'),
            'abs' =>    new \Twig_Function_Function('abs'),
        );
    }

    public function getFilters()
    {
        return array(
            'ceil' =>   new \Twig_Filter_Function('ceil'),
            'floor' =>  new \Twig_Filter_Function('floor'),
            'round' =>  new \Twig_Filter_Function('round'),
            'abs' =>    new \Twig_Filter_Function('abs'),
        );
    }

    public function getName()
    {
        return 'app.math';
    }
}
