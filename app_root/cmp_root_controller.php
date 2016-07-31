<?php
error_reporting(E_ERROR|E_COMPILE_ERROR|E_PARSE|E_CORE_ERROR|E_USER_ERROR);
#WARNING:  this controller are for referece only.
#V20160801
(function($webroot,$uu){
	if($uu==''){
		//chdir('webroot/');require 'index.php';return;
		require $webroot.'index.php';return;
		//echo getcwd();return;
	}
	//just a example how to do reverse-proxy:
	if(preg_match("/^px(\.php)?\//",$uu)){
		//special handling
		if(file_exists('px.php')){
			require 'px.php';
		}else{
			print "404 $uu";
		}
		return;
	}
	$uu=$webroot.$uu;
foreach(
	array(
		"/(,?)([^\/,]*)[\.|,]([^\.]*),?(.*)\.(api|static|web|json)$/"=>function(&$uu,$pattern,$matches){
			$_c=$_REQUEST['_c']=$_GET['_c']=$matches[2];
			$_m=$_REQUEST['_m']=$_GET['_m']=$matches[3];
			$uu=dirname($uu).'/'.($matches[5]=='static'?'static':'index').'.php';
		},

		//patch for ACE{:
		"/([^\/]*)\.shtml$/"=>function(&$uu,$pattern,$matches){
			$_REQUEST['_p']=$_GET['_p']=$matches[1];
			if(file_exists(dirname($uu).'/shtml.php')){
				chdir(dirname($uu));
				require dirname($uu).'/shtml.php';
				return true;
			}
		},
		"/([^\/]*)\.api$/"=>function(&$uu,$pattern,$matches){
			$_REQUEST['_m']=$_GET['_m']=$matches[1];
			$uu=dirname($uu).'/index.php';
		},
		"/([^\/]*)\.static$/"=>function(&$uu,$pattern,$matches){
			$_REQUEST['_m']=$_GET['_m']=$matches[1];
			$uu=dirname($uu).'/static.php';
		},
		//TODO ./upload/ mapping to ..
		//patch for ACE}:

		"/\/$/"=>function($uu){
			if(file_exists($uu .'index.php')){
				chdir($uu);
				require $uu.'index.php';
				return true;
			}
		},
		"/\.php$/"=>function($uu,$pattern){
			if(file_exists($uu)){
				chdir(dirname($uu));
				require basename($uu);
				return true;
			}else{
				echo "404 ".getcwd()."$uu ...";return true;
			}
		},
		"/\.(js|css|jpg|jpeg|png|gif|ttf)$/"=>function($uu,$pattern){
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
	if($k=='' || preg_match($k,$uu,$matches)){
		if(true===$v($uu,$k,$matches)) break; else continue;
	}
}
})('webroot/',ltrim($_SERVER['REQUEST_URI'] | $_SERVER['PATH_INFO'],'/'));
