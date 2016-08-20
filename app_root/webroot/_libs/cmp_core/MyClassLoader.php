<?php
class MyClassLoader
{
	protected static function endsWith($haystack,$needle){
		return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
	}
	public static function recompile(){
		$class_file_name = _TMP_ ."/class_a.php";
		//compile
		$c_a=array();
		$class_path_a=getConf("class_path_a");//配置里面的目录数组..
		foreach(array_reverse($class_path_a) as $class_path){
			foreach(scandir($class_path) as $v){
				if(self::endsWith($v,".php")){
					$c=basename($v,".php");
					if($c){
						$c_a[$c]=realpath("$class_path/$v");//push
					}
				}
			}
		}
		$file_s=var_export($c_a,true);
		$file_s="\$class_a=$file_s;";
		file_put_contents($class_file_name,"<"."?php\n$file_s");
	}
	//以前的load似乎效率更高....
	public static function old_load($class_name){

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
				require $ccc;
				return true;
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
	public static function load($class_name){
		return self::old_load($class_name);//还是先用旧的，用编译的方法因为不深入到子目录（不能，因为会让多个app冲突），不能获得较大的加载优势.
	}

}
