<?php
//this header file is example about the //CMP/LibCore Only.

//error_reporting(E_ERROR|E_COMPILE_ERROR|E_PARSE|E_CORE_ERROR|E_USER_ERROR);

($f='CMP_bootstrap.php')&&(class_exists('\CMP\LibCore')||((file_exists($f)||file_put_contents($f,file_get_contents('https://github.com/cmptech/cmp/raw/master/app_root/webroot/_libs/CMP/bootstrap.php'))) and require_once($f)));

//to load the class in the folder of current
spl_autoload_register(function($class_name){
	if( defined("_APP_DIR_") && file_exists(_APP_DIR_."$class_name.php") ){
		require_once _APP_DIR_."$class_name.php";
	}elseif(file_exists("$class_name.php")){
		require_once "$class_name.php";
	}elseif(file_exists(basename($class_name).".php")){
		require_once basename($class_name).".php";
	}
});


use \CMP\LibCore;

LibCore::println( $_SERVER );
LibCore::println( "CMP RUNNING OK IF \$_SERVER IS DUMPPED" );

