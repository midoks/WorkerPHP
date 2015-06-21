<?php
class PdoController extends WorkerController{

	public function __construct(){
		$this->set_display_auto(false);
	}
	
	/**
	 *	PDO 模型测试
	 */
	public function index(){}

	//获取数据
	public function select(){
		$mobj = M('Pdotest');
		$data = $mobj->select();
		var_dump($data);
	}
	
	//插入数据
	public function insert(){
		$mobj = M('Pdotest');
		$data = $mobj->insert();
		var_dump($data);
	}

	//更新数据
	public function update(){
		$mobj = M('Pdotest');
		$data = $mobj->update();
		var_dump($data);
	}

	//删除数据
	public function delete(){
		$mobj = M('Pdotest');
		$data = $mobj->delete();
		var_dump($data);
	}

	//删除数据
	public function replace(){
		$mobj = M('Pdotest');
		$data = $mobj->replace();
		var_dump($data);
	}

	//事务测试
	public function trans(){
		$mobj = M('Pdotest');
		$data = $mobj->trans();
		var_dump($data);
	}

	//事务测试2
	public function trans2(){
		$mobj = M('Pdotest');
		$data = $mobj->trans2();
		var_dump($data);
	}

	//锁表测试2
	public function trans_my(){
		$mobj = M('Pdotest');
		$data = $mobj->trans_my();
		var_dump($data);
	}
	
}
?>
