<?php
//20161001
//The core layer where CMP starts with, for detail please see http://cmptech.info/
namespace CMP
{
	error_reporting(E_ERROR | E_COMPILE_ERROR | E_PARSE | E_CORE_ERROR | E_USER_ERROR);
	if (!function_exists('spl_autoload_register')) {
		throw new \Exception("\\CMP\\ needs spl_autoload_register()");
	}
	//if (version_compare(PHP_VERSION, '5.4.0') < 0) {
	//	throw new \Exception("\\CMP\\ now only support php5.4+");
	//}
	class LibCore
	{
		public static function o2s($o, $wellformat = false)
		{
			if ($wellformat) {
				$s = json_encode($o, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
			} else {
				$s = json_encode($o, JSON_UNESCAPED_UNICODE);
			}
			return $s;
		}

		public static function s2o($s)
		{
			$o = json_decode($s, true);//true->array, false->obj
			//NOTES: json_decode not support {a:"b"} but only support {"a":"b"}.
			return $o;
		}

		//the mini curl wrapper function.  for complex usage, another bigger class is needed.
		public function web($url, $postdata, $timeout = 14)
		{
			if (is_array($postdata)) {
				$postdata_s = http_build_query($postdata);
			} elseif (is_string($postdata)) {
				$postdata_s = $postdata;
			} elseif ($postdata) {
				throw new \Exception("web() unknown postdata=" . self::o2s($postdata));
			}
			$url_a = parse_url($url);
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			if ($url_a['scheme'] == "https") {
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
			}
			if ($postdata_s) {
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata_s);
			}
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			if ($timeout > 0 && $timeout < 1) {
				curl_setopt($curl, CURLOPT_NOSIGNAL, 1);//ms
				curl_setopt($curl, CURLOPT_TIMEOUT_MS, 200);//since cURL 7.16.2/PHP 5.2.3
			} elseif ($timeout >= 1) {
				curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
			}
			$result = curl_exec($curl);
			curl_close($curl);
			$errno = curl_errno($curl);
			if ($errno) {
				throw new \Exception(curl_error($curl), $errno);
			}
			return $result;
		}

		public static function stderrln($s)
		{
			if (is_array($s) || is_object($s)) {
				$s = self::o2s($s, $wellformat);
			}
			file_put_contents('php://stderr', $s . "\n", FILE_APPEND);
		}

		public static function stderr($s)
		{
			if (is_array($s) || is_object($s)) {
				$s = self::o2s($s, $wellformat);
			}
			file_put_contents('php://stderr', $s, FILE_APPEND);
		}

		public static function println($s, $wellformat = false)
		{
			if (is_string($s)) {
			} else {
				$s = self::o2s($s, $wellformat);
			}
			print $s . "\n";
			//if(is_array($s) || is_object($s)){
			//	$s=self::o2s($s,$wellformat);
			//}
			////file_put_contents('php://stdout',$s,FILE_APPEND);
			//print $s ."\n";//NOTES: don't use PHP_EOL;
		}

		public static function getbarcode($defaultLen = 23, $seed = '0123456789ABCDEF')
		{
			$code = "";
			list($usec, $sec) = explode(" ", microtime());
			srand($sec + $usec * 100000);
			$len = strlen($seed) - 1;
			for ($i = 0; $i < $defaultLen; $i++) {
				$code .= substr($seed, rand(0, $len), 1);
			}
			return $code;
		}

		//NOTES: maybe update in future :)
		public static function os_compare($bits)
		{
			$my_bits = 32;
			$isos64bit = (strstr(php_uname("m"), '64')) ? true : false;
			if ($isos64bit) $my_bits = 64;
			$isos128bit = (strstr(php_uname("m"), '128')) ? true : false;//future
			if ($isos128bit) $my_bits = 128;
			if ($my_bits > $bits) return 1;
			if ($my_bits < $bits) return -1;
			return 0;
		}

