<?php
/**
 *	@func 	工作者的框架
 *	@email 	midoks@163.com
 *	@time   2015-4-9
 */
!defined('APP_NAME') && exit('Forbidden');

define('ABSPATH', 	str_replace('\\', '/', dirname(__FILE__)).'/');

/**
 *	框架里的文件夹
 */
define('WK_CORE', 		ABSPATH.'core/');
define('WK_LIB',		ABSPATH.'lib/');


/**
 * APP 应用文件夹
 */
define('APP_DIR', 		dirname(ABSPATH).'/'.APP_NAME.'/');
define('APP_CTR_DIR',	APP_DIR.'controllers/');
define('APP_MOD_DIR',	APP_DIR.'modules/');
define('APP_COMMON',	APP_DIR.'common/');
define('APP_LIB',		APP_DIR.'lib/');

//设置搜索路径
$include_path = get_include_path();

//把common引入include_path
$include_path = $include_path.PATH_SEPARATOR.APP_COMMON;
$include_path = $include_path.PATH_SEPARATOR.APP_LIB;
set_include_path($include_path);

define('WK_ERR_EXCEPTION', 0);


//运行应用
include(WK_CORE.'AppStart.class.php');
AppStart::run();
?>
