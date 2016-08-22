<?php
#throw new Exception("Must manually edit config.switch.php");
#$SAE=defined('SAE_TMP_PATH') && !$argv[0];//dirty tricks
$SAE=defined('SAE_TMP_PATH');
if($SAE){
	$_switch_conf="dev_sae";//Using SAE config on SAE Env
}else{
	//NOTES: 不要提交任何的 .tmp文件到 代码库.
	if(file_exists(__DIR__."/config.switch.override.tmp"))
		require(__DIR__."/config.switch.override.tmp");
	else{
		#print "404 config.switch.override.tmp";//如果有错就会知道应该把 .tmp.example复制修改好!!
		#die;
	}
}

