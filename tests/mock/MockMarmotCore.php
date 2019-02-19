<?php
namespace Marmot\Framework;

class MockMarmotCore extends \Marmot\Framework\MarmotCore
{
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
}
