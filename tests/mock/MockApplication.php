<?php
namespace Marmot\Framework;

use Marmot\Framework\Application\IApplication;

class MockApplication implements IApplication
{
    public function getIndexRoute() : array
    {
        return [];
    }

    public function getRouteRules() : array
    {
        return [];
    }

    public function initErrorConfig() : void
    {
        define('ERROR_TEST', 999);
    }

    public function getErrorDescriptions() : array
    {
        return [
            ERROR_TEST=>
                [
                    'id'=>ERROR_TEST,
                    'link'=>'',
                    'status'=>500,
                    'code'=>ERROR_TEST,
                    'title'=>'error test',
                    'detail'=>'error test',
                    'source'=>[],
                    'meta'=>[]
                ]
        ];
    }

    public function initConfig() : void
    {
    }
}
