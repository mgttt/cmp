<?php
if(preg_match("/([^\/]*)\.([^\.]*)\.api$/",$proxy_url,$matches)){
	$_c=$_REQUEST['_c']=$_GET['_c']=$matches[1];
	$_m=$_REQUEST['_m']=$_GET['_m']=$matches[2];
	$proxy_url=dirname($proxy_url).'/index.php';
}elseif(preg_match("/\.static$/",$proxy_url,$matches)){
	$_c=$_REQUEST['_c']=$_GET['_c']=$matches[1];
	$_m=$_REQUEST['_m']=$_GET['_m']=$matches[2];
	$proxy_url=dirname($proxy_url).'/static.php';
}
foreach(
	array(
		"/\.php$/"=>function($u,$pattern){
			if(file_exists($u)){
				chdir(dirname($u));//change working dir
				require $u;
			}else{
				//TODO header 404
				print "404 $u";
			}
		},
		"/\/$/"=>function($u){
			if(file_exists($u .'index.php')){
				chdir($u);
				require $u.'index.php';
			}else{
				//TODO header 404
				print "404 $u";
			}
		},
		"/\.(js|css)$/"=>function($u,$pattern){
			if(file_exists($u)){
				echo file_get_contents($u);
			}else{
				//TODO header 404
				print "404 $u";
			}
		},
		//handler for undefined...
		"unknown"=>function($u,$pattern){
			#global $REQUEST_URI,$PATH_INFO;
				//TODO header 404
			print "404 $pattern $u";
			#print "REQUEST_URI=$REQUEST_URI<br/>";
			#print "PATH_INFO=$PATH_INFO<br/>";
		}
) as $k=>$v)
		{
			if(preg_match($k,$proxy_url) || $k=='unknown'){
				$v($proxy_url,$k);
				//if(true===$v($proxy_url,$k)){
				//	continue;
				//}
				break;
			}
		};
