<?php
class ApiDemo
	extends WebCore //at cmp_ext
{
	public function PingPong($param){
		$ping = $param['ping'];
		$pong = microtime(true);
		$rt = array(
			"STS" => "OK",
			"ping" => $ping,
			"pong" => $pong,
			"diff" => $pong - $ping,
		);

		$_s=session_id();
		if ($_s){
			$rt['_s'] = $_s;
		}
		return $rt;
	}
	//{ "errmsg": "Maximum execution time of 30 seconds exceeded", "errno": 1, "line": 24, "module": "ApiDemo" }
	public function TestBlackHole1($param){
		sleep(31);
	}
	public function TestBlackHole2($param){
	}
	public function SaveTestNote($param){
		if($param['id']){
			return self::UpdateTestNote($param);
		}else{
			return self::AddTestNote($param);
		}
	}
	public function AddTestNote($param){
		//TODO 要check必要的输入

		$param['note_status'] = "0";
		//访问orm层
		$cls = new OrmDemo(LgcDemo::getDSN());
		$info = $cls->newOrm($param);
		if($info){
			return array("STS"=>"OK","data"=>$info);
		}
		return array("STS"=>"KO");
	}
	public function UpdateTestNote($param){
		//TODO 要check必要的输入

		//访问orm层
		$cls = new OrmDemo(LgcDemo::getDSN());
		$info = $cls->updateOrm($param['id'],$param);
		if($info){
			return array("STS"=>"OK","data"=>$info);
		}
		return array("STS"=>"KO");
	}

	public function DeleteTestNote($param){
		//访问orm层
		$cls = new OrmDemo(LgcDemo::getDSN());
		$cls->deleteBean($param['id']);
		return array("STS"=>"OK");
	}
	public function ListTestNote($param){
		$cls = new LgcDemo();
		return $cls->ListTestNote($param);
	}
}
