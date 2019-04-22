<?php
namespace Marmot\Framework;

use Marmot\Framework\Application\IApplication;

class MockMarmotCore extends \Marmot\Framework\MarmotCore
{
    protected function initApplication() : void
    {
        $this->application = new MockApplication();
    }

    protected function getApplication() : IApplication
    {
        return $this->application;
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
        return '';
    }

    protected function initAutoload()
    {
    }
}
