<?php
namespace Zee\Extensions\Twig;

class TwigGetScriptsFromJsonExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'get_scripts_from_json' => new \Twig_Function_Function(array($this, 'getScriptsFromJson'), array('is_safe' => array('html'))),
        );
    }

    public function getScriptsFromJson($jsonPath = '')
    {
        $tagStart = '<script src="%s">';
        $tagEnd = '</script>';

        if (!file_exists($jsonPath)) {
            throw new \Exception(sprintf('No JSON file found in "%s".', $jsonPath));
        }

        $scripts = json_decode(file_get_contents($jsonPath));

        if (!$scripts) {
            throw new \Exception(sprintf('Failed to decode JSON in "%s".', $jsonPath));
        }

        $output = array_map(function ($script) use ($tagStart, $tagEnd) {
            return sprintf($tagStart, '/'.$script).$tagEnd;
        }, $scripts);

        return !empty($output) ? join(NL, $output) : null;
    }

    public function getName()
    {
        return 'zee.get_scripts_from_json';
    }
}
