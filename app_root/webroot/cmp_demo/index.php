<?php
//cmp的底层文件,主要做的工作是: 1 通过spl_autoload_register注册自动加载类 2 多年经验提供了一些常用的便捷函数
require_once "../cmp_demo/inc.app.php";
//CMP的核心,通过web请求去调用对应的类方法,参数是默认的入口类,入口方法以及项目的名称
$rt=cmp::handleWeb(array(
	"defaultClass"=>"WebCmpDemo",
	"defaultMethod"=>"DefaultIndex",
	//"returnFormat"=>$g_return_format,//shtml|plain|json|jsonp|xml
	"APP_NAME"=>"cmp_demo",
));

