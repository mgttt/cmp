<?php
//基本够用，如果不够，复制一份并放在目录微调一下就可以了.

error_reporting(E_ERROR|E_COMPILE_ERROR|E_PARSE|E_CORE_ERROR|E_USER_ERROR);

if (!defined("_APP_DIR_"))
	//define("_APP_DIR_",realpath(dirname(__FILE__)));
	define("_APP_DIR_",realpath(__DIR__ ."/../../"));

if (!defined("_LIB_"))
	define("_LIB_",realpath(__DIR__ ."/../"));

if (_LIB_=="" || _LIB_=="_LIB_")
	throw new Exception("empty _LIB_");

require_once 'inc.header.sample.2014b.php';//if not enough, copy it and make your own.

//adjust_timezone();//adjust php timezone getConf(SERVER_TIMEZONE), ignore it if using db time

