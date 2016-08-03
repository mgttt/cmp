<?php
$_conf_all_[$_switch_conf]=$_conf_all_common_;//don't delete this one...

$_conf_all_[$_switch_conf]["CONFIG_TYPE"]="CMP_DEV_SAE";

#switch debug level
#$_conf_all_[$_switch_conf]["debug_a"]=array(
#	"*"=>1,//1 to open debug all for quicklog(); but quicklog_must() bypass this check
#);

$_conf_all_[$_switch_conf]["flag_rb_freeze"]=false;//在DEMO不为FREEZE表结构，这样在首次运行都能运行.

//NOTES 如果在代码中，用下面这句来打开 Orm Freeze
//setConf("flag_rb_freeze",false);//DEMO不需要freeze

$_conf_all_[$_switch_conf]["db_conf"]=array(
	//主库
	"db_app" => array(
		"db_type"=>"mysql",
		"db_host"=>"wsyddcnxkwts.rds.sae.sina.com.cn",
		"db_user"=>"cmpdemouser",
		"db_pwd"=>"demo1234",
		"db_name"=>"cmpdemo",
		"db_port"=>10906,
	),
	//从库，由于有延迟。一般用于只读、报表类查询，
	"db_app_ro" => array(
		"db_type"=>"mysql",
		"db_host"=>"mfflidbukzly.rds.sae.sina.com.cn",
		"db_user"=>"cmpdemouser",
		"db_pwd"=>"demo1234",
		"db_name"=>"cmpdemo",
		"db_port"=>10906,
	),
);

/**
数据库用 另一个SAE应用"cmpbase" 的专享mysql实例：
实例名称	地址	端口	实例内存	磁盘空间	最大IOPS	实例状态	
主 wsyddcnxkwts	wsyddcnxkwts.rds.sae.sina.com.cn	10906	0.25GB	1.93GB/20.00GB	150		
从 mfflidbukzly	mfflidbukzly.rds.sae.sina.com.cn	10906	0.25GB	1.42GB/20.00GB	150		

db=cmpdemo
user=cmpdemouser
pass=demo1234
 */
