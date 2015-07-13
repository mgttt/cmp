<?php
$_conf_all_[$_switch_conf]=$_conf_all_common_;//don't delete this one...

#$_conf_all_[$_switch_conf]["CONFIG_TYPE"]="CMP_DEMO";

#switch debug level
#$_conf_all_[$_switch_conf]["debug_a"]=array(
#	"*"=>1,//1 to open debug all for quicklog(); but quicklog_must() bypass this check
#);

$_conf_all_[$_switch_conf]["flag_rb_freeze"]=false;//在DEMO不为FREEZE表结构，这样在首次运行都能运行.

#在代码中
#setConf("flag_rb_freeze",false);//DEMO不需要freeze

