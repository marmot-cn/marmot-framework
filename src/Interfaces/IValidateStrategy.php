<?php
namespace Marmot\Framework\Interfaces;

interface IValidateStrategy
{
    public function validate($verifyValue, string $options = '', int $errorCode = 0) : bool;

    public function typeRule() : bool;
}
