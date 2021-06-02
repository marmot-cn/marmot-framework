<?php

include './vendor/autoload.php';
require './Core.php';
require 'DbTrait.php';

$testCore = Marmot\Core::getInstance();
$testCore->initTest();
