<?php
namespace Marmot\Framework\Interfaces;

interface ISdk
{
    public function getUri() : string;

    public function getAuthKey() : array;
}
