<?php
function qstr2($s){
	//just replace ' to ''
	return str_replace("'","''",$s);
}

function qstr($s){
	$x="'".str_replace("'","''",$s) ."'";
	return $x;
}

function qstr_arr($_a){
	$_a=explode(",",$_a);
	foreach($_a as $k=>$v){
		$_a[$k]=qstr($v);
	}
	return join(',',$_a);
}
