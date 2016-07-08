<?php
/**
 * BPM(Business Process Manager) 业务流程管理器
 *
 * @ref
 * 0, https://en.wikipedia.org/wiki/Business_Process_Model_and_Notation
 * 1, Camunda BPM, http://bpmn.io/ 
 * 2, bpmeditor-ifdl (from BPMX)
 */
class LgcBpm
{
	const BIZ_PROC_VER="20160125";

	//返回 BPM 对象，如果bpm_id为空就新建?
	public static function getBPM($bpm_id){
		$bpm = new AppBP;
		//TODO 
		//Build Context:
		return $bpm;
	}

	//把ifdl文件编译成 bpm 数据结构
	public static function compile_ifdl_to_bpm($ifdl_file){
	}
	//把 http://bpmn.io/ 文件编译成 bpm 数据结构
	public static function compile_camunda_to_bpm($camunda_file){
	}
	////返回 BPM 数据
	//public static function getBPM($bpm_id){
	//}
	public static function ListBPM($param){
		//查询 BPM 列表，难点应该在 "状态"，因为是非线性的啊
	}

	public static function GetVersion(){
		return self::BIZ_PROC_VER;
	}
}

class OrmBPM
{
}

//BPM 封装类
//TODO 最难的是 fake-thread-pool 
//如果没有异步启动，当前一个Thread就够，可以理解为 Core Thread。
//但如果有异步启动，就会有新的 Thread要处理，很容易会爆 Pool.
//什么情况下会有新Thread, 网关出来时如果是 多个的，而又不是 XOR的话，就会起新Thread走新Tick
class AppCtxBP
{
}
class AppBP
{
	//$bpm_context;

	//事件处理
	public function OnEventArrive($event_obj){
	}
	public function Lock(){
		//尝试锁当前 BPM
		//如果当前BPM已经锁定而且在合理时间内，返回BUSY
	}
	//流程下一轮处理.返回状态是非常重要的.
	//最难的地方...
	public function nextTick(){
		$rst_lock = $this->TryLock();
		if($rst_lock == false){
			//返回 BUSY 信号, 用户端需要重试.
		}

		//找出非 等待 用户任务、及

		$curFlowObj = $this->getCurrentFlowObject($bpm_id);
		if ( $this->is_gateway($curFlowObj) ){
			$gateway = new BpmGate($curFlowObj, $ctx);
			$rst_gateway = $gateway->exec();//move current to next....?
			$this->stack($rst_gateway);
		}elseif( $this->is_task($curFlowObj) ){
			$task = new BpmTask($curFlowObj, $ctx);
			$rst_task = $task->exec();//move current to next....?
			$this->stack($rst_task);
		}elseif( $this->is_Event($curFlowObj) ){
			$event = new BpmEvent($curFlowObj, $ctx);
			$rst_event = $event->exec();//move current to next....?
			$this->stack($rst_event);
		}
		$this->TryUnlock();
	}

	//执行最多 n 轮 tick。不能一直 loop，怕死循环.
	public function loopRounds($n){
		for($i=0;$i<$n;$i++){
			$this->nextTick();
			//TODO 
			//查最新状态
			//确定是否需要跳过
		}
	}

	public function queryStatus(){
		//返回当前状态 !!!
		//如果线性的非常容易处理，如果有同步异步非常麻烦, TODO
	}

	//Thread Pool
	//public function ThreadPool(){
	//}

}

/**
BPMN 在程序中的定义方式
数据结构：
为使能够兼容传统SQL，数据结构应该是基本二维数组，形如：
[ $bpm_element, ] 其中 $bpm_element
	{
		bpm_uuid, //bpm uuid
			bpm_id, //业务id，注意跟uuid区别，一个 bpm_id可能会有几个 uuid吗？ 待定...
			bpm_type, //主类型 ActivityTask | ActivitySubProcess | Gateway | Events | Connecting | Swimlanes | Artifacts | Thread
			bpm_subtype, //子类型:
			//gateway: XOR | AND | OR, Parallel | Exclusive | Inclusive, 暂时未确认勾兑关系: XOR应该是 Exclusive，AND似乎应该是Inclusive，OR似乎是Parallel
			//activity: Task | SubProcess | SequenceFlow | MessageFlow | Association
			//Task: UI(UserInteractive) 人机互动 | Manual/Offline Task 线下 有可能连接子流程 | Service API形 | Script 脚本任务，兼容外部子流程？
			//Connecting: SequenceFlow | MessageFlow | Association
			//Swimlanes:  Pool | Lanes
			//Artifacts: Group | Text Annotation
			//Thread: 
			bpm_attributes, //如果是redis类非sql可以展开直接保存?
	}
*/

