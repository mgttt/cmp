<?php
/**
unit test file
 */
require_once __DIR__.'/bootstrap.php';
\CMP\CmpCore::DefaultInit();#TODO more init param...

use CMP\CmpCore;
println(CmpCore::getVersion());

$a=array(
	rand() => rand(),
	rand() => rand(),
);
println($s=my_json_encode($a,true));
println(my_json_decode($s),true);

throw new Exception('test throw ex');
#func_not_exists();



