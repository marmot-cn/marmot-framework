<?php
namespace Marmot\Framework;

use Marmot\Interfaces\Application\IFramework;

/**
 * @codeCoverageIgnore
 */

define('SDK_ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);

class Framework implements IFramework
{
    public function initErrorConfig() : void
    {
        include_once SDK_ROOT.'errorConfig.php';
    }

    public function getErrorDescriptions() : array
    {
        return include_once SDK_ROOT.'./errorDescriptionConfig.php';
    }

    public function initConfig() : void
    {
        include_once SDK_ROOT.'config.php';
    }
}
