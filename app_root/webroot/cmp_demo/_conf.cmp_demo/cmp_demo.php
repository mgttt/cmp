<?php
$_conf_all_[$_switch_conf]=$_conf_all_common_;//don't delete this one...

$_conf_all_[$_switch_conf]["CONFIG_TYPE"]="CMP_DEMO";

#switch debug level
#$_conf_all_[$_switch_conf]["debug_a"]=array(
#	"*"=>1,//1 to open debug all for quicklog(); but quicklog_must() bypass this check
#);

//NOTES 如果在代码中，用下面这句来打开 Orm Freeze
$_conf_all_[$_switch_conf]["flag_rb_freeze"]=false;//在DEMO不为FREEZE表结构，这样在首次运行都能运行.

$_conf_all_[$_switch_conf]["db_conf"]=array(

	"db_app" => array(
		"db_type"=>"mysql",
		"db_host"=>"rds3rj34m4vo902q7zzvo.mysql.rds.aliyuncs.com",
		"db_user"=>"cmpdemo",
		"db_pwd"=>"CmpDemo8888",//公用的DB for cmpdemo
		"db_name"=>"cmpdemo",
		"db_port"=>3306,
	),

	"db_local" => array(
		"db_type"=>"mysql",
		"db_host"=>"127.0.0.1",
		"db_user"=>"root",
		"db_pwd"=>"",
		"db_name"=>"cmp_demo",//create a db name this in you local XAMPP
		"db_port"=>3306,
	),

);