/** 基础概念
Process 流程
Activities 活动；在工作流中所有具备生命周期状态的都可以称之为"活动"，如任务（Task）、流向（Sequence Flow），以及子流程（Sub-Process）等; 每一个activity代表一个特定的业务，比如说登陆，注销等等都可以是一个activity。
Task很明显就是单一的业务活动。
sub-process是一个复合的业务活动，在该活动中存在一个子业务流程。
Sequence Flow 序列流; 实线实心箭头表示，代表流程中将被执行的活动的执行顺序。
Message Flow 消息流; 用虚线空心箭头表示，用来表示2个分开的流程参与者（业务实体或业务角色）之间发送或者接收到的消息流
Association 结合关系 点状虚线表示，用于显示活动的输入输出。

Gateway 网关，用来控制流程的分支和聚合; 我们暂时先根据当前有的工具实现 AND/OR/XOR
用来决定流程流转指向的，可能会被用作条件分支或聚合，也可以被用作并行执行或基于事件的排它性条件判断

Events 事件;—在BPMN2.0执行语义中也是一个非常重要的概念，像启动、结束、边界条件以及每个活动的创建、开始、流转等都是流程事件，利用事件机制，可以通过事件控制器为系统增加辅助功能，如其它业务系统集成、活动预警等

=关于BPMN2
BPMN2.0相对于旧的1.0规范以及XPDL、BPML及BPEL等最大的区别是定义了规范的执行语义和格式，利用标准的图元去描述真实的业务发生过程，保证相同的流程在不同的流程引擎得到的执行结果一致。

http://www.uml.org.cn/workclass/201206272.asp
=1.Flow Objects 流对象
1.Events 事件
2.Activities 活动
3.Gateways 网关

=2.Data 数据
1.Data Objects 数据对象
2.Data Inputs 数据输入
3.Data OutPuts 数据输出
4.Data Stores 数据存储

=3.Connecting Objects 连接对象
1.Sequence Flows 序列流
Sequence Flows 用实线实心箭头表示，代表流程中将被执行的活动的执行顺序.
2.Message Flows 消息流
Message Flows 用虚线空心箭头表示，第阿宝2个分开的流程参与者直接发送或者接收到的消息流.
3.Associations 结合关系
Associations 用点状虚线表示，用于显示活动的输入输出.
4.Data Associations 数据结合关系

=4.Swimlanes 泳道
1.Pools 池
2.Lanes 道

=5.Artifacts 工件
1.Group 组
2.Text Annotation 文本注释

=BPMN2-Diagram Types 图类型
1.Private Processes 私有流程
2.Public Processes 共有流程
3.Choreographies Processes 组合流程

*/

/** 设计点滴
一、Gateway 操作汇聚
AND表示等齐多路结果然后做判断
OR表示其中 N/M 路结果到达之后即可做判断
其中 1<=N<=M
二、Gateway 进行分支，其中分支分同步异步，
同步主要是XOR(异或排它型)(其它同步如顺序型要么用子流程代替、要么拆分为顺序型任务序列，所以不需要其他）
异步类似开新进程或线程进行异步执行，所以完全用 AND/OR 网关即可。后两者又可以用 平行网关 概念??

*/

/** 学习笔记
难点主要在于Gateway，因为有很多种模型，包括汇聚时是要等全部还是等部分，出去时是并发还是先后还是只是分支...  Gateway在BPMN是个很复杂的概念。它包括好几个种类：Data-based exclusive，Event-based exclusive 排他，Inclusive，Complex，Parallel并行等。不同的gateway的组合使用有时候可能会产生合法性问题。所以在使用的时候我们要格外小心。

三类执行语义的定义涵盖了业务流程常用的Sequence Flow（流程转向）、Task（任务）、Sub-Process（子流程）、Parallel Gateway（并行执行网关）、Exclusive Gateway（排它型网关）、Inclusive Gateway（包容型网关）等常用图元:
http://www.infoq.com/resource/articles/bpmn2-activiti5/zh/resources/image1.png

现实业务所有的业务环节都离不开Activities、Gateways和Events，无论是简单的条件审批还是复杂的父子流程循环处理，在一个流程定义描述中，所有的业务环节都离不开Task、Sequence Flow、Exclusive Gateway、Inclusive Gateway（如图1中右侧绿色标记所示元素），其中Task是一个极具威力的元素，它能描述业务过程中所有能发生工时的行为，它包括User Task、Manual Task、Service Task、Script Task等，可以被用来描述人机交互任务、线下操作任务、服务调用、脚本计算任务等常规功能。
User Task:生成人机交互任务，主要被用来描述需要人为在软件系统中进行诸如任务明细查阅、填写审批意见等业务行为的操作，流程引擎流转到此类节点时，系统会自动生成被动触发任务，须人工响应后才能继续向下流转。常用于审批任务的定义。
Manual Task:线下人为操作任务，常用于为了满足流程图对实际业务定义的完整性而进行的与流程驱动无关的线下任务，即此类任务不参与实际工作流流转。常用于诸如物流系统中的装货、运输等任务的描述。
Service Task:服务任务，通常工作流流转过程中会涉及到与自身系统服务API调用或与外部服务相互调用的情况，此类任务往往由一个具有特定业务服务功能的Java类承担，与User Task不同，流程引擎流经此节点会自动调用Java类中定义的方法，方法执行完毕自动向下一流程节点流转。另外，此类任务还可充当“条件路由”的功能对流程流转可选分支进行自动判断。常用于业务逻辑API的调用。
Script Task:脚本任务，在流程流转期间以“脚本”的声明或语法参与流程变量的计算，目前支持的脚本类型有三种：juel（即JSP EL）、groovy和javascript。在Activiti5.9中新增了Shell Task，可以处理系统外部定义的Shell脚本文件，也与Script Task有类似的功能。常用于流程变量的处理。
*/

/** https://en.wikipedia.org/wiki/Business_Process_Model_and_Notation
 *
 *
 *
 */














