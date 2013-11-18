<?php
// +----------------------------------------------------------------------
// | Fanwe 多语商城建站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: awfigq(67877605@qq.com)
// +----------------------------------------------------------------------

// 亿美短信平台
include_once("Sms.class.php");
include_once(ROOT_PATH."ThinkPHP/Vendor/Sms/Emay/Client.php");

class EmaySms implements Sms
{
	public $sms;
	public $message = "";
	public $apitype = 1;
	
	public $statusStr = array(
		"10"  => "客户端注册失败",
		"11"  => "企业信息注册失败",
		"17"  => "发送信息失败",
		"18"  => "发送定时信息失败",
		"305" => "服务器返回了错误的数据，原因可能是通讯过程中有数据丢失",
		"306" => "发送缓存太小，无法把数据保存到缓存中",
		"307" => "下行目标号码不符合规则，手机号码必须是以0、1开头",
		"308" => "某函数的参数必须为数字,此为非数字错误 如: 在注册或修改密码时出现此异常则说明 密码必须为数字",
		"999" => "操作频繁，有些接口是不能多次调用的",
		"998" => "由于网络问题导致信息发送超时失败",
		"101" => "服务端网络无法连接到sdk服务器",
		"996" => "短消息序列号错误,序列号非数字，或则不足8位"			   
	);
	
    public function __construct($smsInfo = '')
    { 	
		if(!empty($smsInfo))
		{
			set_time_limit(0);
			
			if(intval($smsInfo['config']['apitype']) > 0)
				$this->apitype = intval($smsInfo['config']['apitype']);
		
			$this->sms = new Client($smsInfo['server_url'],$smsInfo['user_name'],$smsInfo['password'],$smsInfo['password'],false,false,false,false,2,10);
			$this->sms->setOutgoingEncoding("UTF-8");
		}
    }
	
	public function sendSMS($mobiles=array(),$content,$sendTime='')
	{
		$mobileLen = 1000;
		$messageLen = 70;
		if($this->apitype == 2)
			$messageLen = 500;
		
		$contentLen = mb_strlen(utf8ToGB($content),"GBK");
		$smsTotalCount = ceil($contentLen / $messageLen) * count($mobiles);
		
		$mobileList = array_chunk($mobiles,$mobileLen);
		$successNum = 0;
		
		foreach($mobileList as $mobileItem)
		{
			$smsCount = ceil($contentLen / $messageLen) * count($mobileItem);
			$statusCode = $this->sms->sendSMS($mobileItem,$content,$sendTime);
			
			$smsLog['send_content'] = $content;
			
			$mobile = implode(",",$mobileItem);
			$smsLog['send_mobiles'] = $mobile;
			
			$smsLog['success_mobiles'] = "";
			$smsLog['fail_mobiles'] = "";

			if($statusCode!=null && $statusCode==0)
			{
				$smsLog['success_count'] = $smsCount;
				$smsLog['expense_count'] = ceil($contentLen / 70) * count($mobileItem);
				$smsLog['fail_count'] = 0;
				$smsLog['action_message'] = "发送成功";
				$successNum += $smsCount;
			}
			else
			{
				$smsLog['success_count'] = 0;
				$smsLog['fail_count'] = $smsCount;
				$smsLog['expense_count'] = 0;
				$smsLog['action_message'] = $this->statusStr[$statusCode];
			}
			
			$smsLog['send_time'] = gmtTime();
			
			if(intval($GLOBALS['db']->getOne("SELECT val FROM ".$GLOBALS['db_config']['DB_PREFIX']."sys_conf WHERE name='SMS_SEND_LOG'")) == 1)
			{
				$sql = "insert into ".$GLOBALS['db_config']['DB_PREFIX']."sms_send_log (class_name,send_content,success_count,success_mobiles,fail_mobiles,expense_count,fail_count,action_message,send_time) values('Emay','".$smsLog['send_content']."','".$smsLog['success_count']."','".$smsLog['success_mobiles']."','".$smsLog['fail_mobiles']."','".$smsLog['expense_count']."','".$smsLog['fail_count']."','".$smsLog['action_message']."','".$smsLog['send_time']."')";
				
				$GLOBALS['db']->query($sql);
			}
		}
		
		if($smsTotalCount > $successNum)
		{
			$this->message = $smsTotalCount."条短信中，有".($smsTotalCount - $successNum)."条未成功发送到手机".implode(",",$mobiles);
			return 0;
		}
		else
		{
			$this->message ="成功发送短信【".$content."】，到手机".implode(",",$mobiles);
			return 1;
		}
	}
}
?>