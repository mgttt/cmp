<?php
# cmp_tester/ 重用 cmp_demo/ 的配置就可以了
require_once "../cmp_demo/inc.app.php";

//使用 CMP的主控制器入口
$rt=cmp::handleWeb(array(
	"defaultClass"=>"WebCmpTester",
	"defaultMethod"=>"DefaultIndex",
	"APP_NAME"=>"cmp_tester",
));

