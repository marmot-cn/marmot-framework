<?php
namespace Marmot\Framework\Common\Model;

trait OperateTrait
{
    public function add() : bool
    {
        return $this->addAction();
    }

    public function edit() : bool
    {
        return $this->editAction();
    }

    abstract protected function addAction() : bool;

    abstract protected function editAction() : bool;
}
