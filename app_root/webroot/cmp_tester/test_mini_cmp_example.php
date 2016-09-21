<?php
/*
 * 这个文件演示一个最迷你的方式来写一个小小工具：用百度网页来查询一个汇率并获得返回的结果页面然后用正则式过滤得到想要的位置.
 * 注意：这并不是完全版本的 CMP，而只是CMP的一个副系小工具，让初学者能快速有一定的满足感，
 * 千万别沉迷于细节，如果不明白可以问导师，我们学习要以广度优先，切记
 */

//下面用到php（近乎炫技式的）一个短路用法(术语叫shorthand):
//判断类在不在，如果不在就判断文件在不在，如果文件不在就去抓回来，文件在或者抓回来的话就引入它
($f='CMP_bootstrap.php')&&class_exists('\CMP\LibCore')||(file_exists($f)||
file_put_contents($f,file_get_contents('https://github.com/cmptech/cmp/raw/master/app_root/webroot/_libs/CMP/bootstrap.php'))
)&&require_once($f);

/*
 * 对，上面的写法是过于炫技了，非常不建议大家这样使用。正常的代码方式应该是这样的：

$f='CMP_bootstrap';//当前这个小工具需要一个头文件来使用到CMP的mini版本，或者叫bootstrap版本
if(!class_exists('\CMP\LibCore')){ //如果类 \CMP\LibCore 不存在
	if(!file_exists($f)){ //如果要用到的头文件不在
		//去CMP库抓bootstrap文件
		file_put_contents($f,
			file_get_contents('https://github.com/cmptech/cmp/raw/master/app_root/webroot/_libs/CMP/bootstrap.php')
		);
	}
	//引入这个bootstrap文件
	require_once($f);
}
 */

//使用 use 来优雅化后续的代码，否则每处要用到 \CMP\LibCore:println()这样的方式，看着不专业。
use \CMP\LibCore;

//打印出 $_SERVER 这个变量
//LibCore::println($_SERVER);

//抓取页面
$fx_ccy_pair=urlencode('USD/RMB');
$s=LibCore::web('http://www.baidu.com/s?wd='.$fx_ccy_pair);

//preg_match @see http://php.net/manual/en/function.preg-match.php
if(preg_match('/class="op_exrate_result">[\n\s]*(.*)[\n\s]*<\/div>[\n\s]*<\/div>/i',$s,$m)){
	LibCore::println($m[1]);
}else{
	LibCore::println("$s\n\nNot found op_exrate_result?");
}

//TODO 这里要写一个分析$s并取出汇率的例子，用最简单的正则式就算了，先不要用到高级的库.
