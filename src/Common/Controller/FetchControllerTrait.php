<?php
namespace Marmot\Framework\Common\Controller;

use Marmot\Interfaces\IView;
use Marmot\Interfaces\INull;

use Marmot\Core;

use Marmot\Framework\Classes\Repository;
use Marmot\Framework\Controller\JsonApiTrait;

trait FetchControllerTrait
{
    use JsonApiTrait;

    abstract protected function generateView($data) : IView;

    abstract protected function getResourceName() : string;

    protected function getRepository()
    {
    }

    public function fetchOne(int $id)
    {
        $object = $this->getRepository()->fetchOne($id);

        if (!$object instanceof INull) {
            $this->renderView($this->generateView($object));
            return true;
        }

        Core::setLastError(RESOURCE_NOT_EXIST);
        $this->displayError();
        return false;
    }

    public function fetchList(string $ids)
    {
        $ids = explode(',', $ids);

        $objectList = array();

        $objectList = $this->getRepository()->fetchList($ids);

        if (!empty($objectList)) {
            $this->renderView($this->generateView($objectList));
            return true;
        }

        Core::setLastError(RESOURCE_NOT_EXIST);
        $this->displayError();
        return false;
    }

    public function filter()
    {
        list($filter, $sort, $curpage, $perpage) = $this->formatParameters();

        list($objectList, $count) = $this->getRepository()->filter(
            $filter,
            $sort,
            ($curpage-1)*$perpage,
            $perpage
        );

        if ($count > 0) {
            $view = $this->generateView($objectList);
            $view->pagination(
                $this->getResourceName(),
                $this->getRequest()->get(),
                $count,
                $perpage,
                $curpage
            );
            $this->renderView($view);
            return true;
        }

        $this->displayError();
        return false;
    }
}
