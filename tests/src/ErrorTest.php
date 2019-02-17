<?php
namespace Marmot\Framework;

use Marmot\Core;
use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
{
    public function setUp()
    {
        Core::setLastError(ERROR_NOT_DEFINED);
    }

    public function tearDown()
    {
        Core::setLastError(ERROR_NOT_DEFINED);
    }

    /**
     * @dataProvider errorDescriptionConfigProvider
     */
    public function testErrorDescriptionConfig($id, $expectedId, $expectedStatus)
    {
        Core::setLastError($id);
        $this->assertEquals($expectedId, Core::getLastError()->getId());
        $this->assertEquals($expectedStatus, Core::getLastError()->getStatus());
    }

    public function errorDescriptionConfigProvider()
    {
        return [
            array(-1, ERROR_NOT_DEFINED, 500),
            array(ERROR_NOT_DEFINED, 0, 500),
            array(INTERNAL_SERVER_ERROR, 1, 500),
            array(ROUTE_NOT_EXIST, 2, 404),
            array(METHOD_NOT_ALLOWED, 3, 405),
            array(UNSUPPORTED_MEDIA_TYPE, 4, 415),
            array(NOT_ACCEPTABLE_MEDIA_TYPE, 5, 406),
            array(INCORRECT_RAW_BODY, 6, 400),
            array(RESOURCE_NOT_EXIST, 10, 404),
            array(COMMAND_HANDLER_NOT_EXIST, 11, 404),
            array(TRANSLATOR_NOT_EXIST, 12, 404),
        ];
    }
}
