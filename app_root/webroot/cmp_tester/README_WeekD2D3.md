# 培训第二周 周二与周三 2天的内容 （到时根据实际情况来判定要否加到三天的量）
建立
WebTester{$Devname}.php

编写
WebTester{$Devname}.JeasyUI_CaseXXX()
=>调用模板
test{$devname}.JeasyUI_CaseXXX.htm

XXX 是指根据
http://www.jeasyui.com/demo/main/index.php
中的例子
来做测试用例。其中要用到的数据接口，不要用静态文件，要自己编写对应的
ApiTester{$Devname}中的方法接口来获得，不一定要用到数据库ORM，用简单的
dummy-hardcoded 数据返回就可以！（后面自然会用真的数据做测试数据接口）

* 特别要测试的控件包括：
Tab
Dialog
Messanger
Combo
ComboBox
SearchBox
PasswordBox
Calendar
DateBox
DateTimeBox
Form
DataGrid
TreeGrid
Application
【提示】
我们实际后台应用跟数据交互最多的就是 Grid/Dialog，所以才根据经验定制了两个封装
后面还有一个 DateBox的 Hack（根据我们用户体验去hack的一个输入时间box）
以及一个 Combo/ComboBox的 Hack，配合后台数据，调出之前的高频录入数据提供下拉

* 测试目的：
通过对模板、数据接口的编写考察大家对
前端（暂时用jeasyui库）与后台数据接口的联通，同时熟悉基本的前端库的使用；


