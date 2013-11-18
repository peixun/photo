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

// 企信通短信平台
include_once("Sms.class.php");
include_once(ROOT_PATH."ThinkPHP/Vendor/transport.php");
include_once(ROOT_PATH."ThinkPHP/Vendor/Sms/XmlBase.php");

class QXTSms implements Sms
{
	public $message = "";
	public $smsInfo;
	
	public $statusStr = array(
		"00"  => "批量短信提交成功（批量短信待审批）",
		"01"  => "批量短信提交成功（批量短信跳过审批环节）",
		"03"  => "单条短信提交成功",
		"04"  => "用户名错误",
		"05" => "密码错误",
		"06" => "剩余条数不足",
		"07" => "信息内容中含有限制词(违禁词)",
		"08" => "信息内容为黑内容",
		"09" => "该用户的该内容 受同天内内容不能重复发 限制",
		"10" => "批量下限不足",
		"97" => "短信参数有误",
		"98" => "防火墙无法处理这种短信"			   
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
		$mobileLen = 200;
		$messageLen = 67;
		if($this->smsInfo['config']['contentType'] == 8)
			$messageLen = 500;
			
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
					"OperID"=>$this->smsInfo['user_name'],	
					"OperPass"=>$this->smsInfo['password'],	
					"DesMobile"=>$mobile,
					"Content"=>urlencode($msg),
					"ContentType"=>$this->smsInfo['config']['contentType'],
					"SendTime"=>"",
					"ValidTime"=>"",
					"AppendID"=>""
				);
				
				$result = $sms->request($this->smsInfo['server_url'],$params);
				$smsStatus = toArray($result['body']);

				$code = $smsStatus['code'][0];
				
				$smsLog['send_content'] = gbToUTF8($msg);
				
				$smsLog['action_message'] = $this->statusStr[$code];
				$sendCount = count($mobileItem);
				
				if($code == "00" || $code == "01" || $code == "03")
				{
					$smsLog['success_mobiles'] = $mobile;
					$smsLog['fail_mobiles'] = "";
					$smsLog['success_count'] = $sendCount;
					$smsLog['fail_count'] = 0;
					
					$sendStrLen = mb_strlen($msg,"GBK");
					$smsLog['expense_count'] = ceil($sendStrLen / 67) * $sendCount;

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
				
				if(intval($GLOBALS['db']->getOne("SELECT val FROM ".$GLOBALS['db_config']['DB_PREFIX']."sys_conf WHERE name='SMS_SEND_LOG'")) == 1)
				{
					$sql = "insert into ".$GLOBALS['db_config']['DB_PREFIX']."sms_send_log (class_name,send_content,success_count,success_mobiles,fail_mobiles,expense_count,fail_count,action_message,send_time) values('QXT','".$smsLog['send_content']."','".$smsLog['success_count']."','".$smsLog['success_mobiles']."','".$smsLog['fail_mobiles']."','".$smsLog['expense_count']."','".$smsLog['fail_count']."','".$smsLog['action_message']."','".$smsLog['send_time']."')";
					
					$GLOBALS['db']->query($sql);
				}
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