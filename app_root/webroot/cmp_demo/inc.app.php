<?php
//报告以下错误.
error_reporting(E_ERROR|E_COMPILE_ERROR|E_PARSE|E_CORE_ERROR|E_USER_ERROR);

//定义常量，用于CmpCore
if(!defined("_APP_DIR_")) define("_APP_DIR_",realpath(dirname(__FILE__)));

#微调目录结构...
if(!defined("_LIB_")) define("_LIB_",realpath(__DIR__ ."/../_libs/"));
if(_LIB_=="" || _LIB_=="_LIB_")throw new Exception("empty _LIB_");

//CMP's bootstrap (the core level under CMP):
require_once _LIB_.'/CMP/bootstrap.php';

\CMP\CmpCore::DefaultInit(_APP_DIR_.'/config.switch.override.tmp');

#如果注释，用的是 WEB的PHP的默认时间，如果使用，用的是 配置目录里面的时区，测试用 test/test_timezone.php
adjust_timezone();//现在用的数据库的，所以是否adjust_timezone问题不大。不过有些 Web Server所以的可能未弄好，所以弄一下可以调整好php的时区.
