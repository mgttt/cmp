<?php
$_conf_all_[$_switch_conf]=$_conf_all_common_;//don't delete this one...

#$_conf_all_[$_switch_conf]["CONFIG_TYPE"]="CMP_DEMO";

#for LIVE
#$_conf_all_[$_switch_conf]["debug_a"]=array(
#	"*"=>1,//1 to open debug all for quicklog(); but quicklog_must() bypass this check
#);

//go saas core to get the saas tenant config info

$SCRIPT_NAME=$_SERVER['SCRIPT_NAME'];
$PATH=dirname($SCRIPT_NAME);
$SERVER_PORT=$_SERVER['SERVER_PORT'];
$SERVER_PORT=$SERVER_PORT?:"80";

