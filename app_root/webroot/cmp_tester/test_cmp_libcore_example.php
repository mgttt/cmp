<?php
##############################################################################
/*
A Demo to show how to use CMP bootstrap(LibCore) as the mini-framework to do scripting..
 */
call_user_func(function($APP_DIR){
	error_reporting(E_ERROR|E_COMPILE_ERROR|E_PARSE|E_CORE_ERROR|E_USER_ERROR);
	$CMP_bootstrap_file='CMP_bootstrap.php';
	if(!class_exists('\CMP\LibCore')){
		if(file_exists($CMP_bootstrap_file)){
			require_once $CMP_bootstrap_file;
		}else{
			$rf='https://github.com/cmptech/cmp/raw/master/app_root/webroot/_libs/CMP/bootstrap.php';
			$s=file_get_contents($rf);
			if($s){
				file_put_contents($CMP_bootstrap_file,$s);
				if(file_exists($CMP_bootstrap_file)){
					require_once $CMP_bootstrap_file;
				}
			}
		}
	}
	if(!class_exists('\CMP\LibCore')){
		print '{"STS":"KO","errmsg":"LibCore not found"}';die;
	}
	//to load the class in the folder of current
	spl_autoload_register(function($class_name){
		if(file_exists("$APP_DIR$class_name.php")){
			require_once "$APP_DIR$class_name.php";
		}else
			if(file_exists("$class_name.php")){
				require_once "$class_name.php";
			}
	});
},array(__DIR__));
##############################################################################
use \CMP\LibCore;

LibCore::println( LibCore::o2s($_SERVER) );

$u="https://api.safe-login-center.com/";
$s=LibCore::web($u);
LibCore::println("s=$s");

$o=LibCore::s2o($s);
var_dump($o);
