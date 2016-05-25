<?php
//简单的 get/post 函数.
function web_request($url,$postdata,$timeout=7){
	//$postdata_s=null;
	if(is_array($postdata)){
		$postdata_s=http_build_query($postdata);
	}elseif(is_string($postdata)){
		$postdata_s=$postdata;
	}
	$url_a=parse_url($url);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	if($url_a['scheme']=="https"){
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	}
	if($postdata_s){
		curl_setopt($ch, CURLOPT_POST, true);
		//curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata_s);
	}
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	if($timeout>0 && $timeout<1){
		curl_setopt($ch, CURLOPT_NOSIGNAL,1);//毫秒级需要..
		curl_setopt($ch, CURLOPT_TIMEOUT_MS,200);//超时毫秒，cURL 7.16.2中被加入。从PHP 5.2.3起可使用
	}elseif($timeout>=1){
		curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);
	}
	$result = curl_exec($curl);
	curl_close($curl);
	$errno=curl_errno($curl);
	if($errno){
		throw new Exception(curl_error($curl),$errno);
	}
	return $result;
}

