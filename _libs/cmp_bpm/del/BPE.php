<?php
/**
 * B - 业务 Biz/Business
 * P - 流程 Proc/Process
 * E - 引擎 Engine
 */
class BPE
{
	//Biz Proc Instance
	//新建"业务流程实例"
	public static function newBP(){
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

	//send alert to rnd
	public static function SendAlertRND($msg){
		self::SendEmail($conf_sys_alert_email, $msg);
		self::SendSms($conf_sys_alert_phone, $msg);
	}

	//Cron Job Entry
	public static function CronJobEntry(){
		//tell remote api "check and run"
	}
	//continue the bp
}

/**

	从界面、user task 入口 => Lgc 层 
	=> BPE 新建 BP 或者 继续 BP //参数如何传送？直接丢进去让Task自己跟进?
	// 要检查权限（在LGC还是在BPE？）
	=> 返回 BID

	BPE:
	$task => BPE->locateTask(。。。);//
	BPE->continue($bp,$param);//把参数丢进去?

继续思考：
	假设先不要考虑 context的存取开销，从用户任务到脚本任务的呼叫都改为堆栈？

	API=>LGC( BPE->pleaseCall($bp, $taskID) return OK/KO ? )
	return OK是指已经成功放到BPE（BPE应该显式给予回复，并建议多久返回？？？)
	
	$rs=pushTask($bp, $taskID);
	//如果推送失败需要报告前台。推送完轮询结果呢？

	//结果如何搞？
	->queryTaskResult($bp, $taskID);
	返回的结果，（是否完成、什么时候完成、进入过的次数。。。）


		//BPE::kickoffChecker();这个名字不好哦

		//Cronjob 每1-2秒 用WEB接口 测试下BPE是否在正常工作（要正常返回OK值才算），如果返回错误连续3次就要发出alert(SMS + EMAIL)
		//Cronjob=>Web(ApiBPE.checkAndStart())=>
		//1,check auth with IP //好像又不太需要，因为谁都可以疯狂来触发，不要出错就好.所以就算要check auth，简单check一下IP就可以了.
		$BPE->checkAndStart();
	//=> 
	//1，判断有多少个任务需要处理、其实主要是多少个 BP需要处理。
	//2，看看相应的WORKER是否在（矛盾：要不要用WSW方式，好像又不是很健康？）
	// 非常难点，如何启动和关闭相应的worker？
	// 问题：WEB 一次性、会超时，有启锁未必一定能闭锁
	// * 想保证解锁，需要二次呼叫，所以这个 checkAndStart 好像非常重要，涉及到调度
	// checkAndStart {
	//	轮BPs 判断 BP，然后锁 BP，呼叫远程（自身），如果有返回而且OK就解锁，不OK好像也要解锁
	//	如果锁失败，就表示可能已经有WORKER在跟进，先跳过 ** 但是要检测异常奇怪的，以便人工解锁 （在人工界面那里处理）
	// }
	//X，返回状态 + 少量信息（或者工作器信息）
	*
	* ！！！ 这个是指系统运作检测，所以不要有太多操作！！！ 操作改为给 TR ！！！
	* BP 工作队列人工检测状态（【微信登录】，方便一点）功能页
	* => 当前工作列表（待处理）、所属BP、（锁状态）、BPW、BP进行时间、操作（BP View （含Task View）、BP锁重置（锁重置才有可能被下一任的BPW给继续跟进））
	* => 系统负荷统计
	* => BP查询、工作量查询等
	* 超时的需要回收吗？不需要
	* 这里的列表应该把BP锁（即有BPW工作中）的放最上面，其它的似乎都可以全部搬给 TR来跟进
	*
	* ** C2C/A2C这种属于等待人工任务的BP会一直放在上面，不能随便重置啊）
	*
CronJob 频繁？轮询？=> 没所谓，只是 checkAndRunBPE, check返回状态（LAUNCH/BUSY/FREE/ERROR)
API.checkAndRunBPE{
	判断【BPE锁】是否还活着//要通过测试确认1（锁不在进程就不在，跨多WEB实例所以不能用简单进程锁）
1		如果【BPE锁】已经在，向主工作进程问询状态（BUSY/FREE）并返回，其它状态一律返回ERROR
2		如果【】不存在，当前进程就是主进程，马上先做锁（妈蛋，那怎么返回状态？）
进入工作循环（
检查任务
）
}
** 进程锁实现：TODO
	要跨区，所以需要一个中间位置，恐怕除了 sql就只能是redis了，要再想办法啊，而且要注意进程退出时锁还要能解（不能解也要有超时退出机制）。。。
	参考机制：
	http://blog.csdn.net/ugg/article/details/41894947
	http://blog.jobbole.com/95156/
 */

/** 整理
*
LIVE—PRODUCTION 环境下造个驱动器不难，在SAE环境下如何弄？（从外部叫SAE） check and run的手法是不良的。
一、改为 manually launch 手动启动（只实现一个应该就可以？因为它只是个触发器而已，不做复杂业务！！）
** 部署在 73.8 上面，分别是 bpe_trigger_dev.sh 和 bpe_trigger_live.sh，另外在SVN里面放一个 bpe_trigger_localdev.bat
* 每次进入时、循环工作时、都用ORM向应用全局锁做个checkin
二、

 */



























