<?php 

/**
 *	@func 加载配置文件和读取配置
 *	@ret mixed
 */
function C($file = ''){
	static $conf = array();

	if(empty($file)){
		return $conf;
	}

    if(is_file($file)){
		$config = include($file);
		$conf = array_merge($conf, $config);
		return $conf;
	}

	if(is_string($file)){
        return isset($conf[$file]) ? $conf[$file] : '';
	}

	return $conf;
}

/**
 *	@func 事例化模型
 *	@ret obj
 */
function M($modelName = ''){
	static $obj = array();
	$md5_name = md5($modelName);
	if(isset($obj[$md5_name])){
		return $obj[$md5_name];
	}else{
		$modelName = $modelName.'Model';
		$modelObj = new $modelName();
		$obj[$md5_name] = $modelObj;
		return $modelObj;
	}
}


/**
 *	@func 加载文件
 */
function I($file){
	static $file_list = array();
	$md5_name = md5($file);
	if(isset($file_list[$md5_name])){
		return true;
	}else{
		if(file_exists($file)){
			include($file);
		}
		$file_list[$md5_name] = $file;
	}
	return $file_list;
}

/**
 *	@func 	url地址转换
 *	@param 	$page 		页面
 *	@param 	$arg		参数
 *	@ret 	string
 */
function U($page = '', $args = ''){
	$mode = C('url_mode');
	if('' == $page){
		$ctr 	= APP_CTR_NAME;
		$method = APP_METHOD_NAME;
	}else{
		$ctr_and_method = explode( '/', $view , 2);
		
		if(count($ctr_and_method) == 1){
			$ctr 	= APP_CTR_NAME;
			$method = ucfirst( $ctr_and_method[0] );
		}else{
			$ctr 	= ucfirst( $ctr_and_method[0] );
			$method = ucfirst( $ctr_and_method[1] );
		}
	}

	if(is_array($args)){
		$args = http_build_query($args);
	}else if(is_string($args)){}

	if($mode){
		$url = '/'.$ctr.'/'.$method.'.html';
		if(!empty($args)){
			$url .= '?'.$args;
		}
	}else{
		$url = $_SERVER['SCRIPT_NAME'].'?c='.$ctr.'&m='.$method;
		if(!empty($args)){
			$url .= '&'.$args;
		}
	}
	
	return $url;
};

/**
 *	@func 变量输出
 *	@param $var 变量
 */
function E($var = ''){
	if(!empty($var)){
		echo $var;
	}
}

/**
 *	@func 	加载模块
 *	@param 	类文件名字 例如 wk --> wk.class.php
 */
function import($fileName){
	$fn = WK_LIB.$fileName.'.class.php';
	if(file_exists($fn)){
		include($fn);
	}else{
		$fn = APP_LIB.$fileName.'class.php';
		if(file_exists($fn)){
			include($fn);
		}else{
			die("not found {$fileName}.class.php file");
		}
	}
}

// 自定义错误
function wk_error($errMsg){
	$err = WorkerErrorHandler::getInstance();
	$err->setError($errMsg);
}

//创建自定义错误
function wk_trigger_error($error_msg, $error_types = E_USER_ERROR){
	trigger_error($error_msg, $error_types);
}

//自定义异常
function wk_exception($errMsg){
	throw new WorkerExceptionHandler($errMsg);
}

/**
 *	@func 	页面跳转功能
 */
function wk_url($view){
	if('' == $view){
		$ctr 	= APP_CTR_NAME;
		$method = APP_METHOD_NAME;
	}else{
		$ctr_and_method = explode( '/', $view , 2);
		
		if(count($ctr_and_method) == 1){
			$ctr 	= APP_CTR_NAME;
			$method = ucfirst( $ctr_and_method[0] );
		}else{
			$ctr 	= ucfirst( $ctr_and_method[0] );
			$method = ucfirst( $ctr_and_method[1] );
		}
	}
	$url = '/'.$ctr.'/'.$method;
	return $url;
}


/**
 * @func 用户日志
 */
function wk_log($info , $filename = "ulog"){
	$time = date('Y-m-d');
	$infoTime = date('Y-m-d H:i:s');

	$line = "\r\n";
	if(PHP_OS){
		$line = "\n";
	}

	$info = '['.$infoTime.'] '.$info . $line;
	wk_write_file(APP_DIR.'debug/userlog/'.$filename.'-'.$time.'.log', $info);
}

/**
 *	@func 获取当前运行系统
 */
function wk_server_os(){
	return PHP_OS;
}




/**
 *	@func 写文件内容
 *	@param string $file 文件名
 *	@param string $coentnet 内容
 *	@ret boolean 
 */
