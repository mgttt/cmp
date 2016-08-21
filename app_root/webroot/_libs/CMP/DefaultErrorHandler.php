<?php
namespace CMP;
//default. TODO override by APP?
class DefaultErrorHandler
{
	public static function cmp_global_error_handler($file,$line,$message,$trace,$errno){
		return array("errmsg"=>$message,"errno"=>$errno,"trace"=>$trace,"line"=>$line,"file"=>$file);
	}
	public static function cmp_global_error_handler2($ex){
		return self::cmp_global_error_handler($ex->getFile(),$ex->getLine(),$ex->getMessage(),"",$ex->getCode());
		//skip getTraceAsString
	}
	public static function cmp_debug_stack($s=""){
		$rt="";
		if(!function_exists('debug_backtrace'))
		{
			$rt.= 'function debug_backtrace does not exists'."\r\n";
			return $rt;
		}
		//$rt.= "\r\n".'----------------'."\r\n";
		//$rt.= 'Debug backtrace:'."\r\n";
		//$rt.= '----------------'."\r\n";
		foreach(debug_backtrace() as $t)
		{
			$rt.= "\t" . '@ ';
			if(isset($t['file'])) $rt.= basename($t['file']) . ':' . $t['line'];
			else
			{
				// if file was not set, I assumed the functioncall
				// was from PHP compiled source (ie XML-callbacks).
				$rt.= '<PHP inner-code>';
			}

			$rt.= ' -- ';

			if(isset($t['class'])) $rt.= $t['class'] . $t['type'];

			$rt.= $t['function'];

			if(isset($t['args']) && sizeof($t['args']) > 0) $rt.= '(...)';
			else $rt.= '()';

			//$rt.= PHP_EOL;
			$rt.= '\n';
		}
		return $rt;
	}
	public static $exception_before_shutdown=false;
	public static function handleException($ex){

		self::$exception_before_shutdown=true;
		#$rt=self::cmp_global_error_handler2($ex);
		#print json_encode($rt);

		global $APP_NAME;

		$trace_s=$ex->getTraceAsString();
		$trace_s=substr($trace_s,0,4096);

		$rt=self::cmp_global_error_handler2($ex);
		//$rt['nav_helper']="<a href='javascript:history.back();'>Go Back";

		$logid=LibBase::getbarcode(8);//for easier to trace ...
		#$logfile=quicklog_must("app-$APP_NAME",$logid." ( inc.app.php exception_handler )\n".var_export($rt,true)."\n".$trace_s);
		#quicklog_must("app-$APP_NAME", $logid."\n".var_export($_REQUEST,true)."\n".var_export($_SESSION,true));
		$logfile='TMP';

		if($rt['errno']==0)unset($rt['errno']);
		unset($rt['trace']);//不给客户看到...
		unset($rt['trace_s']);//不给客户看到...
		$rt['file']=basename($rt['file'],".php");
		$rt['log_id']=$logid;
		$rt['log_file']=basename($logfile,".log");
		$s=json_encode($rt);
		print $s;
	}
	public static function handleShutdown(){
		if(self::$exception_before_shutdown){
			//skip if already handled.
			return;
		}
		LibBase::stderrln("TODO handleShutdown !!!!");die;

		$_json=true;//TODO
		$error = error_get_last();

		if ( $error !== NULL ) {
			if ( 8!=$error['type'] //ignore notice
				&& 2!=$error['type'] //ignore warning
				&& 128!=$error['type'] //ignore deprecated warning
				&& 8192!=$error['type'] //ignore deprecated warning
			){
				print json_encode($error);return;//TODO

				$trace_s=self::cmp_debug_stack();
				$trace_s=substr($trace_s,0,4096);
				$error['errmsg']=$error['message'];
				ob_get_clean();//not functioning unless error_reporting(0);

				$log_id=CoreFunc::getbarcode(8);//for easier to trace ...
				$error['log_id']=$log_id;

				$output=cmp_global_error_handler(basename($error['file'],".php"),$error['line'],$error['message'],null,$error['type']);
				$output['module']=$output['file'];//换个名字...
				unset($output['file']);
				unset($output['trace']);//不给外面看..
				//$output['errmsg']="UnexpectedFatalError";
				if($_json){
					print my_json_encode($output,true);
					ob_end_flush();
				}else{
					print_r($output);
					ob_end_flush();
				}
				quicklog_must("IT-CHECK","$log_id ".my_json_encode($error,true)."\n".$trace_s);
				quicklog_must("IT-CHECK","$log_id _SESSION=".my_json_encode($_SESSION));
				require_once _LIB_CORE_.DIRECTORY_SEPARATOR."inc.v5.secure.php";//for _get_ip_()
				$_get_ip_=_get_ip_();
				quicklog_must("IT-CHECK","$log_id _get_ip_=$_get_ip_");
				//return $output;
			}
		}else{
			$trace_s=self::cmp_debug_stack();
			//print $trace_s;
			return;//TODO do logging...

			$trace_s=substr($trace_s,0,4096);
			$log_id=CoreFunc::getbarcode(8);//for easier to trace ...
			quicklog_must("IT-CHECK","$log_id [Not Error Shutdown?]"."\n".$trace_s);//debug_stack in inc.common.func.v5.php
			quicklog_must("IT-CHECK","$log_id _SESSION=".my_json_encode($_SESSION));
			require_once _LIB_CORE_.DIRECTORY_SEPARATOR."inc.v5.secure.php";//for _get_ip_()
			$_get_ip_=_get_ip_();
			quicklog_must("IT-CHECK","$log_id _get_ip_=$_get_ip_");
		}
		#ini_set("display_error", "Off");
	}
}


