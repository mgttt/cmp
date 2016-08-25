# 第三章 CMP 入门（前端模板基础、呼叫流、四层设计策略、SQL构建等)

## CMP WEB前端=>后端联通性体验
* 浏览器打开本地或UAT，访问 //cmp_tester/
如果遇到 404 config.switch.tmp 表示【配置文件开关器】未处理；
预期能看到正常网页入口。

** 如果能正常打开，新建 //cmp_tester_{$你的名字首字母缩写}/
这样方便在自己的空间随便测试、以及稍后联系从自己目录 用 beyond compare 与 //cmp_tester/ 联系文件比较、同步等。
*** 注意 beyondcompare 要学会配置：不修改换行符、默认使用 UTF8

## 看代码，了解以下这条流：
入口 index.php => cmp::handleWeb() => WebCmpTester.DefaultIndex() => tester.DefaultIndex.htm

** 看 tester.DefaultIndex.htm 感受一下两个最简单的模板用法
{$变量}
{if 条件}
{/if}

** 通过观察第一个LINK的内容，结合【CMP设计哲学】感受一下 全URL模式 index.php?_c=$_c&_m=$m 和 优雅模式 $c.$m.api

## 看代码，了解以下这条流
WebCmpTester.MiniAjaxCmp.api => WebCmpTester->MiniAjaxCmp() => tester.MiniAjaxCmp.htm
=> 第一个链接 => TestPingPong() =>(用mini ajax)=> ApiTester.PingPong.api =>返回页面、显示

* 上面这条流主要是感受 从 WEB入口到加载模板生成页面；从页面UI通过mini.ajax发起API请求；API返回结果的简单处理；

** 阅读 tester.MiniAjaxCmp.htm 的其它几个LINKS 和对应的 API/LGC/APP/ORM 层，感受 CMP的四层逻辑与结构策略。

** 特别感受 APP【除数据层以外的业务逻辑...尽量不直接操作SQL】、ORM【相当于 数据操作层，除操作 REDBEAN还会有些直接的 SQL操作】
** 特别感受 ORM层中的 SearchList()的 【SQL构造策略】

*** 不要太计较 Edit/Add那里的 JS算法，都是些之前临时弄的（以后安排完善），实际项目时我们会用到些相关的配合JS；

## 至此，已经走完了一个相对完整的 四层模型，含增删查改。

## 接下来是自学 jeasyui （注：jeasyui这里的例子未完善，以后有机会再安排回顾时完善。。。）
http://jeasyui.com/

## 然后就是投入实际项目中慢慢丰富知识。

### 以后经验丰富之后，请回来协助 CMP的 文档、案例的完善（加入 //github.com/cmptech/cmp/ 后做 pull request，或者直接提交到 SAE上面的UAT，由管理员定期比较和同步去 github/cmptech/cmp/）

谢谢！！

