
# 推荐 基本GIT开发流程

( 如果按照这个流程将几乎不会有任何大的问题 :)

* 强烈建议使用 sourcetree git图形客户端；

* 假设需要开发某特性【$ZZZ】时，把 【远程dev分支】checkout到本地【$who-local-$zzz分支】。这里 $who 表示姓名缩写比如 wjc， $zzz表示特性缩写比如 f_login
* 开发完 $zzz 特性自己本地测试过没问题后，commit，记得不要马上push。  接下来如果需要提交代码到远程目标分支时，合并代码的流程建议按下面做：

  > 1, 先把【远程目标合并分支】（比如远程dev分支）再次checkout最新的到本地如【$who-local-latest】
  >
  > 2, 在sourcetree双击这个刚checkout出来的分支【$who-local-latest】（双击其实相当于checkout切换过去），然后右键自己的分支【$who-local-$zzz】选择 （合并到 $who-local-latest）
  >
  > 3, 这时在【$who-local-latest】做 commit，特别注意要很小心每个文件都预览清楚！ 这里 commit成功后可以做push到刚才的远程目标分支了
  >
  > 4,  上述成功后，可以随手把 分支pull到最新，或者随手删除本地没用的分支（比如说刚才的【$who-local-latest已经可以删除】；反正要做什么事随时checkout出来做feature
  
# Branches

### dev

主开发分支

### vX.X.X

阶段性（发布）版本
（配置文件不发布。。。）
