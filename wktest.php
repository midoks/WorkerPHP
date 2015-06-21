<?php
/**
 *	@func workerphp 入口程序
 */


//ini_set('display_errors', 'off');
//error_reporting(0);

define('APP_PATH', realpath(dirname(__FILE__) . '/'));
define('APP_NAME', 'wktest');
include(APP_PATH.'/WorkerPHP/WorkerPHP.php');
?>
