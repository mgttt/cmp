<?php
require_once "../cmp_demo/inc.app.php";

setConf("flag_rb_freeze",false);//初次跑可能还没有结构

print "db_time<pre>";

println(microtime(true)."<hr/>");

$dsn="db_app";//配置在../cmp_demo/_conf.{$_switch_conf}/
$orm=new OrmTest($dsn);

println(microtime(true)."<hr/>");
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

