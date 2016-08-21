<?php
namespace CMP
{
	//based global function library from previous CMP version
	//@ref CmpCore::tryRegisterGlobalFunc()

	class LibBase
	{
		public static function str_starts_with($haystack, $needle) {
			return preg_match('/^'.preg_quote($needle,'/').'/', $haystack) > 0;
		}
		public static function stderrln($s){
			file_put_contents('php://stderr',$s."\n",FILE_APPEND);
		}
		public static function stderr($s){
			file_put_contents('php://stderr',$s,FILE_APPEND);
		}
		public static function o2s($o,$wellformat=false){
			if($wellformat){
				if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
					$s=json_encode($o,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
				}else{
					$s=json_encode($o);//will have {"a":"b"} instead of {a:"b"}, but encode speed might slightly inproved
					$s=preg_replace('/","/',"\",\n\"",$s);//dirty work for tmp...
				}
			}else{
				if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
					$s=json_encode($o,JSON_UNESCAPED_UNICODE);
				}else{
					$s=json_encode($o);//NOTES: official json_encode must result {"a":"b"} instead of {a:"b"}, but encode speed might slightly inproved
				}
			}
			return $s;
		}
		public static function s2o($s){
			$o=json_decode($s,true);//true->array, false->obj, NOTES that the json_decode not support {a:"b"} but only support {"a":"b"}. it sucks!!
			return $o;
		}
		public static function println($s,$wellformat=false){
			if(is_array($s) || is_object($s)){
				//$s=json_encode($s,$wellformat);
				$s=self::o2s($s,$wellformat);
			}
			print $s ."\n";//.PHP_EOL;
		}
		public static function gzip_output($buffer){
			$len = strlen($buffer);
			if(substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')){
				$gzbuffer = gzencode($buffer);
				$gzlen = strlen($gzbuffer);
				if ($len > $gzlen) {
					header("Content-Length: $gzlen");
					header("Content-Encoding: gzip");
					print $gzbuffer;
					return;
				}
			}
			header("Content-Length: $len");
			print $buffer;
			return;
		}

