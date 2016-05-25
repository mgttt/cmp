<?php
class GlrsTool
{
	public static function BuildSaasConfFileForAcct(){		
		//获取生成配置文件路径
		$saas_conf_file=self::GetSaasConfFileName();

		//获取tenant_code; 目前acct系统直接在conf获取
		$conf = getConf("tenant_conf");
		if(!$conf){
			quicklog_must("IT-CHECK","Tenant_Tool s=$s");
			quicklog_must("IT-CHECK",'Tenant_Tool o='.my_json_encode($o));
			$errmsg="Config(saas_conf) Error";
			throw new Exception($errmsg);
		}
		$file_s=var_export($conf,true);
		$file_s="\$saas_conf=$file_s;";
		file_put_contents($saas_conf_file,"<"."?php\n$file_s");

		//load right away
		self::MergeSaasConf();
	}}