		public static function getDateTimeObj($timestamp, $timezone)
		{
			if (!$timezone) {
				$timezone = ini_get("date.timezone");
			}
			if (!$timezone) $timezone = 'UTC';
			if ($timestamp != '') {
				$o = date_create_from_format('U', $timestamp);
				if (!$o) {
					//try U.u
					$o = date_create_from_format('U.u', $timestamp);
				}
				if ($timezone) {
					date_timezone_set($o, new \DateTimeZone($timezone));
					//println( "TMP2----$timezone ---".$o->format('Y-m-d H:i:s') );
				} else {
					date_timezone_set($o, new \DateTimeZone('UTC'));
					//println( "TMP3----UTC ---".$o->format('Y-m-d H:i:s') );
				}
			} else {
				if ($timezone) {
					$o = date_create("now", new \DateTimeZone($timezone));
					//println( "TMP4----$timezone ---".$o->format('Y-m-d H:i:s') );
				} else {
					$o = date_create("now", new \DateTimeZone('UTC'));
					//$o=date_create("now");//not using UTC but default...
					//println( "TMP5----".$o->format('Y-m-d H:i:s') );
				}
			}
			if ($o) {
				//println( "TMP6----".$o->format('Y-m-d H:i:s') );
				return $o;
			} else {
				throw new \Exception(__CLASS__ . "." . __METHOD__ . "() ERROR: "
					. "timestamp=" . self::o2s($timestamp));
			}
		}

		public static function isoDate($timestamp, $timezone)
		{
			return self::getDateTimeObj($timestamp, $timezone)->format('Y-m-d');
		}

		public static function isoDateTime($timestamp = null, $timezone = null)
		{
			return self::getDateTimeObj($timestamp, $timezone)->format('Y-m-d H:i:s');
		}

		public static function getYmdHis($timestamp = null, $timezone = null)
		{
			return self::getDateTimeObj($timestamp, $timezone)->format('YmdHis');
		}

		//yyyymmdd
		public static function getyyyymmdd($timestamp = null, $timezone = null)
		{
			return self::getDateTimeObj($timestamp, $timezone)->format('Ymd');
		}

		//yymmdd
		public static function getyymmdd($timestamp = null, $timezone = null)
		{
			return self::getDateTimeObj($timestamp, $timezone)->format('ymd');
		}

		//mmdd
		public static function getmmdd($timestamp = null, $timezone = null)
		{
			return self::getDateTimeObj($timestamp, $timezone)->format('md');
		}

		//if $s, translate from datetime string to unix-timestamp
		//if !$s, using now.
		public static function getTimeStamp($s, $timezone = null)
		{
			$strlen_s = strlen($s);
			if ($timezone) {
				$tz = new \DateTimeZone($timezone);
			} else {
				$tz = new \DateTimeZone('UTC');
			}
			if ($strlen_s > 10) {
				//assume YYYY-MM-DD HH:ii:ss, @ref http://php.net/manual/en/datetime.createfromformat.php
				$o = date_create_from_format('Y-m-d H:i:s', $s, $tz);
			} elseif ($strlen_s > 9) {
				//handle YYYY-MM-DD
				$o = date_create_from_format('Y-m-d H:i:s', $s . ' 00:00:00', $tz);
			} elseif ($strlen_s > 0) {
				throw new \Exception(__CLASS__ . "." . __METHOD__ . "() Unsupport $s");
			} else {
				$o = date_create("now", $tz);
			}
			//if(!$o) return null;
			if (!$o) {
				throw new \Exception(__CLASS__ . "." . __METHOD__ . "() Unsupport $s");
			}
			return $o->format('U');
		}

		public static function str_starts_with($haystack, $needle)
		{
			return preg_match('/^' . preg_quote($needle, '/') . '/', $haystack) > 0;
		}

		public static function str_ends_with($haystack, $needle)
		{
			return preg_match('/' . preg_quote($needle, '/') . '$/', $haystack) > 0;
		}
		//judge array whether a associate array
		//https://gist.github.com/1965669
		public static function is_assoc($array)
		{
			return (array_values($array) !== $array);
		}

		public static function throwIf($flag, $msg_param, $msg_tpl, $errcode)
		{
			//@ref vsprintf http://php.net/manual/en/function.sprintf.php
			if ($flag) {
				if ($errcode > 0)
					throw new \Exception(vsprintf(getLang($msg_tpl), $msg_param), $errcode);
				else
					throw new \Exception(vsprintf(getLang($msg_tpl), $msg_param));
			}
		}
	}//LibCore

