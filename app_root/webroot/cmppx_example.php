<?php
/* vim: set tabstop=2 shiftwidth=2 softtabstop=2: */
error_reporting(E_ERROR|E_COMPILE_ERROR|E_PARSE|E_CORE_ERROR|E_USER_ERROR);

//(function(){

$REQUEST_URI=$_SERVER['REQUEST_URI'];
$PATH_INFO=$_SERVER['PATH_INFO'];

$proxy_url = $PATH_INFO or $REQUEST_URI;

//e.g.
//http://localhost:9888/https__/devace.sinaapp.com/saas_ace/login.shtml
//http://localhost:9888/devace/saas_ace/login.shtml
$proxy_url=preg_replace("/^[\/]?devace\//","http__/devace.sinaapp.com/",$proxy_url);

$proxy_url=str_replace("index.php/","",$proxy_url);//tmp solution to remove the leading index.php/

$proxy_url = ltrim($proxy_url,'/');
if($proxy_url){
	$proxy_url=str_replace("http__/","http://",$proxy_url);
	$proxy_url=str_replace("https__/","https://",$proxy_url);

	$_http_host_a=parse_url($proxy_url);
	$final_scheme=$_http_host_a['scheme'];
	$final_host=($_http_host_a['host'])?$_http_host_a["host"]:$host;

	if($final_scheme && $final_host){
		//PROXY MODE (not yet complete testing _FILES...TODO...)

		$final_port=$_http_host_a["port"];
		$final_path=$_http_host_a["path"];

		if($final_port==""){
			if($final_scheme=='http'){
				$final_port=80;
			}elseif($final_scheme=='https'){
				$final_port=443;
			}
		}

		//if(in_array($final_host,array(
		//	"devace.sinaapp.com",
		//	"devace.vipsinaapp.com",
		//	"devace.applinzi.com",
		//))){
		//}else{
		//	print '{"errmsg":"not allow pxu '.$final_host.'"}';die;
		//}

		$QUERY_STRING=$_SERVER['QUERY_STRING'];
		if ($QUERY_STRING!=='') {
			$final_path .= "?$QUERY_STRING";
		}

		require_once 'cmppx.php';
		$px=new cmppx;

		$px->forward($final_path, $final_host, $final_port, $final_scheme);

		flush();die;
	}else{
		require_once 'cmp_root_controller.php';
		//if('_pi_.php'==$proxy_url){
		//	print filemtime('index.php').' '.date('YmdHis')."<br/>";
		//	phpinfo();
		//}else{
		//	print "TODO $proxy_url<br/>";
		//	print "REQUEST_URI=$REQUEST_URI<br/>";
		//	print "PATH_INFO=$PATH_INFO<br/>";
		//}
	}
} else {
	//require 'index_default.php';
	////print "Request Error";
	//print date('YmdHis');
	//print rand();"<hr/>";
	//$last_line=passthru('ls -al');
	//print "last_line=$last_line<br/>";

	print 'cmp HTTP_VERSION_CMP_APP_SERVER='.$_SERVER['HTTP_VERSION_CMP_APP_SERVER'].'<br/>';
	#phpinfo();
}
//die;
//})();
