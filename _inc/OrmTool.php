<?php
class OrmTool
{
	//////////////////////////////////////////////////////
	//查找并调用商业逻辑代码片段（碎片化反而方便维护）
	//Usage: eval(???::Rule($rule_code));
	public static function Rule($rule_code){
		return <<<RULE_END
if(!file_exists("rule.$rule_code.php")) throw new Exception("NotFoundRule($rule_code)");
\$rule_code="$rule_code";
require("rule.$rule_code.php");
RULE_END;
	}

	//先移除，应该没有呼叫的了..
	//public function CheckMandatory($a,$field_a){
	//	foreach($field_a as $v){
	//		$f=$a[$v];
	//		if(!$f) throw new Exception(getLang('KO-ORM_Base.CheckMandatory')." $v");
	//	}
	//}

	/*
	//i.e. InsertUpdate
	public function FindAndUpdateOrCreateBean($orm_class,$beanInfo,$sql_piece,$binding,$errmsg){
		$rt=array();
//		$beanInfo=$this->setBeanInfo($beanInfo);
		$orm=$orm_class->findBeanOne($sql_piece,$binding);
		if($orm){
			$orm=$orm_class->updateBean($orm->id,$beanInfo);
			$rt['STS']="OK";
		}else{
			$orm=$orm_class->newBean($beanInfo);
			$rt['STS']="OK";
		}
		return $rt;
	}
	 */

}
