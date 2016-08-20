<?php
/**
 * 定义标准接口 insert/update/upsert/searchOne/searchList/markDelete/realDelete
 * 其中:
 * 1, realDelete执行 sql delete (一般不建议!) ;
 * markDelete (一般是 set .status=-1, .lmt=$isoDateTime 然后等 housekeeping 例程(routine)来清理);
 *
 * 2, searchOne (理论上直接呼叫searchList并拿第一个，否则就返回null) 不建议重载
 *
 * 3, searchList返回的是记录集（一行一个记录对象），
 * 如果用于GRIDTABLE，表头等需要自己在Lgc逻辑层再处理
 */
//{
//	//必须实现:
//	public function searchList($param);//Remember to implement at sub-class
//	
//	//可重载
//	public function upsert($param);//通常是 insert/update的混合体，有时可以节省代码量.但一般外部不用呼叫，默认不够用的可以重载.
//	//public function Replace($param);//因为是整个换掉，跟upsert有点不同，逻辑也不些复杂，暂时未要求实现，需要时在子类写吧。
//	public function insert($param,$flag_just_id=false);
//	public function update($param,$flag_just_id=false);
//
//	public function searchOne($param);//get the first by calling searchList();
//
//	public function markDelete($param);
//	public function markDeleteAll($param);
//	public function realDelete($param);//not suggest to use unless you are sure
//	public function realDeleteAll($param);//please dont use unless you are very very sure.
//}
class ORM_Base
	extends rbWrapper4 //@ cmp_core\
{
	public static $DSN='db_app';
	//public $bean_name_a;//please override by the children

	//return Array of one, not bean
	//can use $one['id'] to get a bean with .loadBean($id);
	public function searchOne($param){
		$param['LIMIT']=1;
		$rs=$this->searchList($param);
		$rst=$rs['rst'];
		if($rst && count($rst)>0){
			return $rst[0];
		}
		return null;
	}
	//MERGE 或 UPSERT:
	//1,将参数中的属性抄过去，跟 REPLACE 有不同，REPLACE 是把前者没有的参数都给删除掉....REPLACE不好用...
	//2,如果id的存在就做UPDATE，如果ID不存在就INSERT ID VALUES($id)
	//3,如果没有参数ID就当成INSERT
	//4，其它类型的 INSERT_UPDATE 另外实现参考上面Merge/Replace
	//@ref https://en.wikipedia.org/wiki/Merge_(SQL)
	public function upsert($param,$flagNew=true){
		$id=$param['id'];
		$flagReallyNew=false;
		if($id){
			//$bean=$this->loadBean($id);//rbWrapper
			$bean=$this->load($this->NAME_R,$id);//rbWrapper
			if($bean && $bean->id){
				//found
			}else{
				if($flagNew){
					if($id>0){
						$this->exec('INSERT INTO '.$this->NAME_R.' (id) VALUES('.$id.')');
					}else{
						throw Exception('NOT SUPPORT id '.$id);
					}
					$bean=$this->load($this->NAME_R,$id);
					if($bean && $bean->id){
						//ok now...
					}else{
						throw Exception('FAIL id '.$id);
					}
					//$bean=$this->dispenseBean();//rbWrapper
					#$bean->id=$id;//FAILED...
					$flagReallyNew=true;
				}else{
					throw new Exception(getLang('KO-loadBean-').$this->NAME_R.".$id");
				}
			}
		}else{
			if($flagNew){
				$bean=$this->dispenseBean();//rbWrapper
				$flagReallyNew=true;
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

		//FILTER PARAMS
		//如果这个param在预定义的field_name_a中，就复制值过去
		foreach ($param as $kk=>$vv){
			if( in_array($kk,$field_name_a) ||
				array_key_exists($kk,$field_name_a))
			{
				$bean[$kk] = $vv;
			}
		}

		//******** 其它特别处理{
		//基本通用:
		$isoDateTime = $this->isoDateTime();
		$bean->lmt = $isoDateTime;//LastModifiedTime
		if( $flagReallyNew ){
			$bean->status=0;
			$bean->create_time = $isoDateTime;//Time When Create
		}
		//其他字段:
		//******** 其它特别处理}

		$id = $this->store($bean);
		return $id;
	}//upsert

	public function insert($param,$flag_just_id=false){
		$id=$this->upsert($param,true);
		if($flag_just_id) return $id;
		$rt = $this->loadBean($id);
		return $rt;
	}

	public function update($param,$flag_just_id=false){
		$id=$this->upsert($param,false);
		if($flag_just_id) return $id;
		$rt = $this->loadBean($id);
		return $rt;
	}

	public function markDelete($param){
		throw new Exception("DENY markDelete");
	}
	public function realDelete($param){
		throw new Exception("DENY realDelete");
	}
	public function markDeleteAll($param){
		throw new Exception("DENY markDeleteAll");
	}
	public function realDeleteAll($param){
		throw new Exception("DENY realDeleteAll");
	}

	//由子类覆盖的查找 => Array 函数.
	public function searchList($param){
		throw new Exception("searchList() Need Override");
		//下面的是代码参考 ;)
		//字段集
		$field_name_a=$this->bean_name_a;
		//拆出变量
		eval(arr2var("param",$field_name_a));
		//定制查询
		//$_status_like = $param['_status_like'];
		//$_status_in = $param['_status_in'];
		//$_status_not_in = $param['_status_not_in_like'];

		$rb=$this;

		//根据param构建查询条件:
		$where = "1=1";

		//TODO 下面这样的模式重复代码可以归纳.
		//if($app_key){
		//	$where .= " AND app_key=".qstr($app_key);
		//}
		//if($app_secret){
		//	$where .= " AND app_secret=".qstr($app_secret);
		//}
		//if($device_id){
		//	$where .= " AND device_id=".qstr($device_id);
		//}
		//TODO 根据来者来判断来确定哪些任务是给它的
		//if($phone_number){
		//	$where .= " AND phone_number=".qstr($phone_number);
		//}
		//if($sms_status){
		//	$where .= " AND sms_status=".qstr($phone_number);
		//}
		//if($_status_like){
		//	$where .= " AND sms_status LIKE ".qstr($_status_like);
		//}
		//if($_status_in){
		//	$where .= " AND sms_status IN (".qstr_arr($_status_in) .")";
		//}
		//if($_status_not_in){
		//	$where .= " AND sms_status NOT IN (".qstr_arr($_status_not_in) .")";
		//}

		//$minute=60*24;//one day

		$_where_else= $param['_where_else'];
		if($_where_else){
			$where.=$_where_else;
		}
		//$_request_time_more_than = $param['_request_time_more_than'];
		//if($_request_time_more_than){
		//	//$where.=" AND request_time > NOW() - INTERVAL $minute MINUTE";
		//	$where.=" AND request_time > $_request_time_more_than";
		//}
		//if($_request_time_less_than){
		//	//$where.=" AND request_time > NOW() - INTERVAL $minute MINUTE";
		//	$where.=" AND request_time < $_request_time_more_than";
		//}
		if($id){
			$where.=' and id='.qstr($id);
		}
		$pageExecuteParam= array(
			"SELECT"=>"*",
			//"SELECT"=>"id, phone_num as sms_target, sms_status as status,sms_content,TIMESTAMPDIFF(SECOND, request_time,NOW()) AS diffsec ",//TIMESTAMPDIFF是后减前.
			"FROM"=>$this->NAME_R,
			"WHERE"=>$where,
			//"ORDERBY"=>"id DESC",
			//"pageSize"=>$pageSize,
			//"pageNumber"=>$pageNumber,
			//'LIMIT'=>$limit,
		);
		//TODO ORDERBY/pageSize/pageNumber
		$rs=$rb->PageExecute($pageExecuteParam);
		return $rs;
	}

	//WARNING: duplicate item created if design not good.
	//高并发下这样做是不行的。所以应用场合有限，特别注意！
	//try to find first, if found then return, if not found then do insert()
	public function findOrInsert($param,$flag_just_id=true){
		$one=$this->searchOne($param);
		if($one){
			if(!$flag_just_id) return $one;
			$id=$one['id'];
		}else{
			//TODO 要解决高并发时的问题；改实现思路；
			$id=$this->insert($param);
		}
		if($flag_just_id) return $id;
		$rt = $this->loadBean($id);
		return $rt;
	}
	
}
