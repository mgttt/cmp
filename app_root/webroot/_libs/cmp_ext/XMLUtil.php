<?php
class XMLUtil
{

	//just incase u needed
	public static function Xml2Arr_Full($xml){
		$p = xml_parser_create();
		//xml_parser_set_option($p, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($p, XML_OPTION_SKIP_WHITE, 1);
		$vals = array ();
		$index = array ();
		xml_parse_into_struct($p, $xml, $vals, $index);
		xml_parser_free($p);
		return $vals;
	}

	public static function strip($xml_req_raw){
		$xml_resp= preg_replace("/([ \t]*<!--.*?-->)[ \t]*/s", "", $xml_req_raw);
		$xml_resp= preg_replace("/>[ \t\r\n]{1,}</s", "><", $xml_resp);//TEST
		//$xml_resp= preg_replace("/(\n{1,}/s", "\n", $xml_req_raw);//...
		return $xml_resp;
	}
	//Author: Wanjo, always quick and dirty, but clear
	//pattern:
	//$tagObj=<$tagName $tagAttribute="$valAttribute">([$tagObj]{0,}|$tagVal{0,}){0,}</$tagName>
	public static function Obj2Arr($obj, $work_level=0, $max_level=-1){

		if(! is_object($obj)){
			return null;
		}
		$tag_name=trim($obj->getName());
		$mode_key=true;//key mode
		//$tag_value=((string)$obj);
		$tag_value=trim((string)$obj);
		$has_child=false;

		$attributes=array();
		foreach($obj->attributes() as $a => $b){
			$attributes[$a]=(string)$b;
		}
		foreach($obj->children() as $child_name => $child)
		#foreach($obj as $child_name => $child)
		{
			if($child_name!=""){
				if(array_key_exists($child_name,$children_in_key)){
					$mode_key=false;
				}
				$child_obj2arr=self::Obj2Arr($child,$work_level+1);
				$children_in_key[$child_name]=$child_obj2arr["$child_name"];
				$children_in_arr[]=$child_obj2arr;
				$has_child=true;
			}
		}
		if($tag_name){
			if($has_child){
				if($tag_value){
					$mode_key=false;
					$rt[]=$tag_value;
				}
				if($mode_key){
					$rt=array($tag_name=>$children_in_key);
				}else{
					$rt=array($tag_name=>array_merge((array)$rt,$children_in_arr));
				}
				if($attributes){
					if(is_array($rt[$tag_name])){
						$rt[$tag_name]['@attributes']=$attributes;
					}
				}
			}else{
				if($attributes){
					$rt[$tag_name]=array('@attributes'=>$attributes);
					if($tag_value){
						$rt[0]=$tag_value;
					}
				}else{
					$rt=array($tag_name=>$tag_value);
				}
			}
		}else{
			if($mode_key){
				$rt=$children_in_key;
			}else{
				$rt=$children_in_arr;
			}
		}
		return $rt;
	}

	public static function Xml2Arr($x,$options){
		//return self::Obj2Arr(self::Xml2Obj($x,$options));
		try{
			$o=self::Xml2Obj($x,$options);
			return self::Obj2Arr($o);
		}catch(Exception $ex){
			return $x;//TODO
		}
	}
	public static function Xml2Obj($x,$options){
		$rt = null;

		if($x){
			// SimpleXML seems to have problems with the colon ":" in the <xx-x:yyy> response tags, so take them out
			//$x= preg_replace("/(<\/?)([\w-]+):([^>]*>)/", "$1$2_$3", $x);
			$x= preg_replace("/(<\/?)([\w-]+):([\w]*)/", "$1$2_$3", $x);
			//wq_log("worklog_internal","xml=$x");
			$x=str_replace("xmlns:","xmlns_",$x);
			//$x= preg_replace("/xmlns=\".*?\"/si", "", $x);
			//wq_log("worklog_internal","xml=$x");

			//$rt = new SimpleXMLElement($x, LIBXML_NOCDATA);
			$rt=simplexml_load_string($x, 'SimpleXMLElement',
				LIBXML_NOCDATA
				& LIBXML_NSCLEAN
				& LIBXML_PARSEHUGE
				& LIBXML_NOXMLDEC
				//& LIBXML_COMPACT
			);
			if(!$rt){
				throw new Exception(("InvalidXML"));
			}
			#if($debug>2){
			#	wq_log_internal("$x");
			#}
		}
		return $rt;
	}
	//which pure php arr 2 xml with attributes in element
	/*
	input===================================
	$arr = array
	(
		'segment' => array (
			'@attributes'=>array(
		      	'attr1' => "1",
		      	'attr2' => "2",
			),
		  	'airline' => 'DL',
		  	'from' => 'LAX',
		  	'to' => 'PVG',
		  	'remark' => array (
		  	  	'@attributes'=>array(
			    	 'attr3' => "3",
			     	 'attr4' => "4",
				),
		 		'myremark' => 'this is a segment',
		  	),
		),
	);
	output===================================
	<segment attr1="1"  attr2="2" >
		<airline>DL</airline>
		<from>LAX</from>
		<to>PVG</to>
		<remark attr3="3"  attr4="4" >
			<myremark>this is a segment</myremark>
		</remark>
	</segment>
	**/
	public static function Arr2Xml($arr,$work_level,$max_level=-1){
		$rt="";
		$key_c=0;
		$v_xml="";
		if(is_array($arr)){
			foreach($arr as $k => $v){
				if($k === "@attributes"){
					continue;
				}
				$attr = "";
				if(is_array($v['@attributes']))
				{
					foreach($v['@attributes'] as $attr_key => $attr_val)
					{
						//$attr .= " $attr_key=\"$attr_val\" ";
						$attr .= " $attr_key=\"".htmlentities($attr_val)."\"";
					}

				}
				$key_c++;
				if(is_numeric($k) && $k>=0 && $v){
					$v_xml.="".self::Arr2Xml($v,$work_level+1)."";
				}else{
					$v_xml_child=self::Arr2Xml($v,$work_level+1);
					if($v_xml_child!=="") $v_xml.="<$k$attr>$v_xml_child</$k>\n";
					else $v_xml.="<$k$attr/>\n";
				}
			}
			if($key_c>0){
				$rt="$v_xml";
			}
		}else{
			$rt=htmlentities((string)$arr);//(string)$arr;
		}
		return $rt;
	}

}
