<?php
//powered by chloroplast
namespace Marmot\Framework\Classes;

abstract class Repository
{
	abstract protected function getActualAdapter();

	abstract protected function getMockAdapter();

	protected function getAdapter()
	{
		return $this->isMocked()? $this->getMockAdapter() : $this->getActualAdapter();
	}

	private function isMocked()
	{
		$mockStatus = Server::get('HTTP_MOCK_STATUS', 0);
		return $mockStatus > 0;
	}
}