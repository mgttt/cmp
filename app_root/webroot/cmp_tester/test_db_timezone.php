<?php
require_once "../cmp_demo/inc.app.php";
//require_once "../inc.app.php";

$APP_NAME="test";

Tenant_Tool::MergeSaasConf();

use \CMP\LibExt;
use \CMP\LibCore;
use \CMP\LibBase;

println("<pre>PHP:");

$getConf_SERVER_TIMEZONE=LibBase::getConf('SERVER_TIMEZONE');
println("LibBase::getConf(SERVER_TIMEZONE)=$getConf_SERVER_TIMEZONE");

$microtime=microtime(true);
println("microtime(true)=$microtime<hr/>");
println("date(YmdHis)=".date('YmdHis'));
$dateYmdHis=date('YmdHis',$microtime);
println("date(YmdHis,$microtime)=$dateYmdHis");

$getTimeStamp=LibCore::getTimeStamp();
println("LibCore::getTimeStamp()=$getTimeStamp");

println("LibBase::isoDateTime()=".LibBase::isoDateTime());
println("LibExt::getServerTimeZone()=".LibExt::getServerTimeZone());

println("LibBase::isoDateTime($microtime)=".LibBase::isoDateTime($microtime));
$timezone="Asia/Phnom_Penh";
println("LibBase::isoDateTime($microtime,$timezone)=".LibBase::isoDateTime($microtime,$timezone));

println("\n<hr/>DB:");

$orm=new ORM_Base("db_app");

//$rsa=$orm->getAll("SHOW VARIABLES WHERE variable_name LIKE ?",array('%zone%'));
//println("rsa=");
//println($rsa);
//println("<br/>");

$getDbTimeStamp=$orm->getDbTimeStamp();
println("getDbTimeStamp=$getDbTimeStamp");
$getDefaultDbTimeStamp=ServiceDateTime::getDefaultDbTimeStamp();
println("ServiceDateTime::getDefaultDbTimeStamp()=$getDefaultDbTimeStamp");

println("orm->isoDate()=".$orm->isoDate());
println("orm->isoDateTime()=".$orm->isoDateTime());
println("orm->isoDate($getDefaultDbTimeStamp)=".$orm->isoDate($getDefaultDbTimeStamp));
println("orm->isoDateTime($getDefaultDbTimeStamp)=".$orm->isoDateTime($getDefaultDbTimeStamp));
println("orm->isoDateTime(microtime)=".$orm->isoDateTime($microtime));

println("\n<hr/>SPECIAL:");
println("php LibBase::isoDateTime($getDefaultDbTimeStamp)=".LibBase::isoDateTime($getDefaultDbTimeStamp));

die;//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! 下面的是旧代码，保留供参考，不要用

println($orm->db_time(7,$flag_cache));
println("first=$flag_cache");

###println($orm->db_time(0.001,$flag_cache));//纯玩嘢
println($orm->db_time(0.1,$flag_cache));//纯玩嘢
println("0.1 second cache = $flag_cache");

println($orm->db_time(0.001,$flag_cache));//纯玩嘢
println("0.001 second cache = $flag_cache");

//println($orm->db_time(7,$flag_cache));
println($orm->db_time(null,$flag_cache));

println("force = $flag_cache");
println(microtime(true)."<hr/>");

//println(ORM_Base::getTime(null,$flag_cache,null));
println(ORM_Base::getTime(null,$flag_cache));
//usleep(150);println(ORM_Base::getTime(null,$flag_cache,0.001));
println(microtime(true)."<hr/>");

println('static ::getTime cache='.$flag_cache);

println("<hr/>");
println(microtime(true));
println("<hr/>");

#32bit-2038
#println("<hr/>");
#$s=date_create_from_format('Y-m-d H:i:s','3099-12-01 10:10:10')->format('U');//$db_time=strtotime($this->getCell($sql_now));//32bit-2038
#println( "s=$s");
#$s2=date_create_from_format('Y-m-d H:i:s','9999-12-01 10:10:10')->format('U');//$db_time=strtotime($this->getCell($sql_now));//32bit-2038
#println( "s2=$s2");
#println ("s3=".($s2-$s));

////$s1=my_strtotime('9999-12-12 10:10:10');
//$s1=my_strtotime('2049-12-12 10:10:10');
//println("s1=$s1");
//$s2=my_isoDate($s1);
//println("s2=$s2");
//$s3=my_isoDate();
//println("s3=$s3");
//$s4=my_isoDateTime($s1);
//println("s4=$s4");
//$s5=my_isoDateTime();
//println("s5=$s5");

$s1=my_strtotime('2099-10-10');
//$s1=my_strtotime('2099-10-10 00:00:00');
$s2=my_isoDateTime($s1);
$s3=my_isoDate($s1);
println("32bit-2038 bug test=$s1,$s2,$s3");













