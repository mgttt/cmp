<?php
//时间服务
class ServiceDateTime
{
	static protected $rbWrapper;

	//默认拿主配置DB的时间戳。注意，新项目尽量不要乱用!!!
	//因为unixtimestamp是有2038问题的，这里虽然fix但其实是为了兼容一些旧系统而已
	//WARNING: don't use unless special case.
	public static function getDefaultDbTimeStamp($db_dsn, & $flag_cache, $cache_time=7){
		if(! self::$rbWrapper){
			if(!$db_dsn) $db_dsn=ORM_Base::$DSN;
			self::$rbWrapper=new rbWrapper($db_dsn);
		}
		$o=self::$rbWrapper;
		return $o->getDbTimeStamp($cache_time,$flag_cache);
	}
}
