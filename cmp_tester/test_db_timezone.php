<?php
require_once "../inc.app.php";

$APP_NAME="test";

//加载远程拿回来的配置其中最重要应该是数据库链接和Root帐号..
Tenant_Tool::MergeSaasConf();

print "db_time<pre>";

println(microtime(true)."<hr/>");

$orm=new ORM_Base("db_app");

$rsa=$orm->getAll("SHOW VARIABLES WHERE variable_name LIKE ?",array('%zone%'));
println("rsa=");
println($rsa);
println("<br/>");

println($orm->isoDateTime());
die;

