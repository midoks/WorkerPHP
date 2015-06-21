<?php
/**
 * 框架里的错误捕捉
 * @author midoks
 * @email midoks@163.com
 */
class WorkerErrorHandler{

	private static $instance = null;

	/**
	 *	错误列表
	 */
	private $errList = array();

	
	public static function getInstance(){
		if(self::$instance == null){
			$className = __CLASS__;
			self::$instance = new $className();
		}
		
		return self::$instance;
	}

	/**
	 *	设置错误
	 *	@param array $info 
	 */
	public function setError($info){
		$c = C('app_debug');

		if($c) $this->show($info);
	}

	//只执行一此方方法
	private function one_run($func){
		static $_u_func = array();
		$md5_name = md5($func);
		if(isset($_u_func[$md5_name])){
			return false;
		}else{
			$this->$func();
			$_u_func[$md5_name] = $func;
			return true;
		}
	}

	public function page_css(){
		E('');
	}

	public function page_header(){
		E('<div style="margin:0 auto;width:900px;position:absolute;bottom:0;">');
	}

	public function page_footer(){
		E('</div>');
	}

	//页面上显示错误
	private function show($info){

		$error_log = '';
		$time = date('Y-m-d H:i:s (T)');
		
		if(is_array($info)){
			$err_race = $this->get_error_race($info[0]);
			$error_log = '['.$time.']|错误级别:'.$err_race.'|文件:'.$info[2].'|行数:'.$info['3'].'|错误信息:'.$info[1];
		}else if(is_object($info)){
		}else if(is_string($info)){
			$error_log = '['.$time.']'.'错误信息:'.$info;
		}

		E($error_log."<br />");
		error_log($error_log);
	}

	//获取错误级别
	public function get_error_race($errno){
		$err_type = array (                 
			WK_ERR_EXCEPTION		=>	'异常错误',
        	E_ERROR              	=> 	'错误',                  
        	E_WARNING            	=> 	'警告',                  
        	E_PARSE              	=> 	'解析错误',                  
        	E_NOTICE             	=> 	'注意',                  
        	E_CORE_ERROR         	=> 	'核心错误',                  
        	E_CORE_WARNING       	=> 	'核心警告',                  
        	E_COMPILE_ERROR      	=> 	'编译错误',                  
        	E_COMPILE_WARNING    	=> 	'编译警告',                  
        	E_USER_ERROR         	=> 	'用户错误',                  
        	E_USER_WARNING       	=> 	'用户报警',                  
        	E_USER_NOTICE        	=> 	'用户注意',                  
        	E_STRICT            	=> 	'运行注意',                  
			E_RECOVERABLE_ERROR 	=> 	'致命错误',
			E_DEPRECATED			=> 	'过时',
			E_USER_DEPRECATED		=>	'自定过时',
		);
		//var_dump($err_type);
		return $err_type[$errno];
	}
	
	
	
}

//
?>
