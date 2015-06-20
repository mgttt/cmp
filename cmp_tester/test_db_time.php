<?php
require_once "../inc.app.php";

//加载远程拿回来的配置其中最重要应该是数据库链接和Root帐号..
#Tenant_Tool::MergeSaasConf();

#$APP_NAME="test";

setConf("flag_rb_freeze",false);//初次跑可能还没有结构

print "db_time<pre>";

#println(microtime(true)."<hr/>");

##$orm=new ORM_Base;//现在会报错，因为要求显式
$orm=new ORM_Base(ORM_Base::$DSN);

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
