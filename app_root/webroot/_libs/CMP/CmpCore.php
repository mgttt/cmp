<?php
namespace CMP{

class CmpCore
{
	//DefaultInit() if not enough, just copy and make your own!!!!!

	public static function DefaultInit(){
		#error_reporting(E_ERROR|E_COMPILE_ERROR|E_PARSE|E_CORE_ERROR|E_USER_ERROR);
		#error_reporting(0);
		error_reporting(E_ALL);
		register_shutdown_function(array('\CMP\DefaultErrorHandler', 'handleShutdown'));
		set_exception_handler(array('\CMP\DefaultErrorHandler', 'handleException'));
		self::InitGlobalFunc();

		//TODO
		//spl_autoload_register(function($class_name){
		//	require_once __DIR__ .'/CmpClassLoader.php';
		//	CmpClassLoader::tryloadExt($class_name);
		//});
		
		//TODO:
			/*
			if(!$path) throw new Exception("\\CMP\\CmpCore::DefaultInit() need \$path");
			//NOTES: just to make compatible for old projects...
			if(!defined("_APP_DIR_")){
				define("_APP_DIR_",$path);
			}
			 */
	}

	//For the Backward Compatibility, we need some global function
	public static function InitGlobalFunc($funclist=false){
		if(!$funclist){
			$funclist=array(
				'println'=>'LibBase::println',
				'my_json_encode'=>'',
				'my_json_decode'=>'',
			);
			//TODO if SAE, some function is little different
		}
		foreach($funclist as $func=>$clsmethod){
			self::tryRegisterGlobalFunc($func,$clsmethod);
		}
	}
	
	/**
	 * Try Register Function as Global Function.
	 * global $name() => forward to => \CMP\$cls::$name()
	 */
	public static function tryRegisterGlobalFunc($name,$clsmethod)
	{
		if(!$clsmethod)$clsmethod='LibBase::'.$name;
		eval(<<<EF
if(!function_exists('$name')){
function $name()
{
\$args=func_get_args();
return call_user_func_array("\\CMP\\$clsmethod",\$args);
}
}
EF
	);
	}
	public static function getVersion(){
		return CmpClassLoader::getModuleMd5();
	}
}

}
