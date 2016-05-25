/**
	<form method="post" enctype="multipart/form-data" action="javascript:alert('Error');" onSubmit="return frmFileUpload_onSubmit(this);" _id="frmFileUpload">
	File Upload:
	<input type="file" name="file" onChange="frmFileUpload_onSubmit(this.form);" />
	<!--
	<input type="submit" value="Do Upload"/>
	-->
	</form>
//var mu=new clsMiniUpload({ prefix:"MiniUpload", url:"upload_test.php" });
	var mu=new clsMiniUpload({ url:"upload_test.php" });
	function frmFileUpload_onSubmit(frm){
	return mu.doUpload(function(raw_s){alert(raw_s);},frm);
	}

	or

	onSubmit="return mu.doUpload(_onUploadCallback,this);"

*/
var clsMiniUpload=function(opt){
	if (!opt) opt={};
	var _form=opt.form;
	//if(!_form) throw new Error("Need Opt form");
	this.form=_form;
	var _prefix=opt.prefix || "MiniUpload";
	//var _ifrm_id
	var _url=opt.url;
	//if(!_url) throw new Error("Need Opt url");
	this.url=_url;

	if(!this.i) this.i=0;

	var _AttachEvent = function(evnt, elem, func){
		if (elem.addEventListener)  // W3C DOM
			elem.addEventListener(evnt,func,false);
		else if (elem.attachEvent) { // IE DOM
			elem.attachEvent("on"+evnt, func);
		}
		else { //try...
			elem['on'+evnt] = func;
		}
	}

	this.gen_ifrm_id=function(){
		this.i++;
		return _prefix + "_ifrm_"+this.i;
	}

	this.doUpload=function(_callback,_form,_url) //_url is optional
	{
		if(!_form) _form=this.form;
		if(!_form) throw new Error("Need Opt form");
		if(!_url) _url=this.url;
		var _ifrm_id=this.gen_ifrm_id();
		var ie678 = !-[1,];
		//var s_ifrm='<iframe name="' + _ifrm_id + '" id="' + _ifrm_id + '" style="display: none;"></iframe>';
		// The iframe must be appended as a string otherwise IE7 will pop up the response in a new window
		// http://stackoverflow.com/a/6222471/268669
		//$("body").append(s_ifrm);//OK but jq-depends
		//alert(ie678);
		if(ie678){
			var _ifrm_el = document.createElement('<iframe name="'+_ifrm_id+'" id="'+_ifrm_id+'" style="display: none;"/>');
		}else{
			var _ifrm_el = document.createElement("iframe");
		}
		_ifrm_el.id=_ifrm_id;
		_ifrm_el.name=_ifrm_id;
		_ifrm_el.style.display='none';
		document.body.appendChild(_ifrm_el);

		_form.action=_url;
		_form.target=_ifrm_id;

		_AttachEvent("load",_ifrm_el,function(_evt){
			var _tgt = (_evt.target) ? _evt.target : _evt.srcElement;
			//var _nn=_tgt.nodeName;
			var ifrm  = _tgt;
			var win=(ifrm.contentWindow || ifrm.contentDocument);
			var raw_s='{errmsg:"Empty Response"}';
			if(win){
				var doc=win.document;
				if(doc){
					//if (doc.XMLDocument){
					//	response = doc.XMLDocument;
					//}else{
					if(doc.body){
						raw_s = doc.body.innerHTML;
					}
					//}
				}
			}
			if(_callback){
				_callback(raw_s);
			}
			//clean up.
			_form.reset();
			setTimeout(function(){
				document.body.removeChild(ifrm);
				ifrm=null;
			},555);
		});
		_form.submit();
		return false;
	}
}

