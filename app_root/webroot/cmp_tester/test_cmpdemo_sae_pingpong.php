<?php
require_once "../cmp_demo/inc.app.php";

#先用经典方法、之前再用新的 LibBase::WebRequest() 方法
require_once _LIB_CORE_.'/inc.func.web_request.php';

$c="";//PinPong方法应该默认的_c类应该要有.
$m='PingPong';
$api_entry='https://cmpdemo.applinzi.com/cmp_demo/';//远程。
//$api_entry='http://localhost/cmpdemo_sae/cmp_demo/';//如果用本地做测试就自己改一下
$u="$api_entry/$m.api";
$ping=time();//TODO using lib class later
$tm1=microtime(true);
$s=web_request($u,array(
'ping'=>$ping,
));
println("api return=".$s);
