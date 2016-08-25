<?php
class WebCmpTester
	extends ApiDemo
{
	public function DefaultIndex($param){
		$test_tpl_1=(rand(1000)>500)?true:false;
		//$lang=$param['lang'];
		$test_tpl_1=rand(0,1000);//见模板 tester.DefaultIndex.htm
		include($this->TPL("DefaultIndex","tester"));
	}

	public function MiniAjaxCmp($param){
		//$test_tpl_1=(rand(1000)>500)?true:false;
		$lang=$param['lang'];
		$__function__=__FUNCTION__;
		include($this->TPL($__function__,"tester"/*前缀*/));
	}

	//////////////////////////////////////////////////////
	//魔法函数.当要呼叫的函数不在的时候就找到需要的模板，模板也不在就报错.
	public function __call($__function__, $param_a){
		$sid=session_id();
		$request_method_param=$param_a[0];
		$lang=$request_method_param['lang'];
		include($this->TPL($__function__,"tester"/*前缀*/));
	}
}
