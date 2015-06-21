<?php

class Errorcontroller extends workercontroller{

	public function __init(){
		$this->set_display_auto(false);
	}

	//获取数据
	public function index(){
		echo 'Error Page!!!';
	}
	
}
?>
