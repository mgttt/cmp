<?php
require_once "../cmp_demo/inc.app.php";

$SAE=defined('SAE_TMP_PATH') && !$argv[0];//dirty tricks

print "SAE=";
println($SAE);
print '<hr/>';
print phpinfo();
