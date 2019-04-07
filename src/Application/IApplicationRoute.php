<?php
namespace Marmot\Framework\Application;

interface IApplicationRoute
{
    public function getRouteRules() : array;
}
