<?php
namespace Marmot\Framework\Common\Adapter;

use Marmot\Interfaces\ITranslator;
use Marmot\Interfaces\INull;

use Marmot\Framework\Interfaces\IRowQuery;
use Marmot\Common\Model\IObject;

use Marmot\Core;

trait DbAdapterTrait
{
    abstract protected function getDbTranslator() : ITranslator;

    abstract protected function getRowQuery() : IRowQuery;

    abstract protected function getNullObject() : INull;

    protected function addAction(IObject $object) : bool
    {
        $info = array();
        $info = $this->getDbTranslator()->objectToArray($object);
        $id = $this->getRowQuery()->add($info);
        if (!$id) {
            return false;
        }

        $object->setId($id);
        return true;
    }

    protected function editAction(IObject $object, array $keys = array()) : bool
    {
        $info = array();
        $info = $this->getDbTranslator()->objectToArray($object, $keys);

        $rowQuery = $this->getRowQuery();
        $conditionArray[$rowQuery->getPrimaryKey()] = $object->getId();

        return $rowQuery->update($info, $conditionArray);
    }

    protected function fetchOneAction($id) : IObject
    {
        $info = array();

        $info = $this->getRowQuery()->fetchOne($id);
        if (empty($info)) {
            Core::setLastError(RESOURCE_NOT_EXIST);
            return $this->getNullObject();
        }

        $object = $this->getDbTranslator()->arrayToObject($info);
        return $object;
    }

    protected function fetchListAction(array $ids) : array
    {
        $objectList = array();
        
        $objectInfoList = $this->getRowQuery()->fetchList($ids);
        if (empty($objectInfoList)) {
            Core::setLastError(RESOURCE_NOT_EXIST);
            return array();
        }

        $translator = $this->getDbTranslator();
        foreach ($objectInfoList as $objectInfo) {
            $object = $translator->arrayToObject($objectInfo);
            $objectList[$object->getId()] = $object;
        }

        return $objectList;
    }

    public function filter(
        array $filter = array(),
        array $sort = array(),
        int $offset = 0,
        int $size = 20
    ) : array {

        $condition = $this->formatFilter($filter);
        $condition .= $this->formatSort($sort);

        $rowQuery = $this->getRowQuery();
        $list = $rowQuery->find($condition, $offset, $size);

        if (empty($list)) {
            Core::setLastError(RESOURCE_NOT_EXIST);
            return array(array(), 0);
        }

        $ids = array();
        $primaryKey = $rowQuery->getPrimaryKey();
        foreach ($list as $info) {
            $ids[] = $info[$primaryKey];
        }

        $count = 0;
        $count = sizeof($ids);
        if ($count  == $size || $offset > 0) {
            $count = $rowQuery->count($condition);
        }

        return array($this->fetchList($ids), $count);
    }

    abstract protected function formatFilter() : string;

    abstract protected function formatSort() : string;
}
