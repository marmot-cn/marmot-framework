<?php
namespace Marmot\Framework\Query;

use Marmot\Framework\Interfaces\DbLayer;

class MockDBVectorQuery extends DBVectorQuery
{
    public function getDbLayer() : DbLayer
    {
        return parent::getDbLayer();
    }
}
