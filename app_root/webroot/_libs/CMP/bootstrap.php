<?php
/**
http://cmptech.info/
Usage
require_once 'CMP/bootstrap.php';
\CMP\CmpCore::DefaultInit();
#var_dump(\CMP\Tester::getVersion());
 */
#NOTES: namespace need >=php5.3
namespace CMP
{

	if( !function_exists('spl_autoload_register') ){
		throw new Exception("\\CMP needs spl_autoload_register()");
	}
	spl_autoload_register(function($class_name){
		require_once __DIR__ .'/CmpClassLoader.php';
		CmpClassLoader::tryload($class_name);
	});

}
