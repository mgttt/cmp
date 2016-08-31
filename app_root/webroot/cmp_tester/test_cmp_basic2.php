<?php
#if(!defined("_APP_DIR_")) define("_APP_DIR_",realpath(dirname(__FILE__)));
require_once "inc.slctest.php";

println("<pre>");

use CMP\LibCore;
use CMP\CmpCore;
use CMP\LibBase;
use CMP\LibExt;

println(CmpCore::getVersion());
LibBase::println(CmpCore::getVersion());

$a=array(
	rand() => rand(),
	rand() => rand(),
);

println($s=my_json_encode($a,true));
println(my_json_decode($s),true);
println($s=LibBase::o2s($a,true));
println(LibBase::s2o($s,true));

println("WRITE LOG");
$rt=quicklog_must("unit_test",$s);
println("You review the logfile: $rt");

$tz="Asia/Phnom_Penh";

$s1="2016-06-30";
$ts1=LibCore::getTimeStamp($s1);
println("LibCore::getTimeStamp($s1)=$ts1");
$s2="2016-06-30 12:13:14";
$ts2=LibCore::getTimeStamp($s2);
println("LibCore::getTimeStamp($s2)=$ts2");
$s3="2016-06-30 12:13:14";
$ts3=LibCore::getTimeStamp($s3,$tz);
println("LibCore::getTimeStamp($s3,$tz)=$ts3");

$ts=LibCore::getTimeStamp();
println("LibCore::getTimeStamp()=".$ts);

println("isoDate()=".LibCore::isoDate());
println("isoDateTime()=".LibCore::isoDateTime());
println("isoDate($ts,$tz)=".LibCore::isoDate($ts,$tz));
println("isoDateTime($ts,$tz)=".LibCore::isoDateTime($ts,$tz));

println("LibExt::getYmdHis($ts,$tz)=".LibExt::getYmdHis($ts,$tz));

#if(rand(0,999)>500){
	throw new Exception('test throw ex');
#}else{
#	func_not_exists();
#}


