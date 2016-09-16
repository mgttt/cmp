<?php
namespace CMP
{
	class LibExt
		extends LibBase
	{
		public static function getServerTimeZone(){
			//The plus and minus signs (+/-) are not intuitive. For example,
			//"Etc/GMT-10" actually refers to the timezone "(GMT+10:00)
			$server_timezone = self::getConf('SERVER_TIMEZONE');
			//$server_timezone = str_ireplace("etc/", "", $server_timezone)
		/*if (strpos($server_timezone, "-") !== false) {
			$server_timezone = str_replace("-", "+", $server_timezone);
		} else if (strpos($server_timezone, "+") !== false) {
			$server_timezone = str_replace("+", "-", $server_timezone);
		}*/
			$GMT_TIMEZONE = self::getConf('GMT_TIMEZONE');
			return $GMT_TIMEZONE[$server_timezone];
		}
		//unicode(UCS-2 to any)
		public function unicode2any($str,$target_encoding="UTF-8"){
			$str = rawurldecode($str);
			//print $str."\n\n";
			preg_match_all("/(?:%u.{4})|.{4};|&#\d+;|.+/U",$str,$r);
			$ar = $r[0];
			foreach($ar as $k=>$v) {
				if(substr($v,0,2) == "&#") {
					$ar[$k] = iconv("UCS-2",$target_encoding,pack("n",substr($v,2,-1)));
				}
				elseif(substr($v,0,2) == "%u"){
					$ar[$k] = iconv("UCS-2",$target_encoding,pack("H4",substr($v,-4)));
				}
				elseif(substr($v,0,3) == ""){
					$ar[$k] = iconv("UCS-2",$target_encoding,pack("H4",substr($v,3,-1)));
				}
			}
			return join("",$ar);
		}

		public static function adjust_timezone($SERVER_TIMEZONE){
			if(!$SERVER_TIMEZONE)
				$SERVER_TIMEZONE=getConf('SERVER_TIMEZONE');
			if($SERVER_TIMEZONE==''){
				throw new Exception("SERVER_TIMEZONE_must_be_config");
			}else{
				//override the one in init.  NOTES.  u might need to make a tester for this.
				$ini_get_date_timezone=ini_get("date.timezone");
				if($SERVER_TIMEZONE!=ini_get("date.timezone")){
					ini_set("date.timezone",$SERVER_TIMEZONE);
					//date_timezone_set( $o, new \DateTimeZone($SERVER_TIMEZONE) );
				}
			}
		}

		//新便捷函数，代替 checkMandatory，轮数组然后告诉哪个是需要的。
		//Example:
		//mg::checkRequired($param,array("name"=>getLang("name"));
		//or
		//mg::checkRequired($param,array("name");
		public static function checkRequired($arr,$key_a, $msg_tpl="MSG_ParamIsRequired"){
			$flag_is_assoc=LibExt::is_assoc($key_a);
			foreach($key_a as $k=>$v){
				$kk=($flag_is_assoc)?$k:$v;
				//$vv=($flag_is_assoc)?$v:$k;
				$vv=$v;
				$f=$arr[$kk];
				if(!$f && $f!==0 && $f!=='0'){
					//参数1 $arr里面的 指定字段的值 如果不为真而且又不是 数字0或者字符串'0'，就理解为缺失，应抛出异常.
					throw new Exception( vsprintf(getLang($msg_tpl),array($vv)) );
				}
			}
		}

		//便捷函数: 如果 第一参数为真时，用第二、第三参数构造异常抛出.
		//第二参数是 第三参数模板的参数
		public static function checkCond($flag,$msg_param, $msg_tpl="MSG_ParamIsRequired"){
			if($flag){
				throw new Exception( vsprintf(getLang($msg_tpl),$msg_param));
			}
		}
		//@deprecated, 建议用上面checkRequired
		public static function checkMandatory($arr,$key_a){
			foreach($key_a as $v){
				$f=$arr[$v];
				if(!$f && $f!==0 && $f!=='0') throw new Exception(getLang('KO-checkMandatory')." $v");
			}
		}
		//TODO 还没想好 checkFormat怎么做.
		//public static function checkFormat($flag,$msg_param, $msg_tpl="MSG_ParamIsRequired"){
		//	if($flag){
		//		throw new Exception( vsprintf(getLang($msg_tpl),$msg_param));
		//	}
		//}

		public static function get_env($k){
			$rt=getenv($k);
			if($rt && $rt!="") return $rt;
			$rt=$_SERVER[$k];
			if($rt && $rt!="") return $rt;
			return null;
		}
		public static function get_ip(){
			static $_ip="";
			do{
				if($_ip!="") return $_ip;

				$LOCAL127="127.0.0.1";

				$HTTP_X_REAL_IP=LibExt::get_env("HTTP_X_REAL_IP");
				if($HTTP_X_REAL_IP && $HTTP_X_REAL_IP!=$LOCAL127){
					$_ip=$HTTP_X_REAL_IP;break;
				}

				$HTTP_CLIENT_IP=LibExt::get_env("HTTP_CLIENT_IP");
				if($HTTP_CLIENT_IP && $HTTP_CLIENT_IP!=$LOCAL127){
					$_ip=$HTTP_CLIENT_IP;break;
				}

				$HTTP_X_FORWARDED_FOR=LibExt::get_env("HTTP_X_FORWARDED_FOR");
				if($HTTP_X_FORWARDED_FOR)
					list($HTTP_X_FORWARDED_FOR)= explode(",",$HTTP_X_FORWARDED_FOR);
				if($HTTP_X_FORWARDED_FOR && $HTTP_X_FORWARDED_FOR!=$LOCAL127){
					$_ip=$HTTP_X_FORWARDED_FOR;break;
				}

				$REMOTE_ADDR=LibExt::get_env("REMOTE_ADDR");
				//		if($REMOTE_ADDR && $REMOTE_ADDR!=$LOCAL127){
				//			$_ip=$REMOTE_ADDR;break;
				//		}
				$_ip=$REMOTE_ADDR;
			}while(false);
			return($_ip);
		}
		//judge array whether a associate array
		//https://gist.github.com/1965669
		public static function is_assoc($array){
			return (array_values($array) !== $array);
		}
		//get the sequence of a single second (in single thread...)
		//Usage list($sec,$seq)=mg::getTimeSequence();echo "$sec.$seq";
		public static function getTimeSequence(){
			global $getTimeSequence;
			//list($sec,$microsec)=explode('.',microtime(true));
			$sec = LibExt::getYmdHis();
			if($getTimeSequence){
				if($getTimeSequence['sec']!=$sec){
					$getTimeSequence=array('sec'=>$sec, 'seq'=>1);
				}else{
					$getTimeSequence=array('sec'=>$sec, 'seq'=>$getTimeSequence['seq']+1);
				}
			}else{
				$getTimeSequence=array('sec'=>$sec, 'seq'=>1);
			}
			//return $getTimeSequence;
			return array($getTimeSequence['sec'], $getTimeSequence['seq']); 
		}
		//since 2015-8-6
		//function cmp_exit($p){
		//	//TODO 判断 swoole环境下的话要用 swoole_process->exit();
		//	if($p) exit($p);
		//	else exit();
		//}
		//function cmp_die($p){
		//	//TODO 判断 swoole环境下的话要用 swoole_process->close();
		//	if($p) die($p);
		//	else die();
		//}
		public static function _change_session($sid){
			$_prev_sid=session_id();
			if($sid && $sid!=$_prev_sid){
				//万一session有变:
				session_write_close();

				session_id($sid);
				session_start();
				return $_prev_sid;
			}else{
				//如果一样就不用变了..
			}
		}
		//获得制定session id的，一般很少情况下用，多数是一些自动化cron job在用..
		public static function getSession($sid){
			if($sid){
				//change session
				$_prev_sid=LibExt::_change_session($sid);

				//steal
				$session_clone=$_SESSION;

				//restore prev
				session_id($_prev_sid);
				session_start();
				return $session_clone;
			}else{
				throw new Exception("getSession() needs param, if want to use SESSION, just access \$_SESSION for now");
			}
		}
		public static function getSessionVar($key, $sid="", & $probe_sid){
			if($sid){
				$_session= & LibExt::getSession($sid);
			}else{
				$_session= & $_SESSION;
			}
			//2016-7-13: CMP no longer handle cookie/_s
			//if($_session){
			//	//skip, already started.
			//}else{
			//	//$_s_cookie=$_COOKIE['_s'];
			//	//if($_s_cookie){
			//	//	session_id($_s_cookie);
			//	//}
			//	//session_start();
			//	//$sid=session_id();
			//	//if($sid!=$_s_cookie){
			//	//	if($_s_cookie){
			//	//		quicklog_must('KO-CHECK-SESSION-3',$_SESSION);
			//	//		session_start($_s_cookie);
			//	//		quicklog_must('KO-CHECK-SESSION-3',$_SESSION);
			//	//		throw new Exception('KO-SESS');
			//	//	}else{
			//	//		setcookie("_s",$sid);//通知一下浏览器的 _s变更
			//	//	}
			//	//}
			//}
			return $_session[$key];
		}
		//笔记：如果只需要写直接用 _SESSION[?]=? 更好，
		//2、记得有写的话就用session_write_close 来提交更新！！！
		public static function setSessionVar($key, $var, $sid=""){
			if($sid){
				$_prev_sid=LibExt::_change_session($sid);
			}else{
				$current_sid=session_id();
				session_start();//open it for write
			}
			$_SESSION[$key]=$var;
			if($sid){
				LibExt::_change_session($_prev_sid);
			}
			return $var;
		}
		public static function
			__callStatic( $__function__, $param_a ){
				//TODO 查找相关的函数和函数...
				throw new Exception("TODO FUNC $__function__");
			}
	}
}
