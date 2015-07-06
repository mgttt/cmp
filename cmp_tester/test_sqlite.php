<?php
require_once "../cmp_demo/inc.app.php";

setConf("flag_rb_freeze",false);//before new the rb, important before the database creation

//$rb=new rbWrapper4("sqlite:"._APP_DIR_.DIRECTORY_SEPARATOR."../test_sqlite.db");
#$dsn="db_app";//see test mysql
$dsn="sqlite:".__DIR__."/test_sqlite.db";
$rb=new rbWrapper4($dsn);

$book = $rb->dispense( 'book' );
//$book2=$rb->load(1);
#$book = $rb->dispenseBean( 'book' );
$book->title='what'.rand();
$book->rate=rand(1,10);
#$book->id=2;
$id=$rb->store($book);
println("id=$id");
println("book=".var_export($book->export(),true));

##example 1
$book2=$rb->load('book',$id);
println("book2=".var_export($book2->export(),true));

##example2
class ORM_book
	extends rbWrapper4
{
	var $NAME_R="book";
}

$book_cls=new ORM_book($dsn);
$book3_a=$book_cls->loadBeanArr($id);
println("book3_a=".var_export($book3_a,true));

