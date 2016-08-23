<?php
#if(!defined("_APP_DIR_")) define("_APP_DIR_",realpath(dirname(__FILE__)));

require_once '../../cmp_demo/inc.app.php';

use CMP\CmpCore;
use CMP\LibBase;

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

#if(rand(0,999)>500){
	throw new Exception('test throw ex');
#}else{
#	func_not_exists();
#}


