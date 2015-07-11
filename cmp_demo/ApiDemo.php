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
}