	class CmpClassLoader
		extends LibCore
	{
		public static function Size($path, $recursive = true)
		{
			$result = 0;

			if (is_dir($path) === true) {
				$path = self::Path($path);
				$files = array_diff(scandir($path), array('.', '..'));

				foreach ($files as $file) {
					if (is_dir($path . $file) === true) {
						$result += ($recursive === true) ? self::Size($path . $file, $recursive) : 0;
					} else if (is_file() === true) {
						$result += sprintf('%u', filesize($path . $file));
					}
				}
			} else if (is_file($path) === true) {
				$result += sprintf('%u', filesize($path));
			}

			return $result;
		}

		public static function Path($path)
		{
			if (file_exists($path) === true) {
				$path = rtrim(str_replace('\\', '/', realpath($path)), '/');

				if (is_dir($path) === true) {
					$path .= '/';
				}

				return $path;
			}

			return false;
		}

		//TODO  $filter defaults array('.php$');
		public static function Map($path, $recursive = false, $filters)
		{
			$result = array();

			if (is_dir($path) === true) {
				$path = self::Path($path);
				$files = array_diff(scandir($path), array('.', '..'));

				foreach ($files as $file) {
					if (is_dir($path . $file) === true) {
						$result[$file] = ($recursive === true) ? self::Map($path . $file, $recursive) : self::Size($path . $file, true);
					} else if (is_file($path . $file) === true) {
						//TODO if !$filters checkif $file matches $filter... skip if not matches
						$result[$file] = self::Size($path . $file);
					}
				}
			} else if (is_file($path) === true) {
				$result[basename($path)] = self::Size($path);
			}

			return $result;
		}

		public static function getModuleMD5($dir)
		{
			static $md5;
			if ($md5) return $md5;
			if (!$dir) $dir = __DIR__;
			$md5 = md5(serialize(self::Map($dir, true)));
			return $md5;
		}

		public static function tryLoad($classname)
		{
			$ns = "CMP\\";
			if (self::str_starts_with($classname, $ns)) {
				$xxx = substr($classname, strlen($ns));
				if ($xxx && $classname != 'CMP') {
					include_once(__DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $xxx) . '.php');
					if (class_exists($classname)) {
					} else {
						self::stderrln("not found $classname\n");
					}
				}
			}
		}
	}

	//default behavior about to load class
	spl_autoload_register(function ($class_name) {
		CmpClassLoader::tryLoad($class_name);
	});
}//namespace CMP

//some wonderfull short global func, not suitable to move to LibBase/LibCore...
namespace
{
	//Usage: eval(arr2var_all("param"));
	if (!function_exists('arr2var_all')) {
		function arr2var_all($name_of_arr)
		{
			return <<<EOS
eval(arr2var("$name_of_arr",array_keys(\$$name_of_arr)));
EOS
				;
		}
	}
	//Usage: eval(arr2var("arr",$arr));
	//Usage: eval(arr2var("arr",$arr,"v_"));
	if (!function_exists('arr2var')) {
		function arr2var($name_arr, $arr, $prefix = "")
		{
			if (!is_array($arr)) {
				throw new \Exception(getLang("KO-arr2var-notarray") . my_json_encode($arr));
			}
			$rt = "";
			foreach ($arr as $key) {
				$rt .= '$' . $prefix . $key . '=$' . $name_arr . '["' . $key . '"];';
			}
			return $rt;
		}
	}
	//Usage: eval
	if (!function_exists('var2arr')) {
		function var2arr($name_arr, $arr)
		{
			$rt = "";
			foreach ($arr as $key) {
				$rt .= '$' . $name_arr . '["' . $key . '"]=$' . $key . ';';
			}
			$rt .= ";";
			return $rt;
		}
	}

	//Usage:
	//arr2arr($rt,$_SESSION,array('auth_user_id'));
	if (!function_exists('arr2arr')) {
		function arr2arr(& $target, $src, $key_a, $prefix = "")
		{
			if (is_null($target)) {
				$target = array();
			}
			if (is_array($key_a)) {
				foreach ($key_a as $k) {
					$target[$prefix . $k] = $src[$k];
				}
			} else {
				//or all:
				foreach ($src as $k => $v) {
					$target[$prefix . $k] = $v;
				}
			}
			return $target;//just for some special case
		}
	}

	// e.g.
	// abc'ced
	// abc''ced
	if (!function_exists('qstr2')) {
		function qstr2($s)
		{
			//just replace ' to ''
			return str_replace("'", "''", $s);
		}
	}

	// e.g.
	// abc'ced
	// =>
	// 'abc''ced''
	if (!function_exists('qstr')) {
		function qstr($s)
		{
			$x = "'" . str_replace("'", "''", $s) . "'";
			return $x;
		}
	}

	// e.g.
	// $s=qstr_arr(array("x","y");
	// => $s=> "'x','y'"
	if (!function_exists('qstr_arr')) {
		function qstr_arr($_a)
		{
			if (is_array($_a)) {
			} else {
				//Let expect string...
				$_a = explode(",", $_a);
			}
			foreach ($_a as $k => $v) {
				$_a[$k] = qstr($v);
			}
			return join(',', $_a);
		}
	}
}//namespace
