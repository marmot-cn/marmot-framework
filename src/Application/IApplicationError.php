<?php
namespace Marmot\Framework\Application;

interface IApplicationError
{
    public function initErrorConfig() : void;

    public function getErrorDescriptions() : array;
}
