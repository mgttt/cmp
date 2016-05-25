<?php

if(!defined("_LOG_"))
throw new Exception("_LOG_ is not config");

if(!defined("_TMP_"))
throw new Exception("_TMP_ is not config");
if(!defined("_LIB_"))
	define("_LIB_", realpath(_APP_DIR_ .'/_libs/'));

if(!is_dir(_LIB_)){
	throw new Exception("_LIB_ is not config, please run install.php");
}

ini_set("session.use_cookies",0);//Default not using Cookie
ini_set("session.name","_s");

//SAE Special...but seems not working at all
if(ini_get("session.name")!='_s'){
	setcookie('PHPSESSID',"",-1,'/');//http://stackoverflow.com/questions/686155/remove-a-cookie
	unset($_COOKIE['PHPSESSID']);
}

//session_set_cookie_params(2 * 3600);//cookie time 2hr
////ini_set('session.cookie_domain', '.openfares.com');//well...
########################################################################ini_set("session.name","_s");//dirty-work solution for phprpc

#memcache is not good yet for app!!!!!!!!!!!!!!!!!!!!!!
//ini_set("session.save_handler", "memcache");
//ini_set("session.save_path", "tcp://127.0.0.1:11211");
//ini_set("session.save_path","tcp://server:port?persistent=1&amp;weight=1&amp;timeout=1&amp;retry_interval=15");

//TODO in Config
//ini_set("session.gc_maxlifetime","7200");
//ini_set("session.cookie_lifetime","7200");

//file handler
//session_save_path(_TMP_ ."/session/");

