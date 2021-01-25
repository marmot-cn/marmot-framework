<?php
namespace Marmot\Framework\Common\Model;

use Marmot\Core;

trait NullOperateTrait
{
    public function add() : bool
    {
        return $this->resourceNotExist();
    }

    public function edit() : bool
    {
        return $this->resourceNotExist();
    }

    protected function resourceNotExist() : bool
    {
        Core::setLastError(RESOURCE_NOT_EXIST);
        return false;
    }
}
