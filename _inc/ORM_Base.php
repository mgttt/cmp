<?php
class ORM_Base
	//extends rbWrapper4
	extends OrmBase //sync with cmp
{
	//old codes...  @deprecated !!!!
	public static $DSN='db_app';
	static protected $rbWrapper4;
	public static function getDefaultDbTimeStamp($db_dsn, & $flag_cache, $cache_time=7){
		if(! self::$rbWrapper4){
			//默认拿主配置的时间.但是这样是不是很建议的，以后再想有没有其它solution:
			if(!$db_dsn) $db_dsn=self::$DSN;
			self::$rbWrapper4=new rbWrapper4($db_dsn);
		}
		$o=self::$rbWrapper4;
		return $o->getDbTimeStamp($cache_time,$flag_cache);
	}
	public function SearchList($param){
		throw new Exception("TODO OVERRIDE SearchList");
	}
}

