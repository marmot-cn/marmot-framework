<?php
ini_set("display_errors","on");

return [
	//database
    'database.host'     => '',
    'database.port'     => 0,
    'database.dbname'   => '',
    'database.user'		=> '',
    'database.password'	=> '',
    'database.tablepre' => '',
    //mongo
    'mongo.host' => '',
    'mongo.uriOptions' => [
    ],
    'mongo.driverOptions' => [
    ],
    //cache
    'cache.route.disable' => true,
    //memcached
    'memcached.service'=>[],
];
