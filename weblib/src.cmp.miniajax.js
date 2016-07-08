ajax={
x:function(){return new(self.XMLHttpRequest||ActiveXObject)("Microsoft.XMLHTTP");}
,send:function(u,f,m,a){var x=this.x();x.open(m,u,true);x.onreadystatechange=function(){if(x.readyState==4){f(x.responseText,x.status,u);}};if(m=='POST')x.setRequestHeader('Content-type','application/x-www-form-urlencoded');x.send(a);}
,get:function(u,cb){this.send(u,cb,'GET');}
,gets:function(u){var x=this.x();x.open('GET',u,false);x.send(null);return x.responseText;}
,post:function(u,cb,args){this.send(u,cb,'POST',args);}
};
