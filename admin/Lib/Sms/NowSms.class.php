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


Vendor('Sms.Now.now#inc');

// 时代互联企业短信平台

class NowSms extends Think implements Sms 
{
	public $config = array(
		"port"=>0,
		"apitype"=>0
	);
	
	public $sms;
	public $message = "";
	public $apitype = 2;
	
	public $statusStr = array(
		"2000"  => "操作成功",
		"3000"  => "数据库错误(插入\删除等)",
		"4000"  => "客户未知错误",
		"4001"  => "xml错误",
		"4002" => "授权错误(用户不存在、密码错误、权限不足等)",
		"4003" => "No data post to server",
		"4004" => "函数不存在",
		"4300" => "财务错误",
		"4400" => "参数错误",
		"5000" => "Server error",
		"5001" => "Unable connet to remote server",
		"5002" => "Server no data return",	
		"5003" => "Server other error",
		"6000" => "操作错误，如不能添加、删除文件等"
	);
	
    public function __construct($smsInfo = '')
    { 	
		if(!empty($smsInfo))
		{
			if(intval($smsInfo['config']['apitype']) > 0)
				$this->apitype = intval($smsInfo['config']['apitype']);
			
			$this->sms = new Now($smsInfo['server_url'],$smsInfo['config']['port'],$smsInfo['user_name'],$smsInfo['password']);
		}
    }
	
	public function sendSMS($mobiles=array(),$content,$sendTime='')
	{
		$messageLen = 70;
		$mobileLen = 99;
		$price = 1.3;
		
		if($this->apitype == 2)
			$price = 1;
		
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
				$this->sms->sendSMS($mobile,$msg,$sendTime,$this->apitype);
				$smsInfo = toArray($this->sms->responseXML);
				$code=$this->sms->getCode();

				$successNum += intval($smsInfo['successnum'][0]);
				$smsLog['class_name'] = 'Now';
				$smsLog['send_content'] = gbToUTF8($msg);
				
				if(intval($code) == 2000)
				{
					$smsLog['success_count'] = intval($smsInfo['successnum'][0]);
					/*2010/06/04 awfigq　修改成功号码和失败号码的检测 */
					$smsLog['success_mobiles'] = empty($smsInfo['successphone'][0]) ? "" : $smsInfo['successphone'][0];
					$smsLog['fail_mobiles'] = empty($smsInfo['failephone'][0]) ? "" : $smsInfo['failephone'][0];
					$smsLog['expense_count'] = $smsLog['success_count'] * $price;
				}
				else
				{
					$smsLog['success_count'] = 0;
					$smsLog['success_mobiles'] = '';
					$smsLog['fail_mobiles'] = $mobile;
					$smsLog['expense_count'] = 0;
				}
				
				/*2010/06/04 awfigq　修改失败号码的数量获取方式 */
				$smsLog['fail_count'] = count($mobileItem) - $smsLog['success_count'];
				$smsLog['action_message'] = $this->statusStr[$code];
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