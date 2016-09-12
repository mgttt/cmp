<?php
#if(!defined("_APP_DIR_")) define("_APP_DIR_",realpath(dirname(__FILE__)));

require_once '../cmp_demo/inc.app.php';

println("<pre>");

use CMP\CmpCore;
use CMP\LibBase;

println(CmpCore::getVersion());
LibBase::println(CmpCore::getVersion());

$a=array(
	rand() => rand(),
	rand() => rand(),
);

//my_json_encode/my_json_decode 是旧版本用的，新版本已经使用 o2s/s2o
println($s=my_json_encode($a,true));
println(my_json_decode($s),true);

println($s=LibBase::o2s($a,true));
println(LibBase::s2o($s,true));

//quicklog_must() 是强制写LOG。另外还有quicklog()是根据配置写LOG
//这里的LOG是本地配置在 _TMP_/下面的基于文件IO的LOG，对一般应用来说已经足够使用。
//特别注意写LOG是非常重要的动作，写好LOG就不需要设置php断点。设置断点式的ｄｅｂｕｇ方法在ｐｈｐ世界观里面是被淘汰的。
println("WRITE LOG");
$rt=quicklog_must("unit_test",$s);
println("You review the logfile: $rt");

#if(rand(0,999)>500){
throw new Exception('test throw ex');
#}else{
#	func_not_exists();
#}


