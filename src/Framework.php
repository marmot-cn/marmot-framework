<?php
namespace Marmot\Framework;

use Marmot\Interfaces\Application\IFramework;

define('FRAMEWORK_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);

class Framework implements IFramework
{
    public function initErrorConfig() : void
    {
        include_once FRAMEWORK_ROOT.'errorConfig.php';
    }

    public function getErrorDescriptions() : array
    {
        return include FRAMEWORK_ROOT.'./errorDescriptionConfig.php';
    }

    public function initConfig() : void
    {
        include_once FRAMEWORK_ROOT.'config.php';
    }
}