		public static function getbarcode($defaultLen=23,$seed='0123456789ABCDEF'){
			$code="";
			list($usec, $sec) = explode(" ", microtime());
			srand($sec + $usec * 100000);
			$len = strlen($seed) - 1;
			for ($i = 0; $i < $defaultLen; $i++) {
				$code .= substr($seed, rand(0, $len), 1);
			}
			return $code;
		}
		//for sae
		protected static function logger_sae($log_filename,$log_content,$prefix="",$gzf){
			$rt="";
			if(!defined('_LOG_')){ throw new Exception("//_LOG_ not defined to call logger"); }
			if($prefix=="DEFAULT") $prefix="--".date('ymd_His')."";

			$suffix="\n";//for all

			$rt=_LOG_ .'/'.$log_filename;
			//file_put_contents($rt, $prefix.$log_content.$suffix, FILE_APPEND);
			//http://sae4java.sinaapp.com/doc/com/sina/sae/storage/SaeStorage.html
			/*
			 * Q: 什么不用 Sae Storage?
			 * A: Storage服务适合用来存储用户上传的文件，比如头像、附件等。不适合存储代码类文件，比如页面内调用的JS、 CSS等，尤其不适合存储追加写的日志。使用Storage服务来保存JS、CSS或者日志，会严重影响页面响应速度。建议JS、 CSS直接保存到代码目录，日志使用sae_debug()方法记录。
			 */
			$mysql = new SaeMysql();
			//注：sae大小写敏感..
			//$sql = "INSERT INTO tbl_log_sys (name,value,time) values(".qstr($log_filename).",".qstr($prefix.$log_content.$suffix).",NOW())";
			$sql = "INSERT INTO tbl_log_sys (name,value,time) values(".qstr($log_filename).",'".$mysql->escape(substr($prefix.$log_content.$suffix,0,1024))."',NOW())";
			$mysql->runSql( $sql );
			if( $mysql->errno() != 0 ) {
				$errmsg="failed ($sql) ".$mysql->errmsg();
				//在系统log那里再写一下....以确保写log失败还是有东西能保存下来
				$prefix2=date('ymd_His');
				try{
					$sql =<<<EOSQL
CREATE TABLE `tbl_log_sys` (
`name` varchar(255) DEFAULT NULL,
`value` varchar(10240) DEFAULT NULL,
`time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
KEY `idx_name` (`name`),
KEY `idx_time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
EOSQL;
					$mysql->runSql( $sql );//写LOG
				}catch(Exception $ex){
					throw new Exception("ADMIN: Log table not found, you need to init a 'share database' in your app for the first time, if you don't know please check the document in the taskmgr system.");
				}
				//file_put_contents(_LOG_ .'/'."SYS-FAILED-$prefix2.log", $prefix.$log_content.$suffix."###".$errmsg); //DIRECTORY_SEPARATOR
			}else{
				$mysql->closeDb();
			}
			return $rt;
		}

		//logger for local
		public static function logger($log_filename,$log_content,$prefix="",$gzf){
			//TMP..dirty tricks
			if(defined('SAE_TMP_PATH')){
				return self::logger_sae($log_filename,$log_content,$prefix,$gzf);
			}
			
			$rt="";
			if(!defined('_LOG_')){ throw new Exception("//_LOG_ not defined to call logger"); }
			if($prefix=="DEFAULT"){
				list($sec, $usec) = explode(".", microtime(true));
				$prefix="--".date('ymd His').".$usec: ";
			}
			//$suffix="\r\n";//for windows
			$suffix="\n";//for all
			if($gzf){
				$rt=_LOG_ .'/'.$log_filename .'.gz';
				//$gz = gzopen($rt,'a');
				//gzwrite($gz, $prefix.$log_content.$suffix);
				//gzclose($gz);
				file_put_contents($rt, gzencode($prefix.$log_content.$suffix), FILE_APPEND);
			}else{
				$rt=_LOG_ .'/'.$log_filename;
				file_put_contents($rt, $prefix.$log_content.$suffix, FILE_APPEND);
			}
			return realpath($rt);
		}
		public static function quicklog_must($log_type=false,$log_content,$gz=false){
			//back to the old days that 'must-log'
			if(is_string($log_content)){
			}else{
				$log_content=var_export($log_content,true);
			}
			//if(is_object($log_content) || is_array($log_content)){
			//	$log_content=o2s($log_content);
			//}
			return self::logger($log_type."-".date('Ymd').".log",$log_content,"DEFAULT",$gz);
		}

		//Usage:
		//quicklog(false);//get current function debug or not
		//quicklog($logtype, $logtxt, $gz=true );//write $logtxt to _LOG_/$logtype-Ymd.log
		//quicklog($logtype, $logtxt, $gz=false, $write_stack=true);// write the $logtxt and whole process steps
		public static function quicklog($log_type=false,$log_content,$gz=false,$write_stack=false){
			$trace = debug_backtrace(false);
			$caller = $trace[1];
			$caller_class=$caller['class'];
			$caller_function=$caller['function'];
			$debug_a=self::getConf("debug_a");
			$debug2=self::getConf("$caller_class.$caller_function",array("debug_a"),false);
			if($debug2){
				$debug=$debug2;
			}else{
				$debug1=self::getConf($caller_class,array("debug_a"),false);
				if($debug1){
					$debug=$debug1;
				}else{
					$debug0=self::getConf("*",array("debug_a"),false);
					$debug=$debug0;
				}
			}
			if($log_type===false) return $debug;//if call for get debug level only
			if($write_stack){
				unset($trace[0]);
				$_s="";
				foreach($trace as $t)
				{
					$_s.= "\t" . '@ ';
					if(isset($t['file'])) $_s.= basename($t['file']) . ':' . $t['line'];
					else
					{
						$_s.= '<PHP inner-code>';
					}

					$_s.= ' -- ';

					if(isset($t['class'])) $_s.= $t['class'] . $t['type'];

					$_s.= $t['function'];

					if(isset($t['args']) && sizeof($t['args']) > 0) $_s.= '(...)';
					else $_s.= '()';

					$_s.= "\n";
				}
				$log_content.="\n"."[STACK]".$_s;
			}
			if($debug>0){
				if(is_string($log_type)){
					if(is_object($log_content) || is_array($log_content)){
						$log_content=self::o2s($log_content);
					}
					return self::logger($log_type."-".date('Ymd').".log",$log_content,"DEFAULT",$gz);
				}
			}
			if(is_object($log_type)){
				//throw new Exception("TODO.quicklog().if log_type is object, check it's ['debug'], and then do logging");
				throw new Exception("Unknonw log_type is object");
			}
		}

		#============================================================================================ config
		public static function getXlsArr($nameXls){
			global $_tm_, $_g_probe_time;
			if($_g_probe_time>2) $_tm_[]=array("before getXlsArrFile",microtime(true));
			$file=getXlsArrFile($nameXls);
			if($_g_probe_time>2) $_tm_[]=array("after getXlsArrFile",microtime(true));
			require($file);
			if($_g_probe_time>2) $_tm_[]=array("after require $file",microtime(true));
			$rt=$getXlsArrFile_rt;//约定.
			return $rt;
		}

		public static function xls_zip_extract_tmp($zip_file,$extract_to_file){
			$zip = zip_open($zip_file);
			if ($zip) {
				while ($zip_entry = zip_read($zip)) {
					$fp = fopen(
						//_TMP_ ."/".zip_entry_name($zip_entry)
						$extract_to_file
						, "w");
					if (zip_entry_open($zip, $zip_entry, "r")) {
						$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
						fwrite($fp,"$buf");
						zip_entry_close($zip_entry);
						fclose($fp);
					}
				}
				zip_close($zip);
			}
		}
		public static function getXlsArrFile($nameXls){
			if(!$nameXls) throw new Exception("KO-404-param-nameXls");

			$xls_file=_APP_DIR_.DIRECTORY_SEPARATOR.$nameXls;
			$filemtime=filemtime($xls_file);

			if(!$filemtime){
				//2015-3-13 没有的话试试zip
				$nameXlsZip=$nameXls.".zip";
				$xls_zip_file=_APP_DIR_.DIRECTORY_SEPARATOR.$nameXlsZip;
				$filemtime=filemtime($xls_zip_file);
				if(!$filemtime){ throw new Exception("KO-404-".$nameXlsZip); }

				//$xls_file=_TMP_ ."/$filemtime.".basename($nameXls);
				$xls_file=_TMP_ ."/$filemtime.".basename($nameXls);
				$xls_file_mtime=filemtime($xls_file);
				if(!$xls_file_mtime){
					xls_zip_extract_tmp($xls_zip_file,$xls_file);
					$xls_file_mtime=filemtime($xls_file);
				}
				if(!$xls_file_mtime){
					throw new Exception("Fail Extract $nameXlsZip");
				}
			}
			if(!$filemtime){ throw new Exception("KO-404-".$nameXls); }

			$target_cache_file=_TMP_."/".basename($xls_file).".".$filemtime.".php.cache";
			$cache_file_mtime=filemtime($target_cache_file);
			if(!$cache_file_mtime){
				//gen cache file
				require_once _LIB_."/faisalman-simple-excel-php-9bcff4b/src/SimpleExcel/SimpleExcel.php";
				$excel = new SimpleExcel\SimpleExcel('xml');
				$excel->parser->loadFile("$xls_file");
				$csv_a=$excel->parser->getField();

				$csv_a_1st=array_shift($csv_a);
				array_shift($csv_a);//第二行忽略...（约定）.
				array_shift($csv_a_1st);//不要第一行的第一个，其它为key
				$key_a=array();
				$_full_a=array();
				foreach($csv_a as $k=>$v){
					$key_a[]=$v[0];
					foreach($csv_a_1st as $kk=>$vv){
						if(!$_full_a[$vv])$_full_a[$vv]=array();
						$_full_a[$vv][$v[0]]=$v[$kk+1];
					}
				}
				$_full_a['KEYS']=$key_a;
				$full_s=var_export($_full_a,true);
				$full_s=str_replace("&#38;","&",$full_s);
				file_put_contents($target_cache_file,"<"."?php\n\$getXlsArrFile_rt=$full_s;");
				$cache_file_mtime=filemtime($target_cache_file);
				if(!$cache_file_mtime) throw new Exception("KO FOR COMPILE pack");
			}
			return $target_cache_file;
		}

		//变体：返回以行 为数组的结果, 暂时使用到的地方有 AceTool、ApiTopup
		public static function getXlsArr2($nameXls){
			$file=self::getXlsArrFile2($nameXls);
			require($file);
			$rt=$getXlsArrFile_rt;//约定.
			return $rt;
		}

		public static function getXlsArrFile2($nameXls){
			if(!$nameXls) throw new Exception("KO-404-param-nameXls");

			$xls_file=_APP_DIR_.DIRECTORY_SEPARATOR.$nameXls;
			$filemtime=filemtime($xls_file);
			if(!$filemtime){
				//copy by zhb 2015-3-13 没有的话试试zip
				$nameXlsZip=$nameXls.".zip";
				$xls_zip_file=_APP_DIR_.DIRECTORY_SEPARATOR.$nameXlsZip;
				$filemtime=filemtime($xls_zip_file);
				if(!$filemtime){ throw new Exception("KO-404-".$nameXlsZip); }

				//$xls_file=_TMP_ ."/$filemtime.".basename($nameXls);
				$xls_file=_TMP_ ."/$filemtime.".basename($nameXls);
				$xls_file_mtime=filemtime($xls_file);
				if(!$xls_file_mtime){
					xls_zip_extract_tmp($xls_zip_file,$xls_file);
					$xls_file_mtime=filemtime($xls_file);
				}
				if(!$xls_file_mtime){
					throw new Exception("Fail Extract $nameXlsZip");
				}
			}
			if(!$filemtime){ throw new Exception("KO-404-".$nameXls); }

			$target_cache_file=_TMP_."/".basename($xls_file).".".$filemtime.".php.cache";
			$cache_file_mtime=filemtime($target_cache_file);
			if(!$cache_file_mtime){
				//gen cache file
				require_once _LIB_."/faisalman-simple-excel-php-9bcff4b/src/SimpleExcel/SimpleExcel.php";
				$excel = new SimpleExcel\SimpleExcel('xml');
				$excel->parser->loadFile("$xls_file");
				$csv_a=$excel->parser->getField();

				$csv_a_1st=array_shift($csv_a);
				array_shift($csv_a);//第二行忽略...（约定）.
				//这个函数最大的修改是下面的逻辑：
				$_full_a=array();
				foreach($csv_a as $k=>$v){
					$_singleRow = array();
					foreach($csv_a_1st as $kk=>$vv){
						$_singleRow[$vv] = $v[$kk];
					}
					$_full_a[] = $_singleRow;
				}
				$full_s=var_export($_full_a,true);
				$full_s=str_replace("&#38;","&",$full_s);
				file_put_contents($target_cache_file,"<"."?php\n\$getXlsArrFile_rt=$full_s;");
				$cache_file_mtime=filemtime($target_cache_file);
				if(!$cache_file_mtime) throw new Exception("KO FOR COMPILE pack");
			}
			return $target_cache_file;
		}

		public static function getLang_a($lang){
			static $lang_a=null;
			if($lang_a) return $lang_a[$lang];//静态有的话就用静态的.
			if(!$lang)$lang=$_SESSION['lang'];
			if(!$lang)$lang=getConf("default_lang");

			$lang_pack_conf=getConf("lang_pack_conf");//注意是相对目录
			$lang_a=$a=getXlsArr($lang_pack_conf);
			return $a[$lang];
		}

		public static function calcLangFromBrowser(){
			preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
			$lang = strtolower($matches[1]);
			if(!$lang) $lang='en';
			return $lang;
		}

		public function getLang($k,$lang=null){
			if(!$k) throw new Exception("KO: getLang(null) is not supported");
			static $lang_static=null;//初值为空
			static $lang_static_en=null;//初值为空
			if(!$lang){
				if($lang_static) $lang=$lang_static;//静态已经有，直接用静态的.
				else{
					if(!$lang)$lang=$_REQUEST['lang'];
					if(!$lang)$lang=$_SESSION['lang'];
					if(!$lang)$lang=$_COOKIE['lang'];
					if(!$lang)$lang="en";//实在不行才用en做为保底...
					$lang_static=$lang;//保存到静态给下一个用.
				}
			}
			$lang_a=getLang_a($lang);
			if(in_array($lang,array("type","column","remark","uidefault"))){
				//special...
				$rt=$lang_a[$k];
			}else{
				if($lang_static_en) $lang_a_en=$lang_static_en;
				else $lang_static_en=$lang_a_en=getLang_a("en");

				if(!$lang_a) $lang_a=$lang_a_en;
				$rt=$lang_a[$k];
				if(!$rt) $rt=$lang_a_en[$k];
				if(!$rt) $rt="I18N_".$k;
			}
			return $rt;
		}

		#============================================================================================ config
		public static function &getConf($key,$path=array(),$mandate_flag=false,$setConf=0,$setValue=null){
		if(!defined('_APP_DIR_')) throw new Exception('_APP_DIR_ not defined for getConf');
		static $_conf_=null;
		if(!$_conf_){
			$_switch_conf="";
			require(_APP_DIR_ ."/config.switch.php");
			if($_switch_conf=="") throw new Exception("ConfigError: config.switch.php not found?? ($_switch_conf)");

			//require "inc.commonconf.php";//_conf_all_common_
			require _APP_DIR_."/_conf/inc.commonconf.php";//_conf_all_common_
			if(!$_conf_all_common_) throw new Exception("ConfigError: not found _conf_all_common_");

			//$dir_switch_conf=$_conf_all_common_['dir_switch_conf'];
			//if(!$dir_switch_conf) throw new Exception("ConfigError: not found $_switch_conf.dir_switch_conf");

			$conf_file=(realpath(_APP_DIR_."/_conf.$_switch_conf/$_switch_conf.php"));
			if(!$conf_file) throw new Exception("ConfigError: $_switch_conf not found");
			require $conf_file;

			//if($mandate_flag && $_conf_all_[$_switch_conf]){} else {
			//	throw new Exception("ConfigError: getConf failed \$_conf_all_[$_switch_conf]");
			//}

			if(! $_conf_all_[$_switch_conf] )
				throw new Exception("ConfigError: getConf failed \$_conf_all_[$_switch_conf]");

			$_conf_=($_conf_all_[$_switch_conf]);
		}

		$rt=& $_conf_;
		$errmsg="ConfigError: getConf".join('/',$path)."/$key failed";
		if($key){
			foreach($path as $_k){
				if(!array_key_exists($_k,$rt)){
					if($setConf==1){
						$rt[$_k]=array();
					}elseif($mandate_flag){
						throw new Exception($errmsg);
					}
				}
				$rt=& $rt[$_k];
			}
			if($setConf==1){ //1=save
				$rt[$key]=$setValue;
			}elseif($setConf==2){//2=remove
				unset($rt[$key]);
			}
			if(array_key_exists($key,$rt)){
			}else{
				if($mandate_flag){
					throw new Exception($errmsg);
				}
				if($setConf==1){ //1=save
					$rt[$key]=$setValue;
				}elseif($setConf==2){//2=remove
					unset($rt[$key]);
				}
			}
			//$rt=$val=$rt[$key];
			$rt= & $rt[$key];
		}else{
			throw new Exception("getConf need param key");
		}
		return $rt;
		}
		public static function setConf($key,$val,$path=array()){
			#throw new Exception("setConf() to be rewritten as V5Conf::set()");
			return self::getConf($key,$path,false,1,$val);
		}
		public static function removeConf($key,$val,$path=array()){
			#throw new Exception("removeConf() to be rewritten as V5Conf::remove()");
			return self::getConf($key,$path,false,2);
		}
		public static function saveConf($key,$path=array(),$filename=""){//save to file
			#throw new Exception("saveConf() to be rewritten as V5Conf::save()");
		}
	}//class LibBase
}//namespace
