<?php
namespace Zee;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

class Composer
{
    private static $nl = "\n";
    private static $ok = "\033[0;32m OK \033[0m\t";
    private static $nok = "\033[0;31m NOK \033[0m\t";
    private static $error = false;

    public static function postInstall()
    {
        echo self::$nl;
        $dirs = array(
            'app/cache',
            'app/cache/twig',
            'app/logs',
        );

        $fs = new Filesystem();

        foreach ($dirs as $dir) {
            if (!$fs->exists($dir)) {
                try {
                    $fs->mkdir($dir, 0777);
                    echo self::$ok.'create directory '.$dir.self::$nl;
                } catch (IOException $e) {
                    self::$error = true;
                    echo self::$nok.'failed to create directory '.$dir.self::$nl;
                }
            }

            if (is_writable($dir)) {
                echo self::$ok.$dir." is writable".self::$nl;
            } else {
                self::$error = true;
                echo self::$nok.$dir." is NOT writable".self::$nl;
            }
        }

        if (self::$error) {
            echo self::$nok.'Something FAILED :(';
        } else {
            echo self::$ok.'Done :)';
        }

        echo self::$nl.self::$nl;

        self::copyEnvironment($fs);
    }

    public static function copyEnvironment(Filesystem $fs)
    {
        if ($fs->exists('environment.php')) {
            return;
        }

        try {
            $fs->copy('environment.php.dist', 'environment.php');
            echo self::$ok.'environment.php successfully created!';
        } catch (\Exception $e) {
            echo self::$nok.'couldn\'t create environment.php!'.self::$nl;
            echo "\t".$e->getMessage();
        }

        echo self::$nl.self::$nl;
    }
}
