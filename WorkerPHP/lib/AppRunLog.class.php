<?php
/**
 *	@func 记录应用运行的日志。
 */
class AppRunLogPlugin{

	public function start(){
		echo 'start:123';
	}

	public function stop(){
		echo 'end:123';
	}
}
?>
