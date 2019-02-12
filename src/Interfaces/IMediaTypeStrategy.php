<?php
namespace Marmot\Framework\Interfaces;

use Marmot\Framework\Classes\Request;

interface IMediaTypeStrategy
{
    public function validate(Request $request) : bool;

    public function decode($rawData);
}
