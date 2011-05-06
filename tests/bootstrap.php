<?php

/*
 * This file is part of the Metadata library.
 *
 *    (C) 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 */

spl_autoload_register(function($class)
{
    if (0 === strpos($class, 'Metadata\Tests\\')) {
        $path = __DIR__.'/../tests/'.strtr($class, '\\', '/').'.php';
        if (file_exists($path) && is_readable($path)) {
            require_once $path;

            return true;
        }
    } else if (0 === strpos($class, 'Metadata\\')) {
        $path = __DIR__.'/../src/'.($class = strtr($class, '\\', '/')).'.php';
        if (file_exists($path) && is_readable($path)) {
            require_once $path;

            return true;
        }
    }
});

