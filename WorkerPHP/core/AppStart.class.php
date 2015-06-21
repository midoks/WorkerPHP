<?php
/**
 * 应用启动控制
 */


class AppStart{

	/**
	 *	@func 应用启动之前
	 */
	public static function run_before(){
		include(ABSPATH.'common/functions.php');
		I(WK_CORE.'WorkerController.class.php');
		I(WK_CORE.'WorkerErrorHandler.class.php');
		C(ABSPATH.'common/config.php');
		C(APP_COMMON.'config.php');
		I(APP_COMMON.'functions.php');

		spl_autoload_register(array(__CLASS__, 'autoload'));
		set_error_handler(array(__CLASS__,'error'));
		set_exception_handler(array(__CLASS__,'error'));

		//设置时区
		date_default_timezone_set(C('timezone'));

		header('WKer Framework Author: midoks');
		header('Wker Blog: midoks.cachecha.com');

		//开发日志
		C('app_debug') ? self::run_debug() : '';
		session_start();
	}

	/**
	 *	@func 调试模式设置
	 */
	public static function run_debug(){
		//设置系统
		$time = date('Y-m-d');
		
		//强制显示所有错误
		error_reporting(E_ALL); 
		ini_set('display_errors', '1');

		//把错误信息放在应用目录中.便于调试
		ini_set('error_log', APP_DIR.'debug/syslog/sys-'.$time.'.log');

		//把session放在应用目录中。便于调试
		ini_set('session.save_path', APP_DIR.'debug/session');
	}
    
    //应用启动之后
	public static function run_after(){
		spl_autoload_register(array('AppStart', 'not_found'));
	}
	
    //项目启动
	public  static function run(){
		self::run_before();

		$mode = C('url_mode');
		if($mode == 1){
		
			$uri = isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : '';
			$uri = trim($uri, '/');
			$uri_list = explode('/', $uri, 3);

			$controllerName = !empty($uri_list[0]) ? $uri_list[0] : 'index';
			$methodName 	= !empty($uri_list[1]) ? $uri_list[1] : 'index';
		}else{
			$controllerName = isset($_GET['c']) ? $_GET['c'] : 'index';
			$methodName 	= isset($_GET['m']) ? $_GET['m'] : 'index';
		}

		//屏蔽图标icon请求
		if($controllerName == 'favicon.ico'){
			return;
		}

		define('APP_CTR_NAME', ucfirst($controllerName));
		define('APP_METHOD_NAME', ucfirst($methodName));
		
		self::runStart(APP_CTR_NAME, APP_METHOD_NAME);
        self::run_after();
	}


	/**
	 *	@func 启动
	 */
    public static function runStart($controller, $method){
    	$_controller = $controller.'Controller';
		$_controller = self::singleton($_controller);

		$_exists = method_exists($_controller, $method);
		if(!$_exists) $method = '_404';

		$_m_begin = '_begin';
        if(method_exists($_controller, $_m_begin)){
            $_controller->$_m_begin();
		}

		//调用函数之前
        $_m_before = '__'.$method.'__before';
        if(method_exists($_controller, $_m_before)){
            $_controller->$_m_before();
		}

		//调用函数
		$_controller->$method();
        
        //调用函数之后
        $_m_after = '__'.$method.'__after';
        if(method_exists($_controller, $_m_after)){
			$_controller->$_m_after();
		}

		$_m_end = '_end';
        if(method_exists($_controller, $_m_end)){
            $_controller->$_m_end();
		}
		
		$_controller->display($method);
    }

	//错误设置
	public static function error($e){
		$obj = WorkerErrorHandler::getInstance();
		$obj->setError(func_get_args());		
	}

	//加载系统类
	//只加载Controller和Model类 | Handler
	public static function autoload($className){		
        //控制器
		if(substr($className, -10) == 'Controller'){
			self::find_str($className);
		}

		//模型
		if(substr($className, -5) == 'Model'){
			$model = APP_MOD_DIR.$className.'.php';
			if(file_exists($model)){
        		I($model);
			}else{
				$core_model = WK_CORE.$className.'.class.php';
				if(file_exists($core_model)){
					I($core_model);
				}else{
					wk_error("not found model {$className} file");
				}
			}
		}

		//handler
		if(substr($className, -7) == 'Handler'){
			$core_model = WK_CORE.$className.'.class.php';
			if(file_exists($core_model)){
				I($core_model);
			}else{
				wk_error("not found Handler {$className} file");
			}
		}
	}
	
	//没有找到后处理方式
	public static function not_found(){}

	//查找控制器
	private static function find_ctr($className){
		$ctr = str_replace('Controller', '', $className);
		$ctr = APP_CTR_DIR.$ctr.'.php';
        if(file_exists($ctr)){
			include($ctr);
			return $className;
		}else{
			$ctr =  APP_CTR_DIR.'Error.php';
			if(file_exists($ctr)){
				include($ctr);
				return 'ErrorController';
			}else{
				wk_error("not found controller {$className} file");
			}
		}	
	}

	/**
	 *	@func 单例模式
	 *	@param stirng $className 类名 
	 *	@ret class
	 */
	public static function singleton($className){
		static $_instance = array();
		if(isset($_instance[$className])){
			return $_instance[$className];
		}
		
		$args = func_get_args();
		array_shift($args);

		$findCtr = self::find_ctr($className);

		if(!empty($args)){
			$_obj = new $findCtr(implode(',', $args));
		}else{
			$_obj = new $findCtr();
		}

		$_instance[$findCtr] = $_obj;
		return $_obj;
	}

}

?>
