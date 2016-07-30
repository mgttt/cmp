<?php
class WebCmpDemo1
{
	public function DefaultIndex(){
		include(CmpService::TPL("DefaultIndex"));
	}

	public function Demo1_1(){
		include(CmpService::TPL("demo1.1"));
	}
	public function Demo1_2(){
		include(CmpService::TPL("demo1.2"));
	}
	public function Demo1_3(){
		include(CmpService::TPL("demo1.3"));
	}
}
