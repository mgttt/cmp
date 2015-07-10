<?php
require_once "inc.app.php";

$rt=RpcController2014c::handleWeb(array(
	"defaultClass"=>"CmpDemo",
	"defaultMethod"=>"DefaultIndex",
	"returnFormat"=>$g_return_format,//shtml|plain|json|jsonp|xml
	"APP_NAME"=>"cmp_demo",
));

