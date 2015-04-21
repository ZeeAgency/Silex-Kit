<?php
namespace Zee;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

class Composer
{
    protected static $nl = "\n";
    protected static $ok = "\033[0;32m OK \033[0m\t";
    protected static $nok = "\033[0;31m NOK \033[0m\t";
    protected static $error = false;

    public static function getDirs()
    {
        return array(
            'app/cache',
            'app/cache/twig',
            'app/logs',
        );
    }

    public static function postInstall()
    {
        echo static::$nl;
        $dirs = static::getDirs();

        $fs = new Filesystem();

        foreach ($dirs as $dir) {
            if (!$fs->exists($dir)) {
                try {
                    $fs->mkdir($dir);
                    $fs->chmod($dir, 0777, 0000);
                    echo static::$ok.'create directory '.$dir.static::$nl;
                } catch (IOException $e) {
                    static::$error = true;
                    echo static::$nok.'failed to create directory '.$dir.static::$nl;
                }
            }

            if ($fs->exists($dir) && is_readable($dir) && is_writable($dir)) {
                echo static::$ok.$dir." is writable".static::$nl;
            } else {
                static::$error = true;
                echo static::$nok.$dir." is NOT writable".static::$nl;
            }
        }

        if (static::$error) {
            echo static::$nok.'Something FAILED :(';
        } else {
            echo static::$ok.'Done :)';
        }

        echo static::$nl.static::$nl;

        static::copyEnvironment($fs);
    }

    public static function copyEnvironment(Filesystem $fs)
    {
        if ($fs->exists('environment.php')) {
            return;
        }

        try {
            $fs->copy('environment.php.dist', 'environment.php');
            echo static::$ok.'environment.php successfully created!';
        } catch (\Exception $e) {
            echo static::$nok.'couldn\'t create environment.php!'.static::$nl;
            echo "\t".$e->getMessage();
        }

        echo static::$nl.static::$nl;
    }
}
