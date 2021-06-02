<?php
ini_set("display_errors","on");

return [
	//database
    'database.host'     => 'mysql',
    'database.port'     => 3306,
    'database.dbname'   => 'marmot_framework',
    'database.user'		=> 'root',
    'database.password'	=> '123456',
    'database.tablepre' => 'pcore_',
    //memcached
    'memcached.service'=>[['memcached-1',11211],['memcached-2',11211]],
];
