<?php
class LgcTester
	//extends LgcBase
{
	public static function getDSN(){
		if(defined('SAE_TMP_PATH')){
			//SAE not supports sqlite...
			$dsn="db_app";//@ref ./_conf.{$_config_switch}/{$_config_switch}.php
		}else{
			#$dsn="sqlite:"._TMP_."test.db";
			$dsn="db_app";//测试一下远程数据库
		}
		return $dsn;
	}
	public function ListTestObj($param){
		$appObj = new AppTester();//业务对象

		//注：这里由于时间关系其实已经跳过【参数调整】的代码，直接把 param 丢下一层.

		$rt=$appObj->SearchList($param);
		$ttt=$appObj->CalcTotalRecord();//for another biz logic test only...
		$rt['ttt']=$ttt;

		return $rt;
	}
	public function ListTestObj2($param){
		$appObj = new AppTester();//业务对象

		//注：这里由于时间关系其实已经跳过【参数调整】的代码，直接把 param 丢下一层.

		$rt=$appObj->SearchList2($param);

		return $rt;
	}

	public function DeleteTestObj($param){
		$cls = new OrmTester(self::getDSN());
		$cls->deleteBean($param['id']);
		return array("STS"=>"OK");
	}

	public function AddTestObj($param){
		$rt=array();
		$dsn=self::getDSN();
		$rt['dsn']=$dsn;//for debug
		$rt['act']='add';//for debug
		$rt['param']=$param;
		$rt['_POST']=$_POST;

		$param['testobj_status'] = "0";

		$orm_class = new OrmTester($dsn);

		$rst = $orm_class->insert($param);//TODO 要从ACE取最新模式，UPSERT...
		if($rst){
			$rt['STS']='OK';
			$rt['rst']=$rst;
		}else{
			$rt['STS']='KO';
		}
		return $rt;
	}

	public function UpdateTestObj($param){
		$rt=array();
		$dsn=self::getDSN();
		$rt['dsn']=$dsn;//for debug
		$rt['act']='add';//for debug
		$rt['param']=$param;

		$orm_class = new OrmTester($dsn);
		$rst = $orm_class->update($param);//要从ACE取最新模式，UPSERT...
		if($rst){
			$rt['STS']='OK';
			$rt['rst']=$rst;
		}else{
			$rt['STS']='KO';
		}
		return $rt;
	}
}

