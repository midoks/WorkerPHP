<?php
/**
 *	控制器测试
 */
class IndexController extends WorkerController{


	public function _init(){
		
	}

	//自动加载模版
	public function index(){
		$this->assign('midoks', 'Hello WorkerPHP!!!');
	}

	//自定义加载模版
	public function selfDefine(){
		$this->assign('midoks', 'Hello WorkerPHP!!!');
		$this->display('index/p');
	}

	//自定义加载组
	public function selfMdefine(){
		$this->assign('midoks', 'Hello WorkerPHP!!!');
		$this->display('index/index');
	}

	//不自定加载模版
	public function notDefine(){
		//设置不自动加载
		$this->set_display_auto(false);
		echo '123';
	}

	//public function _404(){
		//$this->set_display_auto(false);	
	//}

}
?>
