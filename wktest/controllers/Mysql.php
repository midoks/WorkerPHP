<?php

class Mysqlcontroller extends workercontroller{

	public function __init(){
		$this->set_display_auto(false);

		trigger_error('Incorrect parameters, arrays expected', E_USER_ERROR);
	}

	

	//获取数据
	public function index(){}

	//获取数据
	public function select(){
		$mobj = M('Mysql');
		$data = $mobj->select();
		var_dump($data);
	}
	
	//插入数据
	public function insert(){
		$mobj = M('Mysql');
		$data = $mobj->insert();
		var_dump($data);
	}

	//更新数据
	public function update(){
		$mobj = M('Mysql');
		$data = $mobj->update();
		var_dump($data);
	}

	//删除数据
	public function delete(){
		$mobj = M('Mysql');
		$data = $mobj->delete();
		var_dump($data);
	}

	//删除数据
	public function replace(){
		$mobj = M('Mysql');
		$data = $mobj->replace();
		var_dump($data);
	}

	//事务测试
	public function trans(){
		$mobj = M('Mysql');
		$data = $mobj->trans();
		var_dump($data);
	}

	//事务测试2
	public function trans2(){
		$mobj = M('Mysql');
		$data = $mobj->trans2();
		var_dump($data);
	}

	//锁表测试2
	public function trans_my(){
		$mobj = M('Mysql');
		$data = $mobj->trans_my();
		var_dump($data);
	}
	
}
?>
