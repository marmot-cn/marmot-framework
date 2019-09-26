<?php
namespace Marmot\Framework;

use Marmot\Interfaces\Application\IApplication;
use Marmot\Framework\MarmotCore;

class MockMarmotCore extends MarmotCore
{
    protected function initApplication() : void
    {
        $this->application = new MockApplication();
    }

    protected function getApplication() : IApplication
    {
        return $this->application;
    }
    
    public function initApplicationPublic() : void
    {
        $this->initApplication();
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

    public function isPublicMockedErrorRoute()
    {
        return parent::isMockedErrorRoute();
    }
}
