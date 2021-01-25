<?php
namespace Marmot\Framework\Common\Model;

use Marmot\Core;

use Marmot\Common\Model\Object;
use Marmot\Common\Model\IObject;

class MockObject implements IObject
{
    use Object;

    /**
     * @var int $id
     */
    protected $id;

    public function __construct(int $id = 0)
    {
        $this->id = !empty($id) ? $id : 0;
        $this->status = 0;
        $this->statusTime = 0;
        $this->createTime = Core::$container->get('time');
        $this->updateTime = Core::$container->get('time');
    }

    public function __destruct()
    {
        unset($this->id);
        unset($this->status);
        unset($this->statusTime);
        unset($this->createTime);
        unset($this->updateTime);
    }

    public function setId($id) : void
    {
        $this->id = $id;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function setStatus(int $status) : void
    {
        $this->status = $status;
    }
}
