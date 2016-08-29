<?php
/**
http://cmptech.info/
Usage
require_once 'CMP/bootstrap.php';
use CMP\LibCore;
LibCore::println( $_SERVER );
#\CMP\CmpCore::DefaultInit();
#var_dump(\CMP\Tester::getVersion());
 */
#NOTES: namespace need >=php5.3
namespace CMP
{
	if( !function_exists('spl_autoload_register') ){
		throw new Exception("\\CMP needs spl_autoload_register()");
	}
	class LibCore
	{
		public static function stderrln($s){
			file_put_contents('php://stderr',$s."\n",FILE_APPEND);
		}
		public static function stderr($s){
			file_put_contents('php://stderr',$s,FILE_APPEND);
		}
		public static function o2s($o,$wellformat=false){
			if($wellformat){
				if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
					$s=json_encode($o,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
				}else{
					$s=json_encode($o);//will have {"a":"b"} instead of {a:"b"}, but encode speed might slightly inproved
					$s=preg_replace('/","/',"\",\n\"",$s);//dirty work for tmp...
				}
			}else{
				if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
					$s=json_encode($o,JSON_UNESCAPED_UNICODE);
				}else{
					$s=json_encode($o);//NOTES: official json_encode must result {"a":"b"} instead of {a:"b"}, but encode speed might slightly inproved
				}
			}
			return $s;
		}
		public static function s2o($s){
			$o=json_decode($s,true);//true->array, false->obj, NOTES that the json_decode not support {a:"b"} but only support {"a":"b"}. it sucks!!
			return $o;
		}
		public static function println($s,$wellformat=false){
			if(is_array($s) || is_object($s)){
				//$s=json_encode($s,$wellformat);
				$s=self::o2s($s,$wellformat);
			}
			print $s ."\n";//.PHP_EOL;
		}
		public function web($url,$postdata,$timeout=7){
			if(is_array($postdata)){
				$postdata_s=http_build_query($postdata);
			}elseif(is_string($postdata)){
				$postdata_s=$postdata;
			}else{
				//throw new Exception("unknown param");
			}
			$url_a=parse_url($url);
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			if($url_a['scheme']=="https"){
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
			}
			if($postdata_s){
				curl_setopt($ch, CURLOPT_POST, true);
				//curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata_s);
			}
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			if($timeout>0 && $timeout<1){
				curl_setopt($ch, CURLOPT_NOSIGNAL,1);//ms
				curl_setopt($ch, CURLOPT_TIMEOUT_MS,200);//since cURL 7.16.2/PHP 5.2.3
			}elseif($timeout>=1){
				curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);
			}
			$result = curl_exec($curl);
			curl_close($curl);
			$errno=curl_errno($curl);
			if($errno){
				throw new Exception(curl_error($curl),$errno);
			}
			return $result;
		}
	}//LibCore
	class CmpClassLoader
		extends LibCore
	{
		public static function str_starts_with($haystack, $needle) {
			return preg_match('/^'.preg_quote($needle,'/').'/', $haystack) > 0;
		}
		public static function str_ends_with($haystack, $needle) {
			return preg_match('/'.preg_quote($needle,'/').'$/', $haystack) > 0;
		}
		public static function Size($path, $recursive = true)
		{
			$result = 0;

			if (is_dir($path) === true)
			{
				$path = self::Path($path);
				$files = array_diff(scandir($path), array('.', '..'));

				foreach ($files as $file)
				{
					if (is_dir($path . $file) === true)
					{
						$result += ($recursive === true) ? self::Size($path . $file, $recursive) : 0;
					}

					else if (is_file() === true)
					{
						$result += sprintf('%u', filesize($path . $file));
					}
				}
			}

			else if (is_file($path) === true)
			{
				$result += sprintf('%u', filesize($path));
			}

			return $result;
		}

		protected static function Path($path)
		{
			if (file_exists($path) === true)
			{
				$path = rtrim(str_replace('\\', '/', realpath($path)), '/');

				if (is_dir($path) === true)
				{
					$path .= '/';
				}

				return $path;
			}

			return false;
		}
		protected static function Map($path, $recursive = false)
		{
			$result = array();

			if (is_dir($path) === true)
			{
				$path = self::Path($path);
				$files = array_diff(scandir($path), array('.', '..'));

				foreach ($files as $file)
				{
					if (is_dir($path . $file) === true)
					{
						$result[$file] = ($recursive === true) ? self::Map($path . $file, $recursive) : self::Size($path . $file, true);
					}

					else if (is_file($path . $file) === true)
					{
						$result[$file] = self::Size($path . $file);
					}
				}
			}

			else if (is_file($path) === true)
			{
				$result[basename($path)] = self::Size($path);
			}

			return $result;
		}
		//return the md5 of the whole module CMP
		public static function getModuleMD5(){
			static $md5;
			if($md5) return $md5;
			$md5=md5(serialize(CmpClassLoader::Map(__DIR__, true)));
			return $md5;
		}
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
			$class_path_a=getConf("class_path_a");
			foreach(array_reverse($class_path_a) as $class_path){
				$ccc="$class_path/$class_name.php";
				if(file_exists($ccc)){
					#self::stderrln("### $ccc ###");
					require $ccc;
					return true;
				}else{
					#print("!!! $ccc !!!\n");
					#self::stderrln("!!! $ccc !!!");
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

		public static function tryLoad($classname){
			$ns="CMP\\";
			if(self::str_starts_with($classname,$ns)){
				$xxx=substr($classname, strlen($ns));
				if($xxx && $classname!= 'CMP'){
					#include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $xxx).'.php');
					include_once(__DIR__.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $xxx).'.php');
					if(class_exists($classname)){
					}else{
						self::stderrln("not found $classname\n");
					}
				}
			}
		}
	}
	//default behavior about to load class file under __DIR__.
	spl_autoload_register(function($class_name){
		#require_once __DIR__ .'/CmpClassLoader.php';
		CmpClassLoader::tryload($class_name);
	});
}

