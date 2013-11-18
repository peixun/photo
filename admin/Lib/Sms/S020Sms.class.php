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

// 020短信平台
Vendor('transport');

class S020Sms implements Sms
{
	public $config = array(
		"ecode"=>"",
	);
	
	public $message = "";
	public $smsInfo;
	
	public $statusStr = array(
		"1"   => "发送成功",
		"-1"  => "不能初始化SO",
		"-2"  => "网络不通",
		"-3"  => "一次发送的手机号码过多",
		"-4"  => "内容包含不合法文字",
		"-5"  => "登录账户错误",
		"-6"  => "通信数据传送",
		"-7"  => "没有进行参数初始化",
		"-8"  => "扩展号码长度不对",
		"-9"  => "手机号码不合",
		"-10" => "号码太长",
		"-11" => "内容太长",
		"-12" => "内部错误",
		"-13" => "余额不足",
		"-14" => "扩展号不正确",
		"-50" => "配置参数错误"	   
	);
	
    public function __construct($smsInfo = '')
    { 	
		if(!empty($smsInfo))
		{
			set_time_limit(0);
			
			$this->smsInfo = $smsInfo;
		}
    }
	
	public function sendSMS($mobiles=array(),$content,$sendTime='')
	{
		$mobileLen = 100;
		$messageLen = 67;
		
		$content = utf8ToGB($content);
		$contentLen = mb_strlen($content,"GBK");
		$smsTotalCount = ceil($contentLen / $messageLen) * count($mobiles);
		
		$mobileList = array_chunk($mobiles,$mobileLen);
		$successNum = 0;

		foreach($mobileList as $mobileItem)
		{
			$smsCount = ceil($contentLen / $messageLen);
			$mobile = implode(",",$mobileItem);
			
			for($i=0;$i < $smsCount;$i++)
			{
				$msg = mb_substr($content,$i * $messageLen,$messageLen,"GBK");
				
				$sms = new transport();
				
				$params = array(
					"ECODE"=>$this->smsInfo['config']['ecode'],
					"USERNAME"=>utf8ToGB($this->smsInfo['user_name']),
					"PASSWORD"=>utf8ToGB($this->smsInfo['password']),
					"EXTNO"=>'',
					"MOBILE"=>$mobile,
					"CONTENT"=>$msg,
					"SEQ"=>1000
				);
				
				$result = $sms->request($this->smsInfo['server_url'],$params);
				$code = trim($result['body']);
				
				if(empty($code) || !isset($this->statusStr[$code]))
				   $code = "-2";
				
				$smsLog['class_name'] = 'S020';
				$smsLog['send_content'] = gbToUTF8($msg);
				
				$smsLog['action_message'] = $this->statusStr[$code];
				$sendCount = count($mobileItem);
				
				if($code == "1")
				{
					$smsLog['success_mobiles'] = $mobile;
					$smsLog['fail_mobiles'] = "";
					$smsLog['success_count'] = $sendCount;
					$smsLog['fail_count'] = 0;
					
					$sendStrLen = mb_strlen($msg,"GBK");
					$smsLog['expense_count'] = $sendCount;

					$successNum += $sendCount;
				}
				else
				{
					$smsLog['success_mobiles'] = "";
					$smsLog['fail_mobiles'] = $mobile;
					$smsLog['success_count'] = 0;
					$smsLog['fail_count'] = $sendCount;
					$smsLog['expense_count'] = 0;
				}
				
				$smsLog['send_time'] = gmtTime();
				
				if(eyooC("SMS_SEND_LOG") == 1)
					D("SmsSendLog")->add($smsLog);
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