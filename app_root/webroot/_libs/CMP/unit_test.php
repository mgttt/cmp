<?php
/**
unit test file
 */
error_reporting(E_ERROR|E_COMPILE_ERROR|E_PARSE|E_CORE_ERROR|E_USER_ERROR);
require_once __DIR__.'/bootstrap.php';

define("_APP_DIR_",__DIR__.'/../../');
define("_LIB_",__DIR__ .'/../');

\CMP\CmpCore::DefaultInit();

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

println("WRITE LOG");
$rt=quicklog_must("unit_test",$s);
println($rt);

throw new Exception('test throw ex');
#func_not_exists();



