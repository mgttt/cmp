<?php

if(!defined("_APP_DIR_"))
	throw new Exception("_APP_DIR_ is not exists ");

if(!defined("_LOG_"))
	define(_LOG_, _APP_DIR_ .'/_logs/');
if(!defined("_TMP_"))
	define(_TMP_, _APP_DIR_ .'/_tmp/');
if(!defined("_LIB_"))
	define(_LIB_, realpath(_APP_DIR_ .'/_libs/'));

if(!is_dir(_LOG_)){
	mkdir(_LOG_,0777,true);
	//throw new Exception("_LOG_ is not exists ");
}
if(!is_dir(_TMP_)){
	mkdir(_TMP_,0777,true);
	//throw new Exception("_TMP_ is not exists");
}
if(!is_dir(_TMP_.'/session/')){
	mkdir(_TMP_.'/session/',0777,true);
	//throw new Exception("_TMP_/session/ is not exists");
}

if(!is_dir(_LIB_)){
	throw new Exception("_LIB_ is not config");
}

//TODO
//more other php setting for session for this app
ini_set("session.use_cookies",0);//no cookie for session...（因为实战中太多应用如果用cookie来做session会被过度缓存出现问题。所以需要显式session id）
//session_set_cookie_params(2 * 3600);//cookie time 2hr
////ini_set('session.cookie_domain', '.openfares.com');//well...
//ini_set("session.name","_s");//dirty-work solution for phprpc....failed...������Ի�ûͨ��������û�취������...
###PHPSESSID

ini_set("session.name","_s");

#memcache is not good yet for app!!!!!!!!!!!!!!!!!!!!!!
//ini_set("session.save_handler", "memcache");
//ini_set("session.save_path", "tcp://127.0.0.1:11211");
//ini_set("session.save_path","tcp://server:port?persistent=1&amp;weight=1&amp;timeout=1&amp;retry_interval=15");

//TODO in Config
//ini_set("session.gc_maxlifetime","7200");
//ini_set("session.cookie_lifetime","7200");

//file handler
session_save_path(_TMP_ ."/session/");

