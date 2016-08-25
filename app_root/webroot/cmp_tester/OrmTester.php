<?php
class OrmTester
	extends ORM_Base
{
	public $NAME_R = 'tbl_testobj';

	//根据我们的实用类 rbWrapper(针对 RedBeanPHP的二次封装工具类），定义 testobj的字段，快速设计阶段我们不用计较类型。RB会自动做转换.
	public $bean_name_a = array(
		"testobj_key",
		"testobj_status",
		"testobj_remark",
		"testobj_random",//临时.
	);

	public function SearchList($param){ throw new Exception("TODO"); }
	
	public function Upsert($param){ throw new Exception("TODO"); }
	//public function Insert($param){ throw new Exception("TODO"); }
	//public function Update($param){ throw new Exception("TODO"); }
	public function MarkDelete($param){ throw new Exception("TODO"); }
	public function RealDelete($param){ throw new Exception("TODO"); }

	public function Insert($ormInfo,$flag_just_id=false){

		//拆出变量:
		$field_name_a = $this->bean_name_a;
		//$testobj_key=$ormInfo['testobj_key'];
		//$testobj_status=$ormInfo['testobj_status'];
		//$testobj_remark=$ormInfo['testobj_remark'];
		eval(arr2var("ormInfo",$field_name_a));//arr2var() 主要是实现把数组中的值拆出来成为变量，节约代码量。

		//生成一个空的redbean
		$rb=$this->dispenseBean($this->NAME_R);

		//变量写回到bean的字段:
		//$rb['testobj_key']=$testobj_key;
		//$rb['testobj_status']=$testobj_status;
		//$rb['testobj_remark']=$testobj_remark;
		eval(var2arr("rb",$field_name_a));//跟arr2var作用刚好相反，把变量根据数组写回到 bean的值去.

		//保存bean
		$id = $this->store($rb);
		if($flag_just_id) return $id;
		$rt = $this->loadBean($id);
		return $rt;
	}

	public function Update($ormInfo){
		$orm_id = $ormInfo['id'];

		//读出该 bean
		$rb = $this->loadBean($orm_id);

		//参数拆出变量:
		$field_name_a = $this->bean_name_a;
		eval(arr2var("ormInfo",$field_name_a));

		$testobj_random='random-'.date('YmdHis');

		//变量写回字段:
		eval(var2arr("rb",$field_name_a));

		$id = $this->store($rb);

		$rt = $this->loadBean($id);//重新加载一下得到最新的结果.
		return $rt;
	}
	public function CalcTotalRecord($param)
	{
		//RB的getCell用法.
		$c=$this->getCell('SELECT COUNT(*) FROM ' .$this->NAME_R);
		return $c;
	}
}

