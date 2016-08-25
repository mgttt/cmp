<?php
namespace CMP
{
	#if(!class_exists("\\CMP\\CmpClassLoader")){//暂时先这样解决.
	class CmpClassLoader
	{
		protected static function str_starts_with($haystack, $needle) {
			return preg_match('/^'.preg_quote($needle,'/').'/', $haystack) > 0;
		}
		protected static function stderrln($s){
			file_put_contents('php://stderr',$s."\n",FILE_APPEND);
		}
		protected static function Size($path, $recursive = true)
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

			//LibBase::stderrln(var_export($result,true));
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
	#}//!class_exists
}//namespace
