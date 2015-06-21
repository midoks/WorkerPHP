<?php
/**
 * 框架异常捕捉
 * @author midoks
 * @email midoks@163.com
 */
class WorkerExceptionHandler extends Exception{

	private $trace;

	
	public function __construct($info) {
		$err = WorkerErrorHandler::getInstance();

		$err_info[] = WK_ERR_EXCEPTION;
		$err_info[] = $info;
		$err_info[] = $this->file;
		$err_info[] = $this->line;
		$err_info[] = $this->getTrace();
		$err->setError($err_info);
	}
	
}
?>
