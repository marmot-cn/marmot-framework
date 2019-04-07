<?php
namespace Marmot\Framework;

use Marmot\Framework\Application\IApplication;

class MockMarmotCore extends \Marmot\Framework\MarmotCore
{
    protected function getApplication() : IApplication
    {
        return new MockApplication();
    }

    public function initEnv()
    {
        parent::initEnv();
    }

    public function initError()
    {
        parent::initError();
    }

    protected function initDb()
    {
    }

    protected function initCache()
    {
    }

    protected function getAppPath() : string
    {
    }

    protected function initAutoload()
    {
    }
}
