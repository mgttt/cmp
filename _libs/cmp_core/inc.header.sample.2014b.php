<?php
/* vim: set tabstop=2 shiftwidth=2 softtabstop=2: */
/*
 *   注意：这是个example，可用，如果有特别修改，请不要直接修改，请复制到自己的目录然后再做修改 （提醒：要定义好 _LIB_CORE_）
 */
error_reporting(E_ERROR|E_COMPILE_ERROR|E_PARSE|E_CORE_ERROR|E_USER_ERROR);

if(!defined("_APP_DIR_")) die('"404 _APP_DIR_"');

if(!defined("_LIB_CORE_")) define("_LIB_CORE_",realpath(dirname(__FILE__)));

require _APP_DIR_."/config.switch.php";//switch of runtime env conf

//NOTES: $SAE 在 config.switch.php 里面设定的.
if(!$SAE){
	//local mode
	require_once 'inc.local.header.php';
}else{
	//SAE mode
	define("_LOG_",  'saestor://logs/');//提醒：新建SAE应用要打开 storage应用并新建 domain=logs
	define("_TMP_", "saemc://");//提醒：新建 SAE应用要打开 memcache 服务...
	require_once 'inc.header.sae.php';
}

require_once "inc.v5.mymagicload.php";//class loader adjustment ** important class

require_once "inc.v5.globalerror.example.php";//虽然是example但好好用...

require_once "inc.header.myglobalerror.php";//global error handling

require_once "inc.commonfunc.v5.php";//very common funcs for quick and simple coding

require_once 'inc.v5.func.config.php';//quick func of config...

if(!$SAE){//non SAE mode
	require_once 'inc.local.func.quicklog.php';//quick log...
}else{//SAE mode
	require_once 'inc.v5.func.quicklog.sae.php';//quick log...
}

require_once "inc.v5.lang.php";//quick func for lang handling

require_once "inc.v5.session.php";//quick func for session handling

require_once "inc.v5.quick.php";//more quick func (arr2var,arr2var_all,var2arr,arr2arr)

require_once "inc.v5.sqlsafe.php";//qstr() and qstr2();

