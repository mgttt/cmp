<?php
(function(){
$REQUEST_URI=$_SERVER['REQUEST_URI'];
$PATH_INFO=$_SERVER['PATH_INFO'];
$proxy_url = $PATH_INFO or $REQUEST_URI;
$proxy_url = ltrim($proxy_url,'/');
foreach(
	array(
		//TODO skip _logs? _tmp?

		"/([^\/]*)\.([^\.]*)\.api$/"=>function(&$proxy_url,$pattern,$matches){
			//handle cmp elegant mode
			$_c=$_REQUEST['_c']=$_GET['_c']=$matches[1];
			$_m=$_REQUEST['_m']=$_GET['_m']=$matches[2];
			$proxy_url=dirname($proxy_url).'/index.php';
			return true;//true to continue next rule
		},
		"/\.static$/"=>function(&$proxy_url,$pattern,$matches){
			//handle cmp fake static mode
			$_c=$_REQUEST['_c']=$_GET['_c']=$matches[1];
			$_m=$_REQUEST['_m']=$_GET['_m']=$matches[2];
			$proxy_url=dirname($proxy_url).'/static.php';
			return true;//true to continue next rule
		},
		"/\.php$/"=>function($u,$pattern){
			if(file_exists($u)){
				chdir(dirname($u));//change working dir to the folder of the file
				require $u;
			}else{
				print "404 $u";
			}
		},
		//default index.php if $folder/
		"/\/$/"=>function($u){
			if(file_exists($u .'index.php')){
				chdir($u);
				require $u.'index.php';
			}else{
				print "404 $u";
			}
		},
		//the reaul static file such js/css/png/jpg/gif
		"/\.(js|css|jpg|jpeg|png|gif)$/"=>function($u,$pattern){
			if(file_exists($u)){
				echo file_get_contents($u);
			}else{
				print "404 $u";
			}
		},
		//other regards as 404
		"unknown"=>function($u,$pattern){
			print "404 $pattern $u";
		}
) as $k=>$v)
{
	$matches=array();
	if(preg_match($k,$proxy_url,$matches) || $k=='unknown'){
		//$v($proxy_url,$k);
		if(true===$v($proxy_url,$k,$matches)){
			//continue next if return true from function...
			continue;
		}
		break;
	}
}
})();

