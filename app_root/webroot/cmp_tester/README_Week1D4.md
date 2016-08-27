# 第二章 CMP初体验

## 测试数据库联动性
cmp_test/test_db_time.php  即（
命令行执行 php cmp_test/test_db_time.php
或者
浏览器访问 http://cmpdemo.applinzi.com/cmp_tester/test_db_time.php
)
* 继续感受命令行下及兼容WEB访问的测试方法
* 继续感受getConf()的简单用法、 几个 跟时间日期有关的粗略方法、做下数据库的联通性测试
* 这里的 Orm还只是先测试下联通性，下面继续 Orm的初体验：

### 先跟管理组拿一个sqlyog绿色版，用于数据库访问和管理。当然可以用不sqlyog改为用自己喜欢的工具；

## ORM 初体验
test_simple_orm.php

* 把上面搞懂，基本上就明白了 Orm层/rbWrapper（RB）封装类/RedBeanPHP类 的基本关系
* test_simple_orm.php 用的是 cmp_demo/_conf.{$config_switch}/ 中的配置 db_app 设定的数据库（在aliyun设置的，专门给cmp_demo做培训用的
* 另外还有一个 test_sqlite.php版本，这里用的 $dsn 用的是本地的 _TMP_ 中的 sqlite 文件数据库 （注意在 SAE上应该跑不到，只能在本机的XAMPP下面玩一下，因为 SAE把 sqlite函数都屏蔽了）



