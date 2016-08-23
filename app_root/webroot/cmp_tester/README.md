# 学习顺序 （随时更新）

## cmptech.info 下载并阅读:
* 【CMP框架之设计哲学及代码约定】与【CMP-4Layers.ppt】
  理解核心约定、接口规范、URL重写优雅方式、四层设计、代码命名规范等
* CMP开发流程201608

## 【代码阅读】 用git检出（github源码【当成是LIVE-SRC】、cmpdemo@SAE【当成是 UAT/DEMO/LOCALDEV】）到本地

### 学习 BeyondCompare4使用 （以后缩写为 bc 推荐软件，或者个人自己选择第三方软件）， 把上述本地的两个源码版本进行对比（理论上是差别不大的，一般UAT测试过了才上传，具体参考上面提到的 【CMP开发流程】

### cmp_demo/ 代码简单阅读 （注意广度优先，只是浅尝式阅读即可）

## 本地新增 cmp_tester_XXX/ 复制和参考 cmp_tester/ 目录，然后一步步做以下的测试
* $app/test/test_php_time.php
  分别在1，命令行下 php test_php_time.php 看输出结果，
	2，在浏览器入口访问 http://$website/$path/$app/test/test_php_time.php
	感受好的框架是可以同时支持 命令行模式（适合写console式程序）和Web模式

