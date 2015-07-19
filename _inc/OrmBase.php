<?php
class OrmBase
	extends rbWrapper4 //at cmp_core
{
	//TODO 放一些Orm非常通用的函数...不过...暂时...无....
}

/** 通用参考代码

	//@ref https://en.wikipedia.org/wiki/Merge_(SQL)
	//MERGE / UPSERT: 将参数中的属性抄过去，跟REPLACE 有不同，后者是把前者没有的给删除掉.
	//另外如果不存在就新建.
	public function UpsertOrm($ormInfo,$flagNew=true){

		$id=$ormInfo['id'];
		if($id){
			$bean=$this->loadBean($id);
		}else{
			if($flagNew){
				$bean=$this->dispenseBean();
			}else
				throw new Exception(getLang("KO-updateOrm-need-id"));
		}

		//拆出变量:
		$field_name_a=$this->bean_name_a;
		eval(arr2var("ormInfo",$field_name_a));

		//变量写入属性:
		eval(var2arr("bean",$field_name_a));

		//******** 其它特别处理{
		//******** 其它特别处理}

		//保存.
		$id = $this->store($bean);
		return $rt;
	}	

	public function newOrm($ormInfo,$flag_just_id=false){
		$id=$this->UpsertOrm($ormInfo,true);
		if($flag_just_id) return $id;
		$rt = $this->loadBean($id);
		return $rt;
	}

	public function updateOrm($ormInfo,$flag_just_id=false){
		$id=$this->UpsertOrm($ormInfo,false);
		if($flag_just_id) return $id;
		$rt = $this->loadBean($id);
		return $rt;
	}


 */
/**
	public function newOrm($ormInfo,$flag_just_id=false){
		//拆出变量:
		$field_name_a = $this->bean_name_a;
		eval(arr2var("ormInfo",$field_name_a));
		$bean=$this->dispenseBean($this->NAME_R);

		//变量写回属性:
		eval(var2arr("bean",$field_name_a));

		//其它特别处理:

		//保存.
		$id = $this->store($bean);
		if($flag_just_id) return $id;
		$rt = $this->loadBean($id);
		return $rt;
	}

	public function updateOrm($orm_id,$ormInfo){
		$orm = $this->loadBean($orm_id);

		//拆出变量:
		$field_name_a = $this->bean_name_a;
		eval(arr2var("ormInfo",$field_name_a));

		//变量写回属性:
		eval(var2arr("orm",$field_name_a));

		//其它特别处理:

		$id = $this->store($orm);
		$rt = $this->loadBean($id);
		return $rt;
	}
 */


