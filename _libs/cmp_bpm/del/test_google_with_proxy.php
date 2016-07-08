<?php
require_once "../inc.app.php";

//while(true){
try{
	//the entry to check the bpe status.
	$url_api_check_bpe=getConf("url_api_check_bpe");
	//$url_api_check_bpe="http://www.baidu.com/";
	if(!$url_api_check_bpe) throw new Exception("please check config for url_api_check_bpe");
	echo "url_api_check_bpe=$url_api_check_bpe\n";

	//TODO 访问远程。
	$DIRECTORY_SEPARATOR=DIRECTORY_SEPARATOR;
	$LIB=_LIB_;
	//require "$LIB{$DIRECTORY_SEPARATOR}cmp_core{$DIRECTORY_SEPARATOR}inc.func.http_post.php";
	//echo http_get($url_api_check_bpe);
	//echo http_post($url_api_check_bpe);
	require_once "$LIB{$DIRECTORY_SEPARATOR}cmp_core{$DIRECTORY_SEPARATOR}inc.func.http_req.php";

	//echo http_req(array( 'url'=>$url_api_check_bpe ));

	$url="http://www.google.com/";

	//KO 用 stream_context_create 不支持socks5，估计只支持简单http_proxy
	//TODO 要用SOCKS5就只能用 CURL库，要另外处理那个inc.func.web_request.php头文件了
	echo http_req2([ 'url'=>$url,
		'data'=>([]),
		'proxy'=>"tcp://127.0.0.1:9329",
	]);
}catch(Exception $ex){
	//BPE::dump($ex);
	print $ex;
}
sleep(1);//rest one second after one loop
//}
