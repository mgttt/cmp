<?php
require_once "../cmp_demo/inc.app.php";

println("<pre>");

setConf("flag_rb_freeze",false);//先进入非冻结模式，这里RedBeanPHP可以自动生成结构。具体实现见 rbWrapper/RedbeanPHP类

//$rb=new rbWrapper4("sqlite:"._APP_DIR_.DIRECTORY_SEPARATOR."../test_sqlite.db");
#$dsn="db_app";//see test mysql

//$dsn="sqlite:".__DIR__."/test_sqlite.db";//PASSED
//$dsn="sqlite:"._TMP_."/test_sqlite.db";
//$dsn="sqlite::memory:";//KO: 这个 不是进程内享的，试出来如果 在不同的句柄打开的话，其它是不同的内存块！
//$dsn="sqlite:memory:;cache=shared";//好像OK

//见 ../cmp_demo/_conf.{$config_switch}/
$dsn = "db_app";
#$dsn = "db_local";

//$rb=new rbWrapper4($dsn);//这个是更底层的用法，即连 Orm层都不需要。。。一般不这样用 :)
$rb=new OrmTester($dsn);

//生成 book 对象。注意这时还没保存的
$book = $rb->dispense( 'book' );//先测试一下 RedBean级别的语法
#$book = $rb->dispenseBean();

//随便搞些字段，得益于 RB的特性，我们不需要去设计 book这个表和字段，都是自动生成的
$book->title='Title-'.my_isoDateTime();
$book->rate=rand(1,10);
#$book->id=2;
$id=$rb->store($book);//保存
println("id=$id");
println("book=".var_export($book->export(),true));

##example 2
$book2=$rb->load('book',$id);
println("book2=".var_export($book2->export(),true));

##example 3
class OrmBook
	extends OrmTester
{
	var $NAME_R="book";
}

$book_cls=new OrmBook($dsn);
$book3_a=$book_cls->loadBeanArr($id);//加载 id=$id这个记录(bean)并获得 数组（即非readbean对象）
println("book3_a=".var_export($book3_a,true));

## 玩一下 PageExecute函数，感受下分页函数.
########## 列出头7个
if(true){
$rsa=$book_cls->PageExecute(array(
	"SELECT"=>"*",
	"FROM"=>"book",

	//TEST 2
	"pageSize"=>7,
	"pageNumber"=>1,

)//,7 TEST 4
);
}else{
//TEST 3
$rsa=$book_cls->PageExecute(array(
	"SELECT"=>"*",
	"FROM"=>"book",
	"LIMIT"=>7,
));
}
use \CMP\LibBase;
//println("PageExecute=".var_export($rsa,true));
println("PageExecute=".LibBase::o2s($rsa));
//println("count(*)=".var_export($book3_a,true));

println("记得每次执行都用sqlyog之类的工具看看数据库的变化");

