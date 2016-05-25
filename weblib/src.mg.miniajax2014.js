/*
var ajax=function(){};
ajax.x=ajax.prototype.x=(window.XMLHttpRequest)?(//Mozilla
	function(){ try { netscape.security.PrivilegeManager.enablePrivilege("UniversalBrowserRead"); } catch (e) { }
	var rt=new XMLHttpRequest(); if(rt.overrideMimeType){rt.overrideMimeType('text/plain');} if (!rt) { alert('Cannot create XMLHTTP instance'); } return rt;}
):((window.ActiveXObject)?(function(){return new ActiveXObject("Msxml2.XMLHTTP");}):(function(){return new ActiveXObject("Microsoft.XMLHTTP");}));
*/
//ref http://www.cnblogs.com/index-html/archive/2011/10/09/xmlhttp_code_min.html
//new(-[1,]?XMLHttpRequest:ActiveXObject)("Microsoft.XMLHTTP")
//new(self.XMLHttpRequest||ActiveXObject)("Microsoft.XMLHTTP")
//ajax.x=new(this.XMLHttpRequest||ActiveXObject)("Microsoft.XMLHTTP");
/*
var ajax={};
ajax.x=new(self.XMLHttpRequest||ActiveXObject)("Microsoft.XMLHTTP");
ajax.send=ajax.prototype.send=function(u,f,m,a){ var x=this.x(); x.open(m,u,true); x.onreadystatechange=function(){ if(x.readyState==4){ f(x.responseText,x.status,u); } }; if(m=='POST')x.setRequestHeader('Content-type','application/x-www-form-urlencoded'); x.send(a); };
ajax.get=ajax.prototype.get=function(url,func){this.send(url,func,'GET')};
ajax.gets=ajax.prototype.gets=function(url){var x=this.x();x.open('GET',url,false);x.send(null);return x.responseText};
ajax.post=ajax.prototype.post=function(url,func,args){this.send(url,func,'POST',args)};
*/
ajax={
x:function(){return new(self.XMLHttpRequest||ActiveXObject)("Microsoft.XMLHTTP");}
,send:function(u,f,m,a){var x=this.x();x.open(m,u,true);x.onreadystatechange=function(){if(x.readyState==4){f(x.responseText,x.status,u);}};if(m=='POST')x.setRequestHeader('Content-type','application/x-www-form-urlencoded');x.send(a);}
,get:function(url,func){this.send(url,func,'GET');}
,gets:function(url){var x=this.x();x.open('GET',url,false);x.send(null);return x.responseText;}
,post:function(url,func,args){this.send(url,func,'POST',args);}
};
