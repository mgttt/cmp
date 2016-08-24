<?php

# 制定上一层目录是 _APP_DIR_
#if(!defined("_APP_DIR_")) define("_APP_DIR_",realpath(dirname(__FILE__)));
#if(!defined("_APP_DIR_")) define("_APP_DIR_",realpath(__DIR__ .'/../'));

require_once "../cmp_demo/inc.app.php";
println('<pre>');

#println(_APP_DIR_);

#setConf("flag_rb_freeze",false);//初次跑可能还没有结构，设置这个flag可以让 rbWrapper/RedbeanPHP这里进入非冻结开发状态，会自动生成结构

print("CONFIG_TYPE=");
println(getConf('CONFIG_TYPE'));//看 ../cmp_demo/_conf.{$_switch_conf}/中的配置 CONFIG_TYPE

println("db_time:");
println(microtime(true)."<hr/>");

$dsn="db_app";//配置在../cmp_demo/_conf.{$_switch_conf}/

$orm=new OrmTest($dsn);

println(microtime(true)."<hr/>");

//下面测试一下 DB的时间
$db_time=$orm->getDbTimeStamp();
println($db_time);

println(microtime(true)."<hr/>");

$db_time=$orm->getDbTimeStamp();
println($db_time);

//println($orm->isoDateTime($db_time));
println($orm->isoDateTime());
println(microtime(true)."<hr/>");

$db_isoDate=$orm->isoDate();
$db_isoDateTime=$orm->isoDateTime();
println("isoDate=$db_isoDate,isoDateTime=$db_isoDateTime");

//下面测试一下 DB那里的 时区相关的变量:
print("DB TimeZone Var:");
$rsa=$orm->getAll("SHOW VARIABLES WHERE variable_name LIKE ?",array('%zone%'));
println("rsa=");
println($rsa);

//下面测试一下 PHP(WEB)时间
println("date_default_timezone_get=".date_default_timezone_get());
println("my_isoDateTime=");
println(my_isoDateTime());
adjust_timezone();
println("my_isoDateTime=");
println(my_isoDateTime());
println("date_default_timezone_get=".date_default_timezone_get());


