<?php
//应用对象层.
class AppTester
{
	protected $_inner_orm;
	public function __construct($dsn,$freeze){
		if($dsn){
		}else{
			$dsn=LgcTester::getDSN();
		}
		$this->_inner_orm=new OrmTester($dsn,$freeze);
	}
	//public function SearchList2($param){
	//	$rt=$this->_inner_orm->SearchList($param);

	//	$rt['c']=count($rt['rst']);
	//	return $rt;
	//}
	//public function SearchList($param){
	//	$rt=$this->_inner_orm->SearchList($param);
	//	$table_data=$rt['table_data'];
	//	$table_data_c=count($table_data);
	//	$rt['table_data_c']=$table_data_c;
	//	return $rt;
	//}
	public function CalcTotalRecord($param){
		$orm=$this->_inner_orm;
		$rt=$orm->CalcTotalRecord($param);
		return $rt;
	}
	public function SearchList($param){
		//$orm=new OrmTester(self::getDSN());
		$orm=$this->_inner_orm;
		//$orm=$this;

		//根据param构建查询条件
		$where = "1=1";
		$testobj_status=$param['testobj_status'];
		if($testobj_status){
			//$where.=" AND testobj_status=".qstr($testobj_status);
			$where.=" AND testobj_status=?";
			$binding[]=$testobj_status;
		}
		$testobj_remark=$param['testobj_remark'];
		if($testobj_remark){
			$where.=" AND testobj_remark LIKE '%".qstr2($testobj_remark)."%'";
		}
		$testobj_key=$param['testobj_key'];
		if($testobj_key){
			$where.=" AND testobj_key=".qstr($testobj_key);
		}

		$rs=$orm->PageExecute(array(
			"SELECT"=>"*",
			"FROM"=>"tbl_testobj",
			"WHERE"=>$where,
			"ORDER"=>"id DESC ",
			"binding"=>$binding,
		));

		//表馅:
		$rt['table_data']=$rs['rst'];

		//表头:
		$cols=array(
			array('field'=>'id','title'=>"id","hidden"=>true),
			array('field'=>'testobj_key','title'=>"主键"),
			array('field'=>'testobj_status','title'=>"状态"),
			array('field'=>'testobj_remark','title'=>"备注"),
		);

		$rt['table_columns']=array($cols);
		//总数量 for SimpleGrid
		$rt['maxRowCount']=$rs['maxRowCount'];
		$rt['sql']=$rs['sql'];#TMP DEBUG...

		$table_data=$rt['table_data'];
		$table_data_c=count($table_data);
		$rt['table_data_c']=$table_data_c;
		return $rt;
	}
	public function __call($func, $args){
		$call_ee=array($this->_inner_orm, $func);
		if ( !is_callable($call_ee) ){
			throw new Exception("Unknown Func $func");
		}
		return call_user_func_array( $call_ee, $args );
	}
}
