<?php
//@deprecated !! moved to LibBase mostly.
class EnvHelper
{
	public static function getMyIsoDateTime(){
		return date('YmdHis');
	}
	public static function getMyEnvVar($k){
		$rt=getenv($k);
		if($rt && $rt!="") return $rt;
		$rt=$_SERVER[$k];
		if($rt && $rt!="") return $rt;
		return null;
	}
	public static function getMyHostName(){

#$HTTP_VIA=strtolower($_SERVER['HTTP_VIA']);
#$HTTP_X_FORWARDED_SERVER=strtolower($_SERVER['HTTP_X_FORWARDED_SERVER']);
		$SERVER_NAME=($_SERVER['SERVER_NAME']);
		$HTTP_HOST=($_SERVER['HTTP_HOST']);
#$MY_HOSTNAME=$HTTP_HOST or $SERVER_NAME;
		if($HTTP_HOST) $MY_HOSTNAME=$HTTP_HOST;
		elseif($SERVER_NAME) $MY_HOSTNAME=$SERVER_NAME;
		return strtolower($MY_HOSTNAME);
	}
	public static function getMyUri(){
		//$PHP_SELF=$_SERVER['PHP_SELF'];
		$MY_URI=$_SERVER['REQUEST_URI'];
#NO !!! if(!$MY_URI) $MY_URI=$_SERVER['PATH_INFO'];
		return $MY_URI;
	}
	public static function getMyScheme(){
		$MY_SCHEME=$_SERVER['HTTP_X_FORWARDED_PROTO'];
		if(!$MY_SCHEME) $MY_SCHEME=$_SERVER['REQUEST_SCHEME'];

		//TODO if any other case of https...

		if(!$MY_SCHEME) $MY_SCHEME='http';
		return strtolower($MY_SCHEME);
	}
	public static function getClientIp(){
		static $_ip="";
		do{
			if($_ip!="") return $_ip;

			$LOCAL127="127.0.0.1";

			$HTTP_X_FORWARDED_FOR=self::getMyEnvVar("HTTP_X_FORWARDED_FOR");
			if($HTTP_X_FORWARDED_FOR)
				list($HTTP_X_FORWARDED_FOR)= explode(",",$HTTP_X_FORWARDED_FOR);
			if($HTTP_X_FORWARDED_FOR && $HTTP_X_FORWARDED_FOR!=$LOCAL127){
				$_ip=$HTTP_X_FORWARDED_FOR;break;
			}

			$HTTP_X_REAL_IP=self::getMyEnvVar("HTTP_X_REAL_IP");
			if($HTTP_X_REAL_IP && $HTTP_X_REAL_IP!=$LOCAL127){
				$_ip=$HTTP_X_REAL_IP;break;
			}

			$HTTP_CLIENT_IP=self::getMyEnvVar("HTTP_CLIENT_IP");
			if($HTTP_CLIENT_IP && $HTTP_CLIENT_IP!=$LOCAL127){
				$_ip=$HTTP_CLIENT_IP;break;
			}

			$REMOTE_ADDR=self::getMyEnvVar("REMOTE_ADDR");
			//		if($REMOTE_ADDR && $REMOTE_ADDR!=$LOCAL127){
			//			$_ip=$REMOTE_ADDR;break;
			//		}
			$_ip=$REMOTE_ADDR;
		}while(false);
		return($_ip);
	}
}
