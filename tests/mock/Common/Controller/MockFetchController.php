<?php
namespace Marmot\Framework\Common\Controller;

use Marmot\Interfaces\IView;
use Marmot\Framework\Classes\Controller;

use Marmot\Framework\Common\View\MockView;
use Marmot\Framework\Common\Repository\MockRepository;
use Marmot\Framework\Common\Controller\FetchControllerTrait;

class MockFetchController extends Controller
{
    use FetchControllerTrait;

    public function generateView($data) : IView
    {
        return new MockView($data);
    }

    public function getResourceName() : string
    {
        return 'resource';
    }

    public function getRepositoryPublic()
    {
        return $this->getRepository();
    }
}
