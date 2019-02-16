<?php
namespace Marmot\Framework\Classes;

class Server
{
    public static function host() : string
    {
        return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    }
}