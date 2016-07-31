<?php
error_reporting(E_ERROR|E_COMPILE_ERROR|E_PARSE|E_CORE_ERROR|E_USER_ERROR);
/* vim: set tabstop=2 shiftwidth=2 softtabstop=2: */

//WARNING: this file is a demo only for reverse-proxy usage

$proxy_url = $_SERVER['PATH_INFO'] or $_SERVER['REQUEST_URI'];

//$proxy_url=preg_replace("/^[\/]?ggg\//","http__/www.google.com/",$proxy_url);

$proxy_url=str_replace("px.php/","",$proxy_url);//tmp solution to remove the leading px.php/
$proxy_url=str_replace("px/","",$proxy_url);//tmp solution to remove the leading px/

$proxy_url = ltrim($proxy_url,'/');
if($proxy_url){
	$proxy_url=str_replace("http__/","http://",$proxy_url);//SICK HACK
	$proxy_url=str_replace("https__/","https://",$proxy_url);//SICK HACK

	$_http_host_a=parse_url($proxy_url);
	$final_scheme=$_http_host_a['scheme'];
	$final_host=($_http_host_a['host'])?$_http_host_a["host"]:$host;
	$final_port=$_http_host_a["port"];
	$final_path=$_http_host_a["path"];

	if($final_scheme && $final_host){
		$mirror=$final_host;
		if($final_port==""){
			if($final_scheme=='http'){
				$final_port=80;
			}elseif($final_scheme=='https'){
				$final_port=443;
			}
		}
		//if(in_array($final_host,array(
		//	//"120.55.73.8",
		//	"acedemo.sinaapp.com",
		//))){
		//}else{
		//	print '{"errmsg":"not allow pxu '.$final_host.'"}';die;
		//}

		$QUERY_STRING=$_SERVER['QUERY_STRING'];
		if ($QUERY_STRING!=='') {
			$final_path .= "?$QUERY_STRING";
		}

		require("cmppx.php");
		$px=new cmppx;

		//echo "$px->forward($final_path, $final_host, $final_port, $final_scheme);";
		$px->forward($final_path, $final_host, $final_port, $final_scheme);

		flush();
	}else{
		print "TODO $proxy_url<br/>";
		#print 'REQUEST_URI='.$_SERVER['REQUEST_URI'].'<br/>';
		#print 'PATH_INFO='.$_SERVER['PATH_INFO'].'<br/>';
		//var_dump($_SERVER);
		print "Please read documentation";
	}
} else {
	print 'px: HTTP_VERSION_CMP_APP_SERVER='.$_SERVER['HTTP_VERSION_CMP_APP_SERVER'].'<br/>';
}
//die;

