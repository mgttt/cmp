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
use \CMP\LibCore;
class ORM_Base
	extends rbWrapper //@ref CMP
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
						//$this->exec('INSERT INTO '.$this->NAME_R.' (id) VALUES('.$id.')');
						//上面这个在没表的时候不行。下面是利用RB特性自动快速建表，再改ID.
						$bean=$this->dispenseBean();//rbWrapper
						$new_id=$this->store($bean);
						if($new_id){
							$sql='UPDATE '.$this->NAME_R.' SET id='.qstr($id).' WHERE id='.$new_id
								.' LIMIT 1'//免得有重大意外...虽然从来没遇到过有意外...
								;
							print $sql;
							$this->exec($sql);
						}else{
							throw new Exception('FAIL CREATE id '.$id);
						}
					}else{
						throw new Exception('NOT SUPPORT id '.$id);
					}
					$bean=$this->load($this->NAME_R,$id);
					if($bean && $bean->id){
						//ok now...
					}else{
						throw new Exception('FAIL id '.$id);
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

		//【笔记】
		//如果param是从API层来的，如果参数检查、权限检查不细致或者漏了，然后直接呼叫Orm层时
		//这里可能会导致被更新掉状态/ID/密码等等，从而产生安全漏洞！！
		//特别是 party/app/user/acct 表这种特别的必须要加强代码方法。 TODO
		//if($flag_check_import && $flag_important){
		//}

		//自带状态的处理:
		if($status==''){
			$status=$param['status'];
		}
		if( $flagReallyNew ){
			if($status!=''){
				$bean->status=$status;
			}else{
				$bean->status=0;
			}
			$bean->create_time = $isoDateTime;//Time When Create
		}else{
			if(!$bean->create_time){
				$bean->create_time=$isoDateTime;
			}
			if($status!=''){
				$bean->status=$status;
			}
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

	//TODO @deprecated !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	//@see findOrUpsert()
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
	// NOTES: using trick sql to fulfil the upsert($toUpdate) by filter($toFind)
	// INSERT INTO $table (k1,k2)
	// SELECT * FROM (SELECT 1,2) AS $tmp_table
	// WHERE NOT EXISTS (SELECT 'Y' FROM $table WHERE k1='$k1' AND k2=$k2 LIMIT 1)
	// $toFind=array('k1'=>$k1,'k2'=>$k2);$toUpdate=array('k1'=>$k1,'k2'=>$k2,'k3'=>$k3);
	public function findAndUpsert($toUpdate, $toFind){
		$table = $this->NAME_R;
		if(!$table) throw new Exception("SYSTEM ERROR: findAndUpsert() failed for empty NAME_R");
		if(is_array($toUpdate) && count($toUpdate)>0){
			//Y
		}else{
			throw new Exception("SYSTEM ERROR: findAndUpsert() not accept empty \$toUpdate");
		}
		if(is_array($toFind) && count($toFind)>0){
			//Y
		}else{
			throw new Exception("SYSTEM ERROR: findAndUpsert() not accept empty \$toFind");
		}
		$tmp_table='TMP_'.LibCore::getYmdHis();
		$where='WHERE 1=1';
		$c=0;
		$s_k="";
		$s_v="";
		foreach($toFind as $k=>$v){
			$where.=" AND $k=".qstr($v);
			$s_k.=($c>0?',':'').$k;
			$s_v.=($c>0?',':'').qstr($v);
			$c++;
		}
		if(!$s_k){
			throw new Exception("SYSTEM ERROR: findAndUpsert() meet a empty \$toFind");
		}
		$id=$this->getCell("SELECT id FROM $table $where LIMIT 1");
		if($id){
			//OK
			$toUpdate['id']=$id;
			$rb=$this->update($toUpdate);
		}else{
			//try insert (with atomic operation)
			$sql="INSERT INTO $table ($s_k) SELECT * FROM (SELECT $s_v) AS $tmp_table WHERE NOT EXISTS (SELECT 'Y' FROM $table $where LIMIT 1)";
			$af_insert=$this->execute($sql);
			$id=$this->getCell("SELECT id FROM $table $where LIMIT 1");
			if($id){
				//OK
				$toUpdate['id']=$id;
				$rb=$this->update($toUpdate);
			}else{
				//INSERT FAILED??
				throw new Exception("SYSTEM ERROR: findAndUpsert() failed to INSERT when not found for the \$toFind");
			}
		}
		return array(
			'sql'=>$sql,
			'insert'=>$af_insert,
			'id'=>$id,
			'rb'=>$rb,
		);
	}
}
