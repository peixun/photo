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

class SmsPlf
{
	var $sms;
	var $message;
	
	public function __construct()
    { 	
		$smsInfo = $GLOBALS['db']->getRow("select * from ".$GLOBALS['db_config']['DB_PREFIX']."sms where status = 1");
		$smsInfo['config'] = unserialize($smsInfo['config']);
		include_once($smsInfo['class_name']."Sms.class.php");
		$sms_class = $smsInfo['class_name']."Sms";
		$this->sms = new $sms_class($smsInfo);
    }
	
	public function sendSMS($mobiles,$content,$sendTime='')
	{
		if(!is_array($mobiles))
			$mobiles = explode(",",$mobiles);
		
		if(count($mobiles) > 0)
		{
			$status = $this->sms->sendSMS($mobiles,$content,$sendTime);
			$this->message = $this->sms->message;
		}
		else
		{
			$status = 0;
			$this->message = "没有发送的手机号";
		}
		
		return $status;
	}
}
?>