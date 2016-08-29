<?php
error_reporting(E_ERROR|E_COMPILE_ERROR|E_PARSE|E_CORE_ERROR|E_USER_ERROR);

#if(!defined("_APP_DIR_")) define("_APP_DIR_",realpath(dirname(__FILE__)));
#if(!defined("_LIB_")) define("_LIB_",realpath(__DIR__ ."/../_libs/"));
#if(_LIB_=="" || _LIB_=="_LIB_")throw new Exception("empty _LIB_");

#require_once _LIB_.'/CMP/bootstrap.php';
#\CMP\CmpCore::DefaultInit();

#wget https://github.com/cmptech/cmp/raw/master/app_root/webroot/_libs/CMP/bootstrap.php
require_once 'CMP_bootstrap.php';

use \CMP\LibCore;
LibCore::println( $_SERVER );

$u="https://api.safe-login-center.com/";
$s=LibCore::web($u);
LibCore::println("s=$s");
