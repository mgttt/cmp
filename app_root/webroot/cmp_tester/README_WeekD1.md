# 成都小象 工作内容计划 (2016.8.29)

============================
## 建立个人测试目录.
在 cmpdemo@SAE 的 repo中建立 //cmp_tester_{$devname}/
其中 {$devname} 是名字的英文缩写

【注意】为方便描述，以下用变量 {$_DEV_HOME_} 代替 //cmp_tester_{$devname}/

写一个 //{$_DEV_HOME_}/README.md
然后操作 git add + commit + push。 记得要有好的注释.

【参考】//git.tips.txt  是wanjo随手做的笔记，关于在 命令下如何上传文件到SAE master:1的
【注意】不要马上做下面的任务，先上传。我要看到有上传记录！！！ 

============================
## 命令行与WEB联通的测试程序

参考 cmp_tester/ (的部分文件，不要简单复杂粘贴！！！）
分别建立 //{$_DEV_HOME_}/下面的几个文件：

假设{$Devname}是驼峰法命名的名字缩写，比如 Cyz
AppTester{$Devname}.php
OrmTester{$Devname}.php

然后编写
{$_DEV_HOME_}/test_my_biz.php

命令行下运行 php test_my_biz.php

要求得到结果是：
每次执行都提交会新增一个记录；
该orm至少新增几个属性字段：remark、type、test_float、test_int、test_string、test_date、test_datetime等
  其中 test_float 要求是随机的 浮点数
自己这个 orm表的 最新记录大小；
取出该orm中最新的三行；
计算该Orm中最新 100行的 test_float的平均数；最新的意思是用字段 lmt，不是用ID倒序。自己理解 lmt字段是如何被ORM_Base封装实现的
计算该Orm中最新 100行的 test_float的方差【方法一】；
计算该Orm中最新 100行的 test_float的方差【方法二】；
注意上面【方法一】【方法二】都要在APP层完成代码
方法一是指用 纯sql 实现；
方法二是指在Orm层先取 100个后再在App层的PHP 计算

** 这个测试主要是要：
看大家对命令行启动测试程序有否正确理解；
看大家对使用测试程序 直接驱动 App/Orm 层有否正确理解；
测试大家对写简单计算的编码能力；

============================
## 测试 API 接口的测试程序
分别建立 //{$_DEV_HOME_}/下面的几个文件：
ApiTester{$_DEV_HOME_}.php
LgcTester{$Devname}.php

其中 Api=>Lgc=>App=>Orm层，Api新增几个接口函数
GetOrmCount() 命令行远程方式 获得自己Orm的记录数；
GetOrmAvg100() 命令行远程方式 获得最新的100的平均数
GetOrmSdt100Way1() 命令行远程方式 获得方法一计算的最新的100的方差
GetOrmSdt100Way2() 命令行远程方式 获得方法一计算的最新的100的方差

然后编写
{$_DEV_HOME_}/test_my_biz_remote.php
对上述几个接口函数进行远程呼叫测试
* 【提示】所谓远程调用可参考下 //cmp_demo/test_cmpdemo_sae_pingpong.php

程序程序测试目标：
看大家对使用命令行写的简单客户端对原程WEB API的简单呼叫的理解是否正确；
看大家对Api/Lgc/Biz/Orm理解是否真正到位；

完成后把今天的工作提交，写好日志，等待QA

理论上提交后还要再测试远程结果是否正确
