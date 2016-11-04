
# CMP推荐 GIT开发基本流程


* 强烈建议使用 sourcetree git图形客户端；

* 假设需要开发某特性【$ZZZ】时，把 【远程dev分支】checkout到本地【$who-local-$zzz分支】。这里 $who 表示姓名缩写比如 wjc， $zzz表示特性缩写比如 f_login。 当然直接 【$who-local-dev】也行。。。

* 在这条分支上开发特性，自己本地测试过没问题后，可以commit，记得不要马上push！ 

接下来如果需要提交代码到远程目标分支时，合并代码的流程建议按下面做：

  > 1, 先把【远程目标合并分支】（比如远程dev分支）再次checkout最新的到本地如【$who-local-latest】
  >
  > 2, 在sourcetree双击这个刚checkout出来的分支【$who-local-latest】（双击相当于checkout 切换过去），然后右键自己的分支【$who-local-$zzz】选择 （合并到 $who-local-latest。 正常下不用解conflict，有的话就自己解一下）
  >
  > 3, 然后在【$who-local-latest】做 commit。特别注意：要很小心每个文件都预览清楚！！ 然后commit成功后可以 push 远程目标分支了
  >
  > 4,  上述成功后，可以随手把本地分支pull到最新、或者随手删除也行（比如说刚才的【$who-local-latest已经可以删除】）。删除是最爽的，有要开发时开新分支开发就好。  ** 也可以本地留一条 【$who-local-latest-ro】只用来不停的 pull到最新
  
# Branches

### dev

主开发分支

### vX.X.X

阶段性（发布）版本
（配置文件不发布。。。）

## 小脚本

#### 
