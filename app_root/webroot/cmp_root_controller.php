<?php
#V20160728
(function($uu){
if($uu==''){require 'index_default.php';return;}
foreach(
	array(
		"/([^\/]*)\.([^\.]*)\.api$/"=>function(&$uu,$pattern,$matches){
			$_c=$_REQUEST['_c']=$_GET['_c']=$matches[1];
			$_m=$_REQUEST['_m']=$_GET['_m']=$matches[2];
			$uu=dirname($uu).'/index.php';
		},
		"/\.static$/"=>function(&$uu,$pattern,$matches){
			$_c=$_REQUEST['_c']=$_GET['_c']=$matches[1];
			$_m=$_REQUEST['_m']=$_GET['_m']=$matches[2];
			$uu=dirname($uu).'/static.php';
		},
		"/\.php$/"=>function($uu,$pattern){
			if(file_exists($uu)){
				chdir(dirname($uu));
				require $uu;
				return true;
			}
		},
		"/\/$/"=>function($uu){
			if(file_exists($uu .'index.php')){
				chdir($uu);
				require $uu.'index.php';
				return true;
			}
		},
		"/\.(js|css|jpg|jpeg|png|gif)$/"=>function($uu,$pattern){
			if(file_exists($uu)){
				echo file_get_contents($uu);
				return true;
			}
		},
		''=>function($uu){
			print "404 $uu";
		}
) as $k=>$v){
	$matches=array();
	if(preg_match($k,$uu,$matches) || $k==''){
		if(true===$v($uu,$k,$matches)) break; else continue;
	}
}
})(ltrim($_SERVER['REQUEST_URI'] | $_SERVER['PATH_INFO'],'/'));
