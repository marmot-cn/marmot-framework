<?php
namespace Marmot\Framework\Application;

interface IApplicationRoute
{
	public function getIndexRoute() : array;

    public function getRouteRules() : array;
}
