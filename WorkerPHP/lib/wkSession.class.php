<?php
/**
 *	@func 记录用户的状态
 
-- 保存session数据(可统计在线人数)

create table if not exists `cc_session`(
	`sid` varchar(255) not null comment 'sessionID',
	`update_time` int(11) not null comment 'session更新时间',
	`client_ip`	char(20) comment '客服端ip',
	`user_agent` char(255) comment '客服端代理信息',
	`data`	blob comment 'session数据',
	unique key `cc_session` (`sid`)
)engine=MyISAM default character set utf8 comment='保存session数据' collate utf8_general_ci;

	@author midoks
	@midoks blog
 */
class wkSession{
	
	public static $lifetime;			//session 有效时间
	public static $handler;				//数据库实例
	
	public static $ip;
	public static $ua;
	public static $time;	

	//session保存
	public static function start(){

		global $ccdb;
		self::$handler = $ccdb;
		self::$lifetime = ini_get('session.gc_maxlifetime');
		self::$lifetime = 30;
		self::$time = time();
		self::$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		self::$ip = !empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] :
						(!empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] :
						(!empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown'));
		
		//运行设置
		self::execute();
	}

	/**
	 *	@func 打开session
	 *	@param string $savePath
	 *	@param mixed $sessName
	 */
	public static function open($savePath, $sessName){
		return true;
	}

	/**
	 *	@func 关闭session
	 */
	public static function close(){
		return true;
	}

	/**
	 *	@func 读取数据
	 *	@param string $sessID sessionID
	 *	@param string 返回数据
	 */
	public static function read($sid){
		$time = self::$time;
		$lifetime = self::$lifetime;
		$ip = self::$ip;
		$user_agent = self::$ua;

		$sql = "SELECT * FROM cc_session WHERE sid='{$sid}'";
		$result = self::$handler->getOne($sql);

		if(!$result){
			return false;
		}

		/* 如果用户更换了浏览器,或更改了ip, 则清除当前的session,重新设置 */
		if($ip != $result['client_ip'] || $user_agent != $result['user_agent']){
			self::destroy($sid);
			return '';
		}

		/* 如果用户长时间没操作，Session已经过期，同样清除当前的Session, 重新设置 */
		if($result['update_time'] + $lifetime < $time){
			self::destroy($sid);
			return '';
		}

		/* 返回从数据库获取的当前session数据（序列化的字符串）并写入$_SESSION变量 */
		return $result['data']; 
	}

	/**
	 *	@func 写入数据
	 *	@param string $sid
	 *	@param string $data
	 */
	public static function write($sid, $data){
		$update_time = self::$time;
		$lifetime = self::$lifetime;
		$client_ip = self::$ip;
		$user_agent = self::$ua;

		$sql = "SELECT `update_time`,`data` FROM `cc_session` where sid='{$sid}'";
		if($result = self::$handler->getOne($sql)){
			/* 如果session数据没有改变, 或s在30s外改变则更新 */
			if($result['data'] != $data || $update_time > $result['update_time'] + $lifetime){
				$sql = "UPDATE `cc_session` SET `update_time` = '{$update_time}', `data` ='{$data}' WHERE `sid` = '{$sid}'";
				self::$handler->query($sql);
			}
			return true;
		}else{
			/* 如过用户没有设置session,即空session则插入记录 */
			if(!empty($data)){
				$sql = "INSERT INTO `cc_session`(`sid`, `update_time`, `client_ip`, `user_agent`, `data`) VALUES('{$sid}', '{$update_time}','{$client_ip}', '{$user_agent}', '{$data}')";
				self::$handler->query($sql);
			}
			return true;
			
		}
		return true;
	}

	/**
	 *	@func 删除session
	 *	@param string $sid
	 */
	public static function destroy($sid){
		$sql = "DELETE FROM `cc_session` WHERE sid='{$sid}'";
		$data = self::$handler->query($sql);
       	return true;
	}

	/**
	 *	@func 垃圾回收 | 删除超时的最大SESSION的时间
	 *	@param string $sessMaxLifeTime
	 */
	public static function gc(){
		$time = self::time - self::$lifetime;
		$sql = "DELETE FROM `cc_session` WHERE update_time<'{$time}'";
		self::$handler->query($sql);
    	return true;
	}

	/**
	 *	@func 打开运行 session
	 */
	public static function execute(){
		session_set_save_handler(
						array(__CLASS__, 'open'),
                        array(__CLASS__, 'close'),
                        array(__CLASS__, 'read'),
                        array(__CLASS__, 'write'),
                        array(__CLASS__, 'destroy'),
						array(__CLASS__, 'gc'));	
	}

	//统计在线人数
	public static function all(){
		$sql = "SELECT count(sid) as num FROM `cc_session`";
		$row = self::$handler->getOne($sql);
		return $row['num'];
	}

}
?>
