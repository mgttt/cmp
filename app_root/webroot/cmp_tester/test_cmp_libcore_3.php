<?php
error_reporting(E_ERROR|E_COMPILE_ERROR|E_PARSE|E_CORE_ERROR|E_USER_ERROR);

/**
 * 演示如何用几行代码可以开始实现 小型工具：
 * 下载CMP的boostrap.php（已经内置 LibCore和CmpClassLoader类
wget -O CMP_bootstrap.php https://github.com/cmptech/cmp/raw/master/app_root/webroot/_libs/CMP/bootstrap.php
 * 用下面的代码做个简单的联通性测试：
 */
require_once 'CMP_bootstrap.php';

use \CMP\LibCore;
LibCore::println( $_SERVER );

$u="https://api.safe-login-center.com/";
$s=LibCore::web($u);
LibCore::println("s=$s");
