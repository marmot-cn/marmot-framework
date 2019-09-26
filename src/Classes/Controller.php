<?php
//powered by chloroplast
namespace Marmot\Framework\Classes;

use Marmot\Basecode\Classes\Controller as BaseController;

/**
 * 应用层服务父类,控制应用服务层的 Request 和 Reponse
 */
abstract class Controller extends BaseController
{
    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
    }
}
