<?php
//this header file is example about the //CMP/LibCore Only.

error_reporting(E_ERROR|E_COMPILE_ERROR|E_PARSE|E_CORE_ERROR|E_USER_ERROR);

begin:
	$CMP_bootstrap_file='CMP_bootstrap.php';
if(!class_exists('\CMP\LibCore')){
	if(file_exists($CMP_bootstrap_file)){
		require_once $CMP_bootstrap_file;
	}else{
		$rf='https://github.com/cmptech/cmp/raw/master/app_root/webroot/_libs/CMP/bootstrap.php';
		echo 'Downloading ...'.$rf."\n";
		$s=file_get_contents($rf);
		if($s){
			echo 'Save to '.$CMP_bootstrap_file."\n";
			file_put_contents($CMP_bootstrap_file,$s);
			sleep(1);
			goto begin;
		}
	}
}

if(!class_exists('\CMP\LibCore')){
	throw new Exception('\CMP\LibCore class not found');
}

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

