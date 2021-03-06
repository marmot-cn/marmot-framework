<?php
namespace Marmot\Framework\Controller;

use Marmot\Framework\Controller\JsonApiTrait;
use Marmot\Framework\Classes\Controller;

class ErrorController extends Controller
{
    use JsonApiTrait;

    /**
     * @codeCoverageIgnore
     */
    public function error()
    {
        $this->displayError();
        return false;
    }
}
