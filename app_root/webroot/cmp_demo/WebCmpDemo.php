<?php
class WebCmpDemo
	extends ApiDemo
{
	public function DefaultIndex(){
		$test_tpl_1=rand(0,1000);
		include($this->TPL("DefaultIndex","demo"));
	}

	//////////////////////////////////////////////////////
	//默认处理魔法函数.
	//如果 _m未在本_c定义，本魔法函数_call()去渲染模板 demo.$_m.htm
	public function __call($__function__, $param_a){
		$sid=session_id();
		//$lang=$this->checkLang();
		//$device=$_SESSION['device'];
		include($this->TPL($__function__,"demo"));
	}
	/**
	 *  新增函数请放在 __call 上方 !
	 */
}
