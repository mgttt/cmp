<?php
namespace CMP
{

	class CmpCore
	{
		//纯 OOP 式初始化，注意此时没有用到全局函数了！！
		public static function DefaultOopInit(){
			error_reporting(E_ERROR|E_COMPILE_ERROR|E_PARSE|E_CORE_ERROR|E_USER_ERROR);
			#error_reporting(0);
			#error_reporting(E_ALL);

			if(!defined("_APP_DIR_")){
				die('"404 _APP_DIR_"');
			}
			if(!defined("_LIB_CORE_")) define("_LIB_CORE_",realpath(dirname(__FILE__)));

			require _APP_DIR_."/config.switch.php";//switch of runtime env conf

			ini_set("session.use_cookies",0);//Default not using Cookie
			ini_set("session.name","_s");
			if($SAE){
				//SAE mode
				if(!defined("_LOG_"))
					define("_LOG_",  'saestor://logs/');//提醒：新建SAE应用要打开 storage应用并新建 domain=logs
				if(!defined("_TMP_"))
					define("_TMP_", "saemc://");//提醒：新建 SAE应用要打开 memcache 服务...

				//SAE Special...but seems not working at all
				if(ini_get("session.name")!='_s'){
					setcookie('PHPSESSID',"",-1,'/');//http://stackoverflow.com/questions/686155/remove-a-cookie
					unset($_COOKIE['PHPSESSID']);
				}
			}else{
				//local mode
				//LOG
				if(!defined("_LOG_"))
					define("_LOG_", _APP_DIR_ .'/_logs/');
				if(!is_dir(_LOG_)){
					mkdir(_LOG_,0777,true);
					if(!is_dir(_LOG_)){
						throw new Exception("_LOG_ FAIL");
					}	
				}
				//TMP && SESSION DEFAULT
				if(!defined("_TMP_")){
					define("_TMP_", _APP_DIR_ .'/_tmp/');
				}
				if(!is_dir(_TMP_)){
					mkdir(_TMP_,0777,true);
					if(!is_dir(_TMP_)){
						throw new Exception("_TMP_ FAIL");
					}
				}
				if(!is_dir(_TMP_.'/session/')){
					mkdir(_TMP_.'/session/',0777,true);
					if(!is_dir(_TMP_.'/session/')){
						throw new Exception("_TMP_/session/ FAIL");
					}
				}
			}
			//SAFE CHECK
			//if(!defined("_LOG_"))
			//	throw new Exception("_LOG_ is not config");
			//if(!defined("_TMP_"))
			//	throw new Exception("_TMP_ is not config");
			//LIB
			if(!defined("_LIB_")){
				define("_LIB_", realpath(_APP_DIR_ .'/_libs/'));
			}
			if(!is_dir(_LIB_)){
				throw new Exception("404 _LIB_");
			}

			register_shutdown_function(array('\CMP\DefaultErrorHandler', 'handleShutdown'));
			//register_shutdown_function(function(){
			//	var_dump(debug_backtrace());
			//	$rt="";
			//	if(!function_exists('debug_backtrace'))
			//	{
			//		$rt.= 'function debug_backtrace does not exists'."\r\n";
			//		return $rt;
			//	}
			//	//$rt.= "\r\n".'----------------'."\r\n";
			//	//$rt.= 'Debug backtrace:'."\r\n";
			//	//$rt.= '----------------'."\r\n";
			//	$c=0;
			//	foreach(debug_backtrace() as $t)
			//	{
			//		if($c<2){
			//			//skip the row
			//			$c++;
			//			//continue;
			//		}
			//		$rt.= "\t" . '@ ';
			//		if(isset($t['file'])) $rt.= basename($t['file']) . ':' . $t['line'];
			//		else
			//		{
			//			// if file was not set, I assumed the functioncall
			//			// was from PHP compiled source (ie XML-callbacks).
			//			$rt.= '<PHP inner-code>';
			//		}

			//		$rt.= ' -- ';

			//		if(isset($t['class'])) $rt.= $t['class'] . $t['type'];

			//		$rt.= $t['function'];

			//		if(isset($t['args']) && sizeof($t['args']) > 0) $rt.= '(...)';
			//		else $rt.= '()';

			//		//$rt.= PHP_EOL;
			//		$rt.= "\n";
			//	}
			//	return $rt;
			//});
			set_exception_handler(array('\CMP\DefaultErrorHandler', 'handleException'));
			self::InitGlobalFunc();

		}

		//DefaultInit() if not enough, just copy and make your own!!!!!
		public static function DefaultInit(){
			self::DefaultOopInit();
			self::InitGlobalFunc();

			//Load Class like the old days:
			spl_autoload_register(function($class_name){
				CmpClassLoader::tryLoadExt($class_name);
			});
		}

		//For the Backward Compatibility, we need some global function
		public static function InitGlobalFunc($funclist=false){
			//if($funclist){
			//	//TODO override the default...
			//}
			if(!$funclist){
				$funclist=array(
					'println'=>'',
					'getConf'=>'',
					'setConf'=>'',
					'logger'=>'',
					'quicklog'=>'',
					'quicklog_must'=>'',
					'_getbarcode'=>'LibBase::getbarcode',
					'calcLangFromBrowser'=>'',
					'getLang'=>'',
					#'getLang_a'=>'',
					'my_json_encode'=>'LibBase::o2s',
					'my_json_decode'=>'LibBase::s2o',
					'_gzip_output'=>'LibBase::gzip_output',//For the Old Controller...
					'adjust_timezone'=>'LibExt::adjust_timezone',//
					'debug_stack'=>'DefaultErrorHandler::cmp_debug_stack',
					'my_strtotime'=>'LibExt::getTimeStamp',//ACE still need it.. going to alternate...TODO
					'my_YmdHis'=>'LibExt::getYmdHis',//titto
					'my_isoDate'=>'LibExt::isoDate',//titto
					'my_isoDateTime'=>'LibExt::isoDateTime',//titto
					'getSessionVar'=>'LibExt::getSessionVar',//from old projects.
				);
			}
			foreach($funclist as $func=>$clsmethod){
				self::tryRegisterGlobalFunc($func,$clsmethod);
			}
		}
		/** Try Register Function as Global Function.
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
