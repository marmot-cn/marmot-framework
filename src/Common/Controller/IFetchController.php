<?php
namespace Marmot\Framework\Common\Controller\Interfaces;
          
interface IFetchController
{
    public function fetchOne(int $id);

    public function fetchList(string $ids);
    
    public function filter();
}
