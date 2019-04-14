<?php
namespace Marmot\Framework\Interfaces;

interface IErrorAdapter
{
	public function lastErrorId() : int;

	public function lastErrorInfo() : array;
}

