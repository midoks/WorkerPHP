<?php 

return array(
	'app_debug'			=>	true,	//开发模式

	'url_mode' 		=> 1, 		//0[普通模式], 1[pathinfo模式] 
	
	/**
	 * 基本设置
	 */
	'timezone'		=> 'PRC',	//时区


	/**
	 * 通用设置
	 *
	 */
	'common'		=> array(
		'error'			=> '404',	//默认控制器
		'html_suffix'	=> 'html', 	//默认后缀名
	),	//通用配置

	
	/**
	 *	数据库设置
	 */
	//格式如db_host=localhost&db_name=wk&db_user=wk&db_pwd=wk&db_table_prefix=wk_&db_port=3306&db_charset=utf8
	//以逗号分开
	'db'		=> '', //必须填写
);

?>
