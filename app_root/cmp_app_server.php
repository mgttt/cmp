<?php
/* vim: set tabstop=2 shiftwidth=2 softtabstop=2: */
error_reporting(E_ERROR|E_COMPILE_ERROR|E_PARSE|E_CORE_ERROR|E_USER_ERROR);

//TODO console parameters handling:
//$shortopts  = "";
//$shortopts .= "p:";  // Required value
//$shortopts .= "h::"; // Optional value
//$shortopts .= "abc"; // These options do not accept values
//
//$longopts  = array(
//    "prefix:",     // Required value
//    "optional::",    // Optional value
//    "option",        // No value
//    "opt",           // No value
//);
//$options = getopt($shortopts, $longopts);
//var_dump($options);

if (!function_exists('http_parse_headers'))
{
	#@ref http://php.net/manual/fa/function.http-parse-headers.php
	function http_parse_headers($raw_headers)
	{
		$headers = array();
		$key = '';

		foreach(explode("\n", $raw_headers) as $i => $h)
		{
			$h = explode(':', $h, 2);

			if (isset($h[1]))
			{
				if (!isset($headers[$h[0]]))
				{
					$headers[$h[0]] = trim($h[1]);
				}
				elseif (is_array($headers[$h[0]]))
				{
					$headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1])));
				}
				else
				{
					$headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1])));
				}
				$key = $h[0];
			}
			else
			{
				if (substr($h[0], 0, 1) == "\t")
					$headers[$key] .= "\r\n\t".trim($h[0]);
				elseif (!$key)
					$headers[0] = trim($h[0]);trim($h[0]);
			}
		}
		return $headers;
	}
}
if (!function_exists('http_build_cookie'))
{
	//@ref http://php.net/manual/fa/function.http-parse-cookie.php
	function http_build_cookie( $data )
	{
		if( is_array( $data ) )
		{
			$cookie = '';
			foreach( $data as $k=>$v )
			{
				$cookie[] = urlencode($k).'='.urlencode($v);
			}
			if( count( $cookie ) > 0 )
			{
				return trim( implode( '; ', $cookie ) );
			}
		}
		return false;
	}
}

$http = new swoole_http_server("0.0.0.0", 9501);

//TODO override by console parameters...

if(!defined("WEBROOT")){
	//echo __DIR__."\n";
	define("APPROOT",realpath(__DIR__));
	define("WEBROOT",realpath(__DIR__.'/webroot/'));
}
echo APPROOT."\n";
echo WEBROOT."\n";

define("FPM_HOST",'localhost');
define("FPM_PORT",'9000');

//for future, unix socket could even faster..
#define("FPM_HOST",'unix:///path/to/php/socket');
#define("FPM_PORT",-1);

require_once 'PhpfpmClient.php';
function get_php_fpm_client(){
	//TODO get from the php-fpm-client-pool to get the vacant one.... but..should that even have a better socket open time?
	$client = new PhpfpmClient(FPM_HOST, FPM_PORT);
	$client->setReadWriteTimeout(300000);//changed to 300 seconds
	return $client;
}

