<?php

include 'vendor/autoload.php';

define('S_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);

spl_autoload_register(
    function ($className) {

        $classFile = str_replace(['\\','Marmot\Framework\\'], ['/',''], $className) . '.class.php';
        $classFile = S_ROOT.'src/'.$classFile;

        if (file_exists($classFile)) {
            include_once $classFile;
        }
    }
);