//几个很好用的快速函数.能大量减少代码量!! 没必要搬到LibBase...
namespace
{
	//Usage: eval(arr2var_all("param"));
	if(!function_exists('arr2var_all')){
		function arr2var_all($name_of_arr){
			return <<<EOS
eval(arr2var("$name_of_arr",array_keys(\$$name_of_arr)));
EOS
			;
		}
	}
	//Usage: eval(arr2var("arr",$arr));
	//Usage: eval(arr2var("arr",$arr,"v_"));
	if(!function_exists('arr2var')){
		function arr2var($name_arr,$arr,$prefix=""){
			if(!is_array($arr)){
				throw new Exception(getLang("KO-arr2var-notarray").my_json_encode($arr));
			}
			$rt="";
			foreach($arr as $key){$rt.='$'.$prefix.$key.'=$'.$name_arr.'["'.$key.'"];';}
			return $rt;
		}
	}
	//Usage: eval
	if(!function_exists('var2arr')){
		function var2arr($name_arr,$arr){
			$rt="";
			foreach($arr as $key){$rt.='$'.$name_arr.'["'.$key.'"]=$'.$key.';';}
			$rt.=";";
			return $rt;
		}
	}

	//Usage:
	//arr2arr($rt,$_SESSION,array('auth_user_id'));
	if(!function_exists('arr2arr')){
		function arr2arr(& $target, $src, $key_a,$prefix=""){
			if (is_null($target)){
				$target=array();
			}
			if (is_array($key_a)) {
				foreach($key_a as $k){
					$target[$prefix.$k]=$src[$k];
				}
			}else{
				//or all:
				foreach($src as $k=>$v){
					$target[$prefix.$k]=$v;
				}
			}
			return $target;//just for some special case
		}
	}

	// e.g.
	// abc'ced
	// abc''ced
	if(!function_exists('qstr2')){
		function qstr2($s){
			//just replace ' to ''
			return str_replace("'","''",$s);
		}
	}

	// e.g.
	// abc'ced
	// =>
	// 'abc''ced''
	if(!function_exists('qstr')){
		function qstr($s){
			$x="'".str_replace("'","''",$s) ."'";
			return $x;
		}
	}

	// e.g.
	// $s=qstr_arr(array("x","y");
	// => $s=> "'x','y'"
	if(!function_exists('qstr_arr')){
		function qstr_arr($_a){
			if(is_array($_a)){
			}else{
				//Let expect string...
				$_a=explode(",",$_a);
			}
			foreach($_a as $k=>$v){
				$_a[$k]=qstr($v);
			}
			return join(',',$_a);
		}
	}
}//namespace
