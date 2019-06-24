<?php
namespace Marmot\Framework\Interfaces;

interface IRestfulTranslator extends ITranslator
{
    public function arrayToObjects(array $expression) : array;
}
