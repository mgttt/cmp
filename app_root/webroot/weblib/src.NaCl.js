//In Wap/Mini Env, we don't use jQuery, target is to shrink the size of the files.
//@ref http://microjs.com/
//@ref https://github.com/james2doyle/saltjs/blob/master/js/salt.js
/*! Salt.js DOM Selector Lib. By @james2doyle */
//window.$ = function(selector, context, undefined){
//	// an object containing the matching keys and native get commands
//	//var _d=selector[0];//IE6 not compatible...
//	//var _d=(selector || "").substring(0,1);
//	var _d=(selector || "").charAt(0);//Good for even IE6
//	//var matches = {
//	//'#': 'getElementById',
//	//'.': 'getElementsByClassName',
//	//'@': 'getElementsByName',
//	//'=': 'getElementsByTagName',
//	//'*': 'querySelectorAll'
//	//}[selector[0]]; // you can treat a string as an array of characters
//	var matches = {
//		'#': 'getElementById',
//		'.': 'getElementsByClassName',
//		'@': 'getElementsByName',
//		'=': 'getElementsByTagName',
//		'*': 'querySelectorAll'
//	}[_d]; // you can treat a string as an array of characters
//
//	// now pass the selector without the key/first character
//	var el = (((context === undefined) ? document: context)[matches](selector.slice(1)));
//
//	// if there is one element than return the element
//	return ((el && el.length ==1) ? el[0]: el);
//};
//NaCl
//NOTES: * to select all is meangingless.   default to ById
//window.$=function(s,c,r,m){s=s||"";m={'#':'getElementById','.':'getElementsByClassName','@':'getElementsByName','=':'getElementsByTagName','*':'querySelectorAll'}[s.charAt(0)];r=m?(c||document)[m](s.slice(1)):[];return (r&&r.length==1)?r[0]:r;}
//window.$=function(s,c,r){s=s||"";r=(c||document)[{'#':'getElementById','.':'getElementsByClassName','@':'getElementsByName','=':'getElementsByTagName','*':'querySelectorAll'}[s.charAt(0)]](s.slice(1))||[];return(r&&r.length==1)?r[0]:r;}
window.$=function(s,c){r=(c||document)['getElement'+({'.':'sByClassName','@':'sByName','=':'sByTagName'}[s.charAt(0)]||'ById')](s.slice(1))||[];return(r.length==1)?r[0]:r;}

