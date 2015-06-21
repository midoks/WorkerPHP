<?php
/**
 *	@func 基本管理模型
 */
class Model{

	
	public $linkID 		= null;
	private $config 	= array();
	private $transTimes	= 0;
	
	/**
	 *	@func 构造函数
	 */
	public function __construct(){
		$conf = C('db');
		parse_str($conf, $config);
		$this->config = $config;

		$this->connect();
	}

	/**
	 *	@func 链接数据库
	 */
	private function connect(){
		$c = $this->config;
		$link = mysql_connect($c['db_host'].':'.$c['db_port'], 
			$c['db_user'], $c['db_pwd']) or die('database connect error!!!');
		mysql_select_db($c['db_name'], $link);
		mysql_query('set names '.$c['db_charset'], $link);
		
		$this->linkID = $link;
	}


	/**
	 * 事务相关的函数
	 * 默认的MyISAM 不支持事务
	 * InnoDB 支持事务
	 */

	//开始一个事务
	public function begin(){
		if(!$this->linkID) return false;

		//数据rollback支持
		if($this->transTimes == 0){
			mysql_query('BEGIN', $this->linkID);//mysql_query('START TRANSACTION', $this->linkID);
		}
		$this->transTimes++;
		return '';
	}

	//事务回滚
	public function rollback(){
		if($this->transTimes > 0){
			$result = mysql_query('ROLLBACK', $this->linkID);
			$this->transTimes = 0;
			if(!$result){
				return false;
			}
		}
		return true;
	}

	//事务确认
	public function commit(){
		if($this->transTimes > 0){
			$result = mysql_query('COMMIT', $this->linkID);
			$this->transTimes = 0;
			if(!$result){
				return false;
			}
		}
		return true;
	}

	/**
	 *	对不支持事务的MyISAM引擎数据库使用锁表
	 */

	/**
	 * 	@func 	锁表
	 *	@param 	$table	表名
	 *	@param	$rw 	[READ|WRITE]
	 *	@ret void
	 */
	public function lock($table, $rw = 'WRITE'){
		mysql_query("LOCK TABLES `{$table}` {$rw}", $this->linkID);
	}

	/**
	 * @func 	解锁
	 * @param	$table 表名
	 * @ret void
	 */
	public function unlock(){
		mysql_query('UNLOCK TABLES', $this->linkID);
	}


	//其他操作
	
	/**
	 * 加入表前缀
	 * @param string $table 表名
	 * @param string $prefix 表前缀
	 * @ret stirng
	 */
	public function table($table, $prefix=''){
		if(!empty($prefix)){
			return $prefix.$table;
		}else{
			return $this->config['db_table_prefix'].$table;
		}
	}

	/**
	 *	@func 查询
	 */
	public function query($sql, $mode = 2){
		$trim_sql = trim($sql);
		//查询数据
		if(preg_match( '/^\s*(select|show|describe|desc)/i', $trim_sql)){
			return $this->get_result($trim_sql, $mode);
		}else if(preg_match('/^\s*(insert|update|delete|replace)/i', $trim_sql)){
			return $this->exec($trim_sql);
		}
		return false;
	}

	/**
	 *	@func	获取结果集
	 *	@param 	$sql  sql语句
	 *	@param 	$mode 获取数据的方式
	 *	@return array
	 */
	public function get_result($sql, $mode = 2){
		$mode_ref = array(
			0 => 'mysql_fetch_array',
			1 => 'mysql_fetch_object',
			2 => 'mysql_fetch_assoc',
		);
		$res = mysql_query($sql, $this->linkID);
		$get_result_func = $mode_ref[$mode];
		$rows = array();
		while($row = $get_result_func($res)){
			$rows[] = $row;
		}
		return $rows;
	}

	/**
	 *	@func 对数据有变动的操作
	 */
	private function exec($sql){
		//添加操作
		$ret = mysql_query($sql, $this->linkID);

		if(!$ret){
			wk_exception(mysql_error());
		}

		if(preg_match('/^\s*(insert)/i', $sql)){
			$last_id = $this->insert_last_id();
			return $last_id;
		}else if(preg_match('/^\s*(update|delete|replace)/i', $sql)){
			return $this->affected_rows();
		}

		return false;
	}

	//最后插入的ID
	public function insert_last_id(){
		 return mysql_insert_id($this->linkID);
	}

	//影响的行数
	public function affected_rows(){
		return mysql_affected_rows($this->linkID);
	}

	//字符串转义
	public function quote($string){
		if($this->linkID) {
            return mysql_real_escape_string($str,$this->linkID);
        }else{
            return mysql_escape_string($str);
        }
	}

	/**
	 *	@func 析构函数
	 */
	public function __destruct(){
		if($this->linkID){
			mysql_close($this->linkID);
		}
		$this->linkID = null;
	}


}
?>
