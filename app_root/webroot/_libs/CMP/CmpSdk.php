<?php
//A Base File for building "SDK"
use \CMP\LibCore;
use \CMP\CmpClassLoader;
class CmpSdk
	extends LibCore
{
	public function setCacheDir($cacheDir){
		if($cacheDir) $this->_cacheDir=$cacheDir;
	}
	public function getCacheDir(){
		$cacheDir=$this->_cacheDir;
		if(!$cacheDir){
			if(defined('_TMP_')){
				$this->_cacheDir=$cacheDir=_TMP_;
			}else{
				//default to {$WhereCmpSdk}/cache/
				$this->_cacheDir=$cacheDir=__DIR__ .DIRECTORY_SEPARATOR ."cache".DIRECTORY_SEPARATOR;
			}
		}
		return $cacheDir;
	}
	protected $_lifeTime=3600;//default value 1hr.
	public function setCacheTime($cacheTime){
		if($cacheTime) $this->_lifeTime=$cacheTime;
	}
	public function saveCache($cache_id,$data){
		if(!class_exists('Cache_Lite')){
			if(file_exists(__DIR__ ."/lib.Cache_Lite/Lite.php")){
				require_once(__DIR__ ."/lib.Cache_Lite/Lite.php");
			}else{
				if(!defined("_LIB_")){
					throw new \Exception("Not found Cache_Lite");
				}
				require_once(_LIB_ ."/lib.Cache_Lite/Lite.php");
			}
		}
		$cacheDir=$this->getCacheDir();
		$options = array( 'cacheDir' => $cacheDir);
		$lifeTime=$this->_lifeTime;
		if($lifeTime)$options['lifeTime']=$lifeTime;

		$Cache_Lite = new Cache_Lite($options);
		$Cache_Lite->save(self::o2s($data,true),$cache_id);
		return true;
	}
	public function loadCache($cache_id){
		if(!class_exists('Cache_Lite')){
			if(file_exists(__DIR__ ."/lib.Cache_Lite/Lite.php")){
				require_once(__DIR__ ."/lib.Cache_Lite/Lite.php");
			}else{
				if(!defined("_LIB_")){
					throw new \Exception("Not found Cache_Lite");
				}
				require_once(_LIB_ ."/lib.Cache_Lite/Lite.php");
			}
		}
		$cacheDir=$this->getCacheDir();
		$options = array( 'cacheDir' => $cacheDir);
		$lifeTime=$this->_lifeTime;
		if($lifeTime)$options['lifeTime']=$lifeTime;

		$Cache_Lite = new Cache_Lite($options);
		$data = $Cache_Lite->get($cache_id);
		if($data){
			return $this->s2o($data);
		}
		return false;
	}

	public static function getClassMd5($file){
		if(!$file) $file=__FILE__;
		return CmpClassLoader::getModuleMD5($file);
	}
	public static function getModuleMd5($dir){
		if(!$dir) $dir=__DIR__;
		return CmpClassLoader::getModuleMD5($dir);
	}

	public $debug_level=0;
	public function setDebugLevel($lvl){
		$this->debug_level=$lvl;
	}
	protected function _debugln($s,$lvl=1){
		if($this->debug_level>=$lvl){
			$prefix="[$lvl] ".$this->isoDateTime()." ";
			if(is_string($s)){
			}else{
				$s=$this->o2s($s);
			}
			$suffix="\n";
			$this->stderr($prefix.$s.$suffix);
		}
	}
	//u: the mandatory full url
	//p: the optional parameters for the remote api
	//timeout: the optional timeout for calling the remote api
	public function callRawApi($param){
		$u=$param['u'];
		if(!$u) throw new Exception('callRawApi NEED "u"');
		$p=$param['p'];
		$this->_debugln("callRawApi.web($u){",2);
		$this->_debugln($p,3);
		$timeout=$param['timeout'];
		if($timeout>0){
			$s=$this->web($u,$p,$timeout);
		}else{
			$s=$this->web($u,$p);
		}
		$this->_debugln("}//callRawApi.web()",2);
		$this->_debugln("s=$s",3);
		return $s;
	}
	/* Wrapper to callRawApi */
	public function callCmpApi($param){

		$rt=array();
		$STS='KO';
		do{
			if($param['u']!=''){
				//use u if presented
			}else{
				//build u
				$c=$param['c'];
				$m=$param['m'];
				$api_entry=$param['api_entry'];
				if(!$api_entry){
					$errcode=999990;
					$errmsg=__FUNCTION__. "($m) not found api_entry";
					break;
				}
				if($c){
					$u=$api_entry."$c.$m.api";
				}elseif($m){
					$u=$api_entry."$m.api";
				}else{
					$errcode=888886;
					$errmsg='Need "u" or "api_entry/c.m"';
					break;
				}
			}
			$q=$param['q'];
			if($q){
				if($this->str_ends_with($u,'?')){
					$u.=$q;
				}else{
					$u.='?'.$q;
				}
			}
			if($u) $param['u']=$u;

			$s=$this->callRawApi($param);
			$rt=$this->s2o($s);
			if(is_array($rt) && isset($rt['STS'])){
				//expected .STS as Cmp Spec
				$STS=$rt['STS'];
			}else{
				if($s==''){
					$errcode=999998;
					$errmsg='Server Returns Nothing (Network broken?)';
				}else{
					if(is_array($rt)){
						$STS='';
					}else{
						$errcode=999997;
						$errmsg='Unexpected Response';
						$rt['s']=$s;//for further investigation
					}
				}
			}
		}while(false);

		if($STS)$rt['STS']=$STS;
		if($errcode)$rt['errcode']=$errcode;
		if($errmsg)$rt['errmsg']=$errmsg;

		return $rt;
	}//callCmpApi()
}//CmpSdk
