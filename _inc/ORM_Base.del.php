<?php
class ORM_Base
	//extends rbWrapper4
	extends OrmBase //sync with cmp
{
}

//下面的都先移除，已经检查过没有使用的，不过为安全计先不完全删除，预计8月中删除：
//《名称索引集》，然后bean_name_a定义在_lang下的那个xls中..
//public $bean_name_a;//由子类覆盖

//即将移除(暂时好像只有不多在使用?) TODO_CONG TODO_WJC
//Get tabel "header" from xls ONLY
//	public function getTableHeader($field_a){
//		$a=array();
//		$orm_type=$this->getBeanType();
//		if($field_a && count($field_a)>0){
//		}else{
//			$field_a=$this->bean_name_a;
//		}
//		if($field_a && count($field_a)>0){
//			foreach($field_a as $k=>$v){
//				if(is_numeric($k)){
//					$field=$v;
//				}else{
//					$field=$k;
//				}
//				$one=array();
//				$one['field']=$field;
//				$one['source']='xls';
//				$a[]=$one;
//			}
//		}
//		$col_a=array();
//		foreach($a as $v){
//			$col=array();
//			if($v['source'])
//				$col['source']=$v['source'];
//			$field=$v['field'];
//			$col['field']=$field;
//			$orm_key=$orm_type."_".$field;
//
//			//translation from xls:
//			$col['title']=getLang($orm_key);
//
//			//type from xls:
//			$xls_type=getLang($orm_key,"type");
//			if($xls_type){
//				$col['xlstype']=$xls_type;
//			}
//
//			//uidefault from xls:
//			$orm_column=getLang($orm_key,"uidefault");
//			if($orm_column){
//				$orm_column_a=explode(',',$orm_column);
//				foreach($orm_column_a as $orm_column_a_v){
//					list($kkk,$vvv)=explode(':',$orm_column_a_v);
//					if($kkk=='notlist')$kkk='hidden';
//					$col[$kkk]=$vvv;
//				}
//			}
//			$col_a[]=$col;
//		}
//		$rt=$col_a;
//		return $rt;
//	}

