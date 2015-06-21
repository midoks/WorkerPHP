<?php
class MysqlModel extends Model{
	
	/**
	 * 	mysql 模型测试
	 *	查询数据
	 */
	public function select(){
		$data = $this->query('select * from wk_test order by id limit 3');
		return $data;
	}


	//插入数据
	public function insert(){
		$aff = $this->query("INSERT INTO `wk_test` (`name`, `age`) VALUES ('234', 2)");
		var_dump($aff);
	}

	//更新数据
	public function update(){
		$aff = $this->query('update wk_test set age=4 where id=14');
		return $aff;
	}

	//删除数据
	public function delete(){
		$aff = $this->query('delete from wk_test where id=14');
		return $aff;
	}

	//替换
	public function replace(){
		$aff = $this->query("replace into `wk_test`(id,name, age) values(15, 'age', '14')");
		return $aff;
	}

	//事务测试trans [InnoDB]引擎
	public function trans(){
		try{
			$this->begin();
			$this->query("INSERT INTO `wk_test` (`name`, `age`) VALUES ('事务测试1', 2)");
			$this->query("INSERT INTO `wk_test` (`names`, `age`) VALUES ('事务测试2', 2)");
			$this->commit();
			return true;
		}catch(Exception $e){
			//echo 'trans:';
			//var_dump($e);
			$this->rollback();
			return false;
		}
	}

	//事务测试trans [MyISAM]引擎
	public function trans2(){
		try{
			$this->begin();
			$this->query("INSERT INTO `wk_test2` (`name`, `age`) VALUES ('事务测试1', 2)");	
			$this->query("INSERT INTO `wk_test2` (`names`, `age`) VALUES ('事务测试2', 2)");
			$this->commit();
			return true;
		}catch(Exception $e){
			//var_dump($e);
			$this->rollback();
			return false;
		}
	}

	//锁表测试 [MyISAM]引擎 锁表插入
	public function trans_my(){
		$this->lock('wk_test2');
		$this->query("INSERT INTO `wk_test2` (`name`, `age`) VALUES ('事务测试1', 2)");
		$this->unlock();
		return true;
	}


	
}
?>
