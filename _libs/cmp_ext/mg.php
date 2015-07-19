<?php
//Since 2015-1-4
//A MG TOOL
class mg
{
	//判断是否关联型数组:
	//https://gist.github.com/1965669
	public static function is_assoc($array){
		return (array_values($array) !== $array);
	}

	public static function o2s($o){
		return json_encode($o);
	}

	public static function s2o($s){
		return json_decode($s,true);
	}

	//新便捷函数，代替 checkMandatory，轮数组然后告诉哪个是需要的。
	//Example:
	//mg::checkRequired($param,array("name"=>getLang("name"));
	//or
	//mg::checkRequired($param,array("name");
	public static function checkRequired($arr,$key_a, $msg_tpl="MSG_ParamIsRequired"){
		$flag_is_assoc=self::is_assoc($key_a);
		foreach($key_a as $k=>$v){
			$kk=($flag_is_assoc)?$k:$v;
			//$vv=($flag_is_assoc)?$v:$k;
			$vv=$v;
			$f=$arr[$kk];
			if(!$f && $f!==0 && $f!=='0'){
				throw new Exception( vsprintf(getLang($msg_tpl),array($vv)) );
			}
		}
	}
	
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
	//还没想好 checkFormat怎么做.
	//public static function checkFormat($flag,$msg_param, $msg_tpl="MSG_ParamIsRequired"){
	//	if($flag){
	//		throw new Exception( vsprintf(getLang($msg_tpl),$msg_param));
	//	}
	//}

	public static function __callStatic($__function__, $param_a){
		//TODO 查找相关的函数和函数...
		throw new Exception("TODO FUNC $__function__");
	}

}
