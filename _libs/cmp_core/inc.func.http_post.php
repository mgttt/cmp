<?php
function http_post($url, $postdata, $timeout=14){
	if(is_array($postdata)){
		$postdata_s=http_build_query($postdata);
	}else/*if(is_string($postdata))*/{
		$postdata_s=$postdata;
	}
	$data_len = strlen($postdata_s);

	$context=stream_context_create(array('http'=>array(
		'method'=>'POST',
		'timeout'=>$timeout,
		//"header"  => "User-agent: Myagent", 
		//"proxy"   => "tcp://my-proxy.localnet:3128", 
		//'request_fulluri' => True //some proxy need full uri
		'header'=>"Connection: close\r\nContent-Length: $data_len\r\n",
		'content'=>$postdata_s,
	)));
	$rt=file_get_contents($url, false, $context);
	return $rt;
}
