<?php
namespace Marmot\Application;

use Marmot\Interfaces\Application\IApplication;

define('APPLICATION_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);

class Application implements IApplication
{
    public function getIndexRoute() : array
    {
        return ['GET', '/', ['Home\Controller\IndexController','index']];
    }

    public function getRouteRules() : array
    {
        return include_once APPLICATION_ROOT.'routeRules.php';
    }

    public function initErrorConfig() : void
    {
        include_once APPLICATION_ROOT.'errorConfig.php';
    }

    public function getErrorDescriptions() : array
    {
        return include_once APPLICATION_ROOT.'./errorDescriptionConfig.php';
    }

    public function initConfig() : void
    {
        include_once APPLICATION_ROOT.'config.php';
    }
}