function wk_write_file($file, $content){
	$fp = fopen($file, 'ab');
	flock($fp, LOCK_EX);
	fwrite($fp, $content);
	flock($fp, LOCK_UN);
	fclose($fp);
}


/**
 * @func 安全执行方法
 * @param string $str 字符串
 * @ret void
 */
function wk_safe_echo($str){
    ob_start();
    echo $str;
    $content = ob_get_contents();
    return $content;
}

/**
 * @func 创建目录,兼容windows和非window
 * @param string $absdir 绝对地址
 * @ret boolean
 */
function wk_mkdir_p($absdir){
	$absdir = str_replace('\\', '/', $absdir);
	$absdir = rtrim($absdir, '/');
	if(file_exists($absdir)){
		return true;
	}
	$pre_dir = dirname($absdir);
	if(!file_exists($pre_dir)){
		wk_mkdir_p($pre_dir);
	}
	return mkdir($absdir);
}

/**
 * @func 判断是否在微信浏览器下
 * @ret boolean
 */
function wk_is_weixin(){
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	if (strpos($user_agent, 'MicroMessenger') === false) {
		return false;
	}else{
		return true;
	}
}

/**
 * @func 判断是否SSL协议
 * @ret boolean
 */
function wk_is_ssl() {
    if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
        return true;
    }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
        return true;
    }
    return false;
}


/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @return mixed
 */
function wk_getclient_ip($type = 0) {
	$type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos    =   array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip     =   trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * @func XML编码
 * @param mixed $data 数据
 * @param string $encoding 数据编码
 * @param string $root 根节点名
 * @return string
 */
function wk_xml_encode($data, $encoding='utf-8', $root='wkphp') {
    $xml    = '<?xml version="1.0" encoding="' . $encoding . '"?>';
    $xml   .= '<' . $root . '>';
    $xml   .= wk_data_to_xml($data);
    $xml   .= '</' . $root . '>';
    return $xml;
}

/**
 * @func 数据XML编码
 * @param mixed $data 数据
 * @return string
 */
function wk_data_to_xml($data) {
    $xml = '';
    foreach ($data as $key => $val) {
        is_numeric($key) && $key = "item id=\"$key\"";
        $xml    .=  "<$key>";
        $xml    .=  ( is_array($val) || is_object($val)) ? wk_data_to_xml($val) : $val;
        list($key, ) = explode(' ', $key);
        $xml    .=  "</$key>";
    }
    return $xml;
}

/**
 *	@func 文件下载
 *	@param 文件下载名(绝对路径)
 *	@ret void 
 */
function wk_download($fileName){
	$is_send = 0; //判断是否发送

	$fname = basename($fileName);

	//处理中文文件名
	$encoded_filename = rawurlencode($fname);

	$ua = $_SERVER["HTTP_USER_AGENT"];
	if (preg_match("/MSIE/", $ua)) {
     	header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
    } else if (preg_match("/Firefox/", $ua)) {
    	header("Content-Disposition: attachment; filename*=\"utf8''" . $fname . '"');
    } else {
    	header('Content-Disposition: attachment; filename="'.$fname.'"');
    }

	//Apache处理
	if(function_exists('apache_get_modules')){
		$list = apache_get_modules();
		foreach($list as $k=>$v){
			if($v == 'mod_xsendfile'){
				$is_send = 1;
				header("X-Sendfile: {$fileName}");
			}
		}
	}

	//nginx处理
	if(!$is_send){
	
	
	}

	//普通处理
 	if(!$is_send){
    	header("Content-Length: ". filesize($fileName));
		readfile($fileName);
	}
}

/**
 *	@func 处理中文json时的问题
 *	@param $array
 *	@ret string
 */
function json_encode_cn($array){
	arrayRecursive($array, 'urlencode', true);
    $json = json_encode($array);
    return urldecode($json);
}

/**************************************************************
 *
 *  使用特定function对数组中所有元素做处理
 *  @param  string  &$array     要处理的字符串
 *  @param  string  $function   要执行的函数
 *  @return boolean $apply_to_keys_also     是否也应用到key上
 *  @access public
 *
 *************************************************************/
function arrayRecursive(&$array, $function, $apply_to_keys_also = false){
	static $recursive_counter = 0;
	if (++$recursive_counter > 1000) {
		die('possible deep recursion attack');
	}
	foreach ($array as $key => $value) {
		if (is_array($value)) {
			arrayRecursive($array[$key], $function, $apply_to_keys_also);
		} else {
			$array[$key] = $function($value);
		}
		if ($apply_to_keys_also && is_string($key)) {
			$new_key = $function($key);
			if ($new_key != $key) {
				$array[$new_key] = $array[$key];
				unset($array[$key]);
			}
		}
	}
	$recursive_counter--;
}

?>
