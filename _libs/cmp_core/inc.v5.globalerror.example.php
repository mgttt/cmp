<?php
//------------------------------------------------------------------------------------------------
function global_error_handler($file,$line,$message,$trace,$errno){
	return array("errmsg"=>$message,"errno"=>$errno,"trace"=>$trace,"line"=>$line,"file"=>$file);
}
function global_error_handler2($ex){
	return global_error_handler($ex->getFile(),$ex->getLine(),$ex->getMessage(),"",$ex->getCode());
	//skip getTraceAsString
}
//处理未期待的错误...
function _shutdown_function($_json=true){
	$error = error_get_last();
	if($error !== NULL){
		if(8!=$error['type'] //ignore notice
			&& 2!=$error['type'] //ignore warning
			&& 128!=$error['type'] //ignore deprecated warning
			&& 8192!=$error['type'] //ignore deprecated warning
		)
		{
			$error['errmsg']=$error['message'];
			ob_get_clean();//not functioning unless error_reporting(0);

			$log_id=_getbarcode();//for easier to trace ...
			$error['log_id']=$log_id;

			$output=global_error_handler(basename($error['file'],".php"),$error['line'],$error['message'],null,$error['type']);
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
			quicklog_must("IT-CHECK","$log_id ".my_json_encode($error,true)."\n".substr(debug_stack(),0,4096));
			quicklog_must("IT-CHECK","$log_id _SESSION=".my_json_encode($_SESSION));
			require_once _LIB_CORE_.DIRECTORY_SEPARATOR."inc.v5.secure.php";
			$_get_ip_=_get_ip_();
			quicklog_must("IT-CHECK","$log_id _get_ip_=$_get_ip_");
			//return $output;
		}
	}else{
		//quicklog_must("_shutdown_function","[Not Error Shutdown?]"."\n".substr(debug_stack(),0,4096));//debug_stack in inc.common.func.v5.php
		quicklog_must("_shutdown_function",var_export($error,true));//debug_stack in inc.common.func.v5.php
	}
	#ini_set("display_error", "Off");
}
function _shutdown_function_nojson(){
	_shutdown_function(false);
}

//除了FatalError以外的可以正常处理的UnexpectedError,(主要是留给非 RpcController模式 的代码所用!)
function exception_handler($ex){
	global $APP_NAME;

	$trace_s=$ex->getTraceAsString();
	$trace_s=substr($trace_s,0,4096);
	
	$rt=global_error_handler2($ex);
	$rt['nav_helper']="<a href='javascript:history.back();'>Go Back";

	$logid=_getbarcode();//for easier to trace ...
	$logfile=quicklog_must("app-$APP_NAME",$logid." ( inc.app.php exception_handler )\n".var_export($rt,true)."\n".$trace_s);
	quicklog_must("app-$APP_NAME", $logid."\n".var_export($_REQUEST,true)."\n".var_export($_SESSION,true));

	if($rt['errno']==0)unset($rt['errno']);
	unset($rt['trace']);//不给客户看到...
	unset($rt['trace_s']);//不给客户看到...
	$rt['file']=basename($rt['file'],".php");
	$rt['log_id']=$logid;
	$rt['log_file']=basename($logfile,".log");
	$s=json_encode($rt);
	print $s;
}


