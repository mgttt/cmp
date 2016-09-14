<?php
//@see http://cmpTech.info/
namespace CMP
{
	if(!class_exists("\\CMP\\CmpCore")){
		class CmpCore
		{
			public static $switch_conf="";
			public static function tryLoadExt($class_name){

				//class path to file path
				$class_name=str_replace('\\', '/', $class_name);
				$class_name=preg_replace("/^\//","",$class_name);//remove the leading /

				if( file_exists( "$class_name.php" ) ){
					require_once "$class_name.php";
					return true;
				}
				$ppp=(_APP_DIR_ ."/$class_name.php");
				if( file_exists( $ppp ) ){
					require_once $ppp;
					return true;
				}

				//try class_path_a
				$class_path_a=LibBase::getConf("class_path_a");
				foreach(array_reverse($class_path_a) as $class_path){
					$ccc="$class_path/$class_name.php";
					if(file_exists($ccc)){
						#LibCore::stderrln("### $ccc ###");
						require $ccc;
						return true;
					}else{
						#print("!!! $ccc !!!\n");
						#LibCore::stderrln("!!! $ccc !!!");
					}
				}

				//try _LIB_CORE_
				if(file_exists( _LIB_CORE_ ."/$class_name.php")){
					require_once(_LIB_CORE_ ."/$class_name.php");
					if(class_exists($class_name)){
						return true;
					}
				}

				if(class_exists($class_name)){
					return true;
				}
				return false;
			}
			public static function DefaultOopInit($tmp_switch_file){
				error_reporting(E_ERROR|E_COMPILE_ERROR|E_PARSE|E_CORE_ERROR|E_USER_ERROR);
				#error_reporting(0);
				#error_reporting(E_ALL);

				//历史原因，来不及改名了.以后新应用 可以复制新写DefaultOopInit2()
				if(!defined("_APP_DIR_")){ die('"404 CONFIG ERROR: NEED _APP_DIR_"'); }

				//历史原因，来不及改名了.以后新应用 可以复制新写DefaultOopInit2() 代码中尽量少用咯.
				if(!defined("_LIB_CORE_")) define("_LIB_CORE_",realpath(dirname(__FILE__)));

				#require _APP_DIR_."/config.switch.php";//switch of runtime env conf
				if(!$tmp_switch_file) $tmp_switch_file=_APP_DIR_.'/config.switch.php.tmp';
				$SAE=defined('SAE_TMP_PATH') && !$argv[0];//dirty tricks
				if($SAE){
					$_switch_conf="dev_sae";//Using SAE config on SAE Env
				}else{
					if(file_exists($tmp_switch_file)) require($tmp_switch_file);
					else{
						print "404 CONFIG ERROR: NEED tmp_switch_file($tmp_switch_file)";die;
					}
				}
				if(!$_switch_conf){
					print "404 CONFIG ERROR: NEED _switch_conf in ($tmp_switch_file)";die;
				}
				self::$switch_conf=$_switch_conf;

				ini_set("session.use_cookies",0);//Default not using Cookie
				ini_set("session.name","_s");
				if($SAE){
					//SAE mode
					if(!defined("_LOG_"))
						define("_LOG_",  'saestor://logs/');//HINT: new SAE app need to open storage and create domain=logs
					if(!defined("_TMP_"))
						define("_TMP_", "saemc://");//HINT: new SAE app need to open memcache service.

					//SAE Special (not using session.name) ...but seems not working
					if(ini_get("session.name")!='_s'){
						setcookie('PHPSESSID',"",-1,'/');//@ref http://stackoverflow.com/questions/686155/remove-a-cookie
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
				set_exception_handler(array('\CMP\DefaultErrorHandler', 'handleException'));
				self::InitGlobalFunc();

			}

			//if DefaultInit() not enough, just copy and make your own!!!!!
			public static function DefaultInit($tmp_switch_file){
				self::DefaultOopInit($tmp_switch_file);
				self::InitGlobalFunc();

				//Load Class like the old days:
				spl_autoload_register(function($class_name){
					self::tryLoadExt($class_name);
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
						'getXlsArr2'=>'',
						'global_error_handler'=>'DefaultErrorHandler::cmp_global_error_handler',
						'global_error_handler2'=>'DefaultErrorHandler::cmp_global_error_handler2',
						//'getXlsArr'='',
						#'getLang_a'=>'',
						'my_json_encode'=>'LibBase::o2s',
						'my_json_decode'=>'LibBase::s2o',
						'o2s'=>'',
						's2o'=>'',
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
			public static function tryRegisterGlobalFunc($name,$clsandmethod)
			{
				if(!$clsandmethod)$clsandmethod='LibBase::'.$name;
				eval(<<<EF
if(!function_exists('$name')){
function $name()
{
\$args=func_get_args();
return call_user_func_array("\\CMP\\$clsandmethod",\$args);
}
}
EF
			);
			}
			public static function getVersion(){
				return CmpClassLoader::getModuleMd5();
			}
		}//class CmpCore
	}//!class_exists
}//namespace
