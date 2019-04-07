<?php
namespace Marmot\Framework;

use Marmot\Framework\Application\IApplication;

class MockApplication implements IApplication
{
    public function getRouteRules() : array
    {
        return [];
    }

    public function initErrorConfig() : void
    {
    }

    public function getErrorDescriptions() : array
    {
        return [];
    }

    public function initConfig() : void
    {
    }
}
