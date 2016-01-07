<?php
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_WARNING);

define('APPLICATION_PATH', dirname(__DIR__) . '/');           /* 根目录 */
define('PHPUNIT_TEST', true);
require_once APPLICATION_PATH . '/vendor/autoload.php';