<?php
namespace Marmot\Framework\Common\Model;

class MockNullOperateObject
{
    use NullOperateTrait;

    public function publicResourceNotExist() : bool
    {
        return $this->resourceNotExist();
    }
}
