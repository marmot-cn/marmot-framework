<?php
namespace Marmot\Framework\Common\Adapter;

use Marmot\Common\Model\IObject;

use Marmot\Interfaces\ITranslator;
use Marmot\Interfaces\INull;

use Marmot\Framework\Interfaces\IRowQuery;

use Common\Model\MockNullObject;

class MockDbAdapter
{
    use DbAdapterTrait;

    protected function getDbTranslator() : ITranslator
    {
    }

    protected function getRowQuery() : IRowQuery
    {
    }

    protected function getNullObject() : INull
    {
    }

    public function add(IObject $object) : bool
    {
        return $this->addAction($object);
    }

    public function edit(IObject $object, array $keys = array()) : bool
    {
        return $this->editAction($object, $keys);
    }

    public function fetchOne($id)
    {
        return $this->fetchOneAction($id);
    }

    public function fetchList($ids)
    {
        return $this->fetchListAction($ids);
    }

    protected function formatFilter() : string
    {
        return '';
    }

    protected function formatSort() : string
    {
        return '';
    }
}