define('VERSION_CMP_APP_SERVER', date('YmdHis',filemtime(__FILE__)));
echo VERSION_CMP_APP_SERVER."\n";
$http->on('request', function ($request, $response)
{
	try{
		$phpfpmclient = get_php_fpm_client();

		//_SERVER
		$p=array_change_key_case($request->server,CASE_UPPER);

		//REQUEST_URI
		$REQUEST_URI=$p['REQUEST_URI'];

		//REQUEST HEADERS //@ref getallheaders()
		$req_header_a=array();
		foreach($request->header as $k=>$v){
			$kk = 'HTTP_'.strtoupper(str_replace('-','_',$k));
			$req_header_a[$kk]=$v;
		}
		$req_header_a['HTTP_VERSION_CMP_APP_SERVER']=VERSION_CMP_APP_SERVER;
		$p=array_merge($p, $req_header_a);

		//_COOKIE
		if(isset($request->cookie)){
			$p['HTTP_COOKIE']=http_build_cookie($request->cookie);
		}

		$p['SCRIPT_FILENAME']=APPROOT.'/cmp_root_controller.php';

		$REQUEST_METHOD=$p['REQUEST_METHOD'];

		print "$REQUEST_METHOD $REQUEST_URI\n";

		if($REQUEST_METHOD=='POST'){
			//TODO FILES handling...needs more development

			$post_s=$request->rawContent();
			$p['CONTENT_TYPE']='application/x-www-form-urlencoded';//IMPORTANT for $_POST
			$p['CONTENT_LENGTH']=strlen($post_s);//IMPORTANT...
			//print "DBG: post_s=$post_s\n";
			$s=$phpfpmclient->request( $p, $post_s );

		}elseif($REQUEST_METHOD=='GET'){
			$s=$phpfpmclient->request( $p, "" );

		}else{
			//Other than GET/POST is not yet supported
			$s="TODO $REQUEST_METHOD $REQUEST_URI";
		}
	}catch(Exception $ex){
		$s=$ex->getCode() .':'.$ex->getMessage();
	}
	$eoh = strpos($s, "\r\n\r\n"); 
	if( $eoh )
	{
		$resp_header_s = substr($s, 0, $eoh);
		$resp_body = substr($s, $eoh + 4);
		$resp_header_a=http_parse_headers($resp_header_s);
		foreach($resp_header_a as $k=>$v){
			if(strtolower($k)=='status'){
				$resp_status=preg_replace("/^(\\d*)(.*)$/","\\1",$v);
				$response->status($resp_status);
				continue;
			}
			if(is_string($v))
			{
				$response->header($k,$v);
			}
			elseif(is_array($v))
			{
				//use the last one as tmp solution ...
				$v=array_pop($v);
				$response->header($k,$v);
			}
		}
	}else{
		$resp_body = $s;
	}
	if($resp_body){
		$response->write($resp_body);
	}
	$response->end();
});

#$http->on('pipeMessage', function($serv, $src_worker_id, $data) {
#	echo "#{$serv->worker_id} message from #$src_worker_id: $data\n";
#});

//TODO
//$http->addlistener("127.0.0.1", 9502, SWOOLE_SOCK_TCP);

$http->start();

//@ref
//http://php.net/manual/en/function.getallheaders.php
//http://php.net/manual/en/function.apache-request-headers.php
//if (!function_exists('getallheaders')) 
//{ 
//	function getallheaders() 
//	{ 
//		$headers = ''; 
//		foreach ($_SERVER as $name => $value) 
//		{ 
//			if (substr($name, 0, 5) == 'HTTP_') 
//			{ 
//				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value; 
//			} 
//		} 
//		return $headers; 
//	} 
//}
//if( !function_exists('apache_request_headers') ) {
//	function apache_request_headers() {
//		$arh = array();
//		$rx_http = '/\AHTTP_/';
//		foreach($_SERVER as $key => $val) {
//			if( preg_match($rx_http, $key) ) {
//				$arh_key = preg_replace($rx_http, '', $key);
//				$rx_matches = array();
//				// do some nasty string manipulations to restore the original letter case
//				// this should work in most cases
//				$rx_matches = explode('_', strtolower($arh_key));
//				if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
//					foreach($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
//					$arh_key = implode('-', $rx_matches);
//				}
//				$arh[$arh_key] = $val;
//			}
//		}
//		if(isset($_SERVER['CONTENT_TYPE'])) $arh['Content-Type'] = $_SERVER['CONTENT_TYPE'];
//		if(isset($_SERVER['CONTENT_LENGTH'])) $arh['Content-Length'] = $_SERVER['CONTENT_LENGTH'];
//		return( $arh );
//	}
//}
