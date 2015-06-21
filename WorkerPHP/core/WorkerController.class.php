<?php
/**
 *	@func 基础控制器
 *	@author midoks
 *	@email	midoks@163.com
 */
class WorkerController{
	
	private $template_var = array();//模板变量
	private $display_auto = true;	//自动加载模版, 默认为true
	
	//构造函数
	public function __construct(){
		if(method_exists($this, '__init')){
			$this->__init();
		}
	}

	/**
	 *  分配的变量名
	 *  @param string $name 变量名
	 *  @param mixed $value 变量值
	 */
	protected function assign($name, $value){
        if(is_array($name)){
            $this->template_var = array_merge($this->template_var, $name);
        }else{
            $this->template_var[$name] = $value;
        }
	}
	
	/**
	 * 	@func 设置是否自动调用视图
	 * 	@param $bool 
	 * 	@ret void
	 */
	public function set_display_auto($bool){
		$this->display_auto = $bool;
	}
	
	/**
	 * 调用视图
	 * @param string $view 视图的名字    [index] 或  [index/index]
	 */
	//分配变量
	public function display($view = ''){
		static $one = true;
		if($one){
			if($this->display_auto){
				extract($this->template_var, EXTR_OVERWRITE);
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
				$tpl = APP_DIR.'views/'.$ctr.'/'.$method.'.view.php';
				if(file_exists($tpl)){
					include($tpl);
				}else{
					die("not found {$tpl}");
				}
			}
			$one = false;
		}
	}

	//404页面
	public function _404(){
		$this->set_display_auto(false);
		die('404 not found '.APP_METHOD_NAME.' method!!!');
	}

	/**
	 * @func 模版加载
	 * @param $template 模版名
	 * @ret
	 */
	public function i($view){
		if('' == $view){
			//自动加载模版
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
		
		$tpl = APP_DIR.'views/'.$ctr.'/'.$method.'.view.php';
		if(file_exists($tpl)){
			include($tpl);
		}else{
			die("not found {$tpl}");
		}
	}


}
?>
