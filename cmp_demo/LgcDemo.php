<?php
class LgcDemo
	//extends LgcBase
{
	public static function getDSN(){
		//因为是临时的，所以用 sqlite来做数据库接入便于测试。一般项目是要在conf那里配置DB链接，部分SAAS项目还要去 SAAS中心用API拿配置.
		$dsn="sqlite:"._TMP_."test.db";
		quicklog_must("DEBUG","dsn=$dsn");
		return $dsn;
	}
	public function ListTestNote($param){
		$rb=new rbWrapper4(LgcDemo::getDSN());
		$where = "1=1";
		//根据param构建查询条件

		$rs=$rb->PageExecute(array(
			"SELECT"=>"*",
			"FROM"=>"tbl_note",  
			"WHERE"=>$where,
			"ORDER"=>"id DESC "
		));

		//表馅:
		$rt['table_data']=$rs['rst'];

		//表头:
		$cols=array(
			array('field'=>'note_key','title'=>"主键"),
			array('field'=>'note_status','title'=>"状态"),
			array('field'=>'note_remark','title'=>"备注"),
		);

		$rt['table_columns']=array($cols);
		//总数量 for SimpleGrid
		$rt['maxRowCount']=$rs['maxRowCount'];

		return $rt;
	}
}

