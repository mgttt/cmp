<?php
require_once "inc.app.php";

$rt=cmp::handleWeb(array(
	"defaultClass"=>"WebCmpDemo",
	"defaultMethod"=>"DefaultIndex",
	"returnFormat"=>$g_return_format,//shtml|plain|json|jsonp|xml
	"APP_NAME"=>"cmp_demo",
));

