<?php
//TODO 要把 ORM_Base 想办法合并... 高难度....因为影响的地方比较多...可以考虑让ORM_Base继承OrmBase...

/**
 * 定义标准接口 Insert/Update/Upsert/SearchOne/SearchList/MarkDelete/RealDelete
 * 其中:
 * 1, RealDelete执行 sql delete，一般不建议, 而 MarkDelete一般是 set .status=-1, .lmt=$isoDateTime 然后等 housekeeping 例程(routine)来清理.
 * 2, SearchOne理论上直接呼叫SearchList并拿第一个就可以，否则就返回null
 * 3, SearchList返回的是记录集（一行一个记录对象），表头等需要自己在Lgc逻辑层微处理. 如果有时为了流量考虑不按规范格式也不在业务对象层和Orm层处理，需要在Lgc处理.
 */
//interface iOrmBase
//{
//	//必须实现:
//	public function SearchList($param);//Remember to implement at sub-class
//	
//	//需要重载，否则一般不够用
//	public function Upsert($param);//通常是 Insert/Update的混合体，有时可以节省代码量.但一般外部不用呼叫，默认不够用的可以重载.
//	//public function Replace($param);//跟Upsert有点不同。暂时未要求实现
//	public function Insert($param,$flag_just_id=false);
//	public function Update($param,$flag_just_id=false);
//
//	public function SearchOne($param);//基本够用的.
//
//	public function MarkDelete($param);
//	public function RealDelete($param);
//	public function RealDeleteAll($param);
//	public function MarkDeleteAll($param);
//}

/***
 *  NOTES: 基础够用，否则如果不够用，先在子类重载，如果未经评审，不要提升到这个公共类!!!
 */
abstract class OrmBase
	extends rbWrapper4 //at cmp_core\
	//implements iOrmBase
{
	public function SearchOne($param){
		$param['LIMIT']=1;
		$rs=$this->SearchList($param);
		$rst=$rs['rst'];
		if($rst && count($rst)>0){
			return $rst[0];
		}
		return null;
	}
	//@ref https://en.wikipedia.org/wiki/Merge_(SQL)
	//MERGE 或 UPSERT:
	//1,将参数中的属性抄过去，跟REPLACE 有不同，后者是把前者没有的参数给删除掉.
	//2,如果不存在就根据参数新建.
	//NOTES: 如果这个函数甚至这个OrmBase类不合适用，可以在自己的空间重载!!!!!!!!!!!!!!!!!!!
	public function Upsert($param,$flagNew=true){
		$id=$param['id'];
		if($id){
			$bean=$this->loadBean($id);//rbWrapper
		}else{
			if($flagNew){
				$bean=$this->dispenseBean();//rbWrapper
			}else{
				throw new Exception(getLang("KO-need-id"));
			}
		}
		//拆出变量:
		$field_name_a=$this->bean_name_a;

		////如果重载本函数，下面的代码可以参考{
		//eval(arr2var("param",$field_name_a));
		//变量写入属性:
		//eval(var2arr("bean",$field_name_a));
		////如果重载本函数，这段代码可以参考}

		foreach ($param as $kk=>$vv){
			if(
				in_array($kk,$field_name_a)
				||
				array_key_exists($kk,$field_name_a)
			){
				//如果这个param在预定义的field_name_a中，就复制值过去
				$bean[$kk] = $vv;
			}
		}

		//******** 其它特别处理{
		//基本通用:
		$isoDateTime = $this->isoDateTime();
		$bean->lmt = $isoDateTime;//LastModifiedTime
		if($flagNew){
			$bean->status=0;
			$bean->create_time = $isoDateTime;//Time When Create
		}
		//其他字段:
		//******** 其它特别处理}

		//保存.
		$id = $this->store($bean);
		return $id;
	}
	public function Insert($param,$flag_just_id=false){
		$id=$this->Upsert($param,true);
		if($flag_just_id) return $id;
		$rt = $this->loadBean($id);
		return $rt;
	}
	public function Update($param,$flag_just_id=false){
		$id=$this->Upsert($param,false);
		if($flag_just_id) return $id;
		$rt = $this->loadBean($id);
		return $rt;
	}
	public function MarkDelete($param){
		throw new Exception("DENY MarkDelete");
	}
	public function RealDelete($param){
		throw new Exception("DENY RealDelete");
	}
	public function MarkDeleteAll($param){
		throw new Exception("DENY MarkDeleteAll");
	}
	public function RealDeleteAll($param){
		throw new Exception("DENY RealDeleteAll");
	}
	public function SearchList($param){
		throw new Exception("SearchList Need Override");
	}
}

