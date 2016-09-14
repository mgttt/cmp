<?php
require_once "../cmp_demo/inc.app.php";
//一个项目的入口文件,分别定义默认的入口类,入口方法以及项目的名称
$rt=cmp::handleWeb(array(
	"defaultClass"=>"WebCmpDemo",
	"defaultMethod"=>"DefaultIndex",
	//"returnFormat"=>$g_return_format,//shtml|plain|json|jsonp|xml
	"APP_NAME"=>"cmp_demo",
));

