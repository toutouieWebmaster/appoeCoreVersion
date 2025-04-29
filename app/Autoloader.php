<?php

namespace App;
class Autoloader
{
    static function register(): void
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    static function autoload($class): void
    {

        if (strpos($class, '\\')) {

            $classExplodes = explode('\\', $class);
            $classExplodesLwr = array_map('lcfirst', $classExplodes);

            $file = array_pop($classExplodesLwr);
            $class = implode('/', $classExplodesLwr);

            if (is_dir(ROOT_PATH.$class . DIRECTORY_SEPARATOR . 'class')) {
                $class .= DIRECTORY_SEPARATOR . 'class';
            }

            $class .= DIRECTORY_SEPARATOR . ucfirst($file) . '.php';
        }

        if (file_exists(ROOT_PATH . $class)) {
            require_once(ROOT_PATH . $class);
        }
    }
}