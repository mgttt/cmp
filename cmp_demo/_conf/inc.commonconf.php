<?php
$_conf_all_common_=array(
	//@ref quicklog()
	"debug_a"=>array(
		"*"=>0,//for quicklog debug level, default 0 means no debug
	),
	"class_path_a"=>array(
		_APP_DIR_."/../_inc",
		//_APP_DIR_."/../_inc_saas",
		_LIB_."/cmp_ext",
	),
	"dz"=>array(
		'template_dir'=> _APP_DIR_,//模板所在目录的根目录....如果不对就要自己override这个值...
		'compile_dir' => _TMP_,#auto create by DzTempalte
		'force_compile' => true,
	),
	"cache_lite"=>array(
		'dir_name' => 'lib.Cache_Lite-1.7.2',
		'cache_dir' => _TMP_ ."/",
	),
	'lang_pack_conf'=>'_lang/lang_pack.xls',//related it is a XML :P
	'lang_support'=>array(
		"en"=>"en",
		"en-us"=>"en",
		"en-uk"=>"en",
		"zh-cn"=>"zh-cn",
		"zh-tw"=>"zh-tw",
		"zh-hk"=>"zh-tw",//hk share tw
		"th"=>"th",
		"kh"=>"kh",
		"vn"=>"vn",
	),
	'default_lang'=>'en',
	"SERVER_TIMEZONE"=>"Asia/Phnom_Penh",//for adjust_timezone()
	//"SERVER_TIMEZONE"=>"Etc/GMT-8",//default timezone Bejing Time
	//"SERVER_TIMEZONE"=>"Etc/GMT+5",//EST Eastern Standard Time
	//"SERVER_TIMEZONE"=>"Etc/GMT+8",//PSC Pacific Stabard Time
	"db_timezone"=>"+7:00",//for rbWrapper3
);
$_conf_all_common_['flag_rb_freeze']=TRUE;
