<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 移通短信平台
include_once("Sms.class.php");
class EtonenetSms implements Sms
{
	public $smsInfo;
	public $message = "";
	private $statusStr = array(
		"000"  => "发送成功",
		"100"  => "SP API接口参数错误",
		"200"  => "MLINK平台内部过滤路由信息",
		"300"  => "MLINK平台内部SP配置信息",
		"400" => "MLINK网关发送时错误",
		"500" => "运行商反馈的信息",
		"600" => "MLINK API发送时错误"   
	);

	private $errStr = array(
		"DELIVRD"  => "短信送到手机",
		"EXPIRED"  => "短信过期",
		"DELETED"  => "短信被删除",
		"UNDELIV"  => "无法投递",
		"ACCEPTD"  => "最终用户接收",
		"UNKNOWN"  => "状态不知",
		"REJECTD"  => "短信被拒绝",
		"ET:0101"  => "缺少操作命令",
		"ET:0102"  => "无效操作命令",
		"ET:0103"  => "缺少SP的ID",
		"ET:0104"  => "无效SP的ID",
		"ET:0105"  => "缺少SP密码",
		"ET:0106"  => "无效SP密码",
		"ET:0107"  => "下行源地址被禁止",
		"ET:0108"  => "无效下行源地址",
		"ET:0109"  => "缺少下行目的地址",
		"ET:0110"  => "无效下行目的地址",
		"ET:0111"  => "超过下行目的地址限制",
		"ET:0112"  => "ESM_CLASS被禁止",
		"ET:0113"  => "无效ESM_CLASS",
		"ET:0114"  => "PROTOCOL_ID被禁止",
		"ET:0115"  => "无效PROTOCOL_ID",
		"ET:0116"  => "缺少消息编码格式",
		"ET:0117"  => "无效消息编码格式",
		"ET:0118"  => "缺少消息内容",
		"ET:0119"  => "无效消息内容",
		"ET:0120"  => "无效消息内容长度",
		"ET:0121"  => "优先级被禁止",
		"ET:0122"  => "无效优先级",
		"ET:0123"  => "定时发送时间被禁止",
		"ET:0124"  => "无效定时发送时间",
		"ET:0125"  => "有效时间被禁止",
		"ET:0126"  => "无效有效时间",
		"ET:0127"  => "通道ID被禁止",
		"ET:0128"  => "无效通道ID",
		"ET:0131"  => "缺少批量下行类型",
		"ET:0132"  => "无效批量下行类型",
		"ET:0133"  => "无效任务ID",
		"ET:0134"  => "无效批量下行标题",
		"ET:0135"  => "缺少批量下行内容",
		"ET:0136"  => "无效批量下行内容",
		"ET:0137"  => "缺少批量下行内容URL",
		"ET:0138"  => "无效批量下行内容URL",
		"ET:0139"  => "SP服务代码不存在",
		"ET:0140"  => "无效的不同内容批量下行地址和内容",
		"ET:0201"  => "MSISDN号码段不存在",
		"ET:0202"  => "MSISDN号码段停用",
		"ET:0210"  => "MSISDN号码被过滤",
		"ET:0220"  => "内容被过滤",
		"ET:0221"  => "内容被人工过滤",
		"ET:0230"  => "下行路由失败",
		"ET:0240"  => "上行路由失败",
		"ET:0250"  => "配额不足",
		"ET:0251"  => "没有配额",
		"ET:0301"  => "SP被禁止",
		"ET:0302"  => "SP被锁定",
		"ET:0303"  => "无效IP地址",
		"ET:0304"  => "超过传输速度限制",
		"ET:0305"  => "超过传输连接限制",
		"ET:0306"  => "SMS下行被禁止",
		"ET:0307"  => "SMS批量下行被禁止",
		"ET:0308"  => "SMS上行被禁止",
		"ET:0309"  => "SMS状态报告被禁止",
		"ET:0401"  => "源号码与通道号不匹配",
		"ET:0402"  => "下发到运行商网关异常",
		"ET:0403"  => "下发到运行商网关无反馈",
		"ET:0500"  => "运行商网关反馈信息",
		"ACCEPTD"  => "API接口消息被接收",
		"REJECTD"  => "API接口消息被拒绝",
		"ET:0601"  => "API接口HttpException",
		"ET:0602"  => "API接口IOException"
	);
	
    public function __construct($smsInfo)
    { 	
		set_time_limit(0);
		$this->smsInfo = $smsInfo;
    }
	
	public function sendSMS($mobiles=array(),$content,$sendTime='')
	{
		$messageLen = 60;
		$mobileLen = 100;
		$price = 1;
		
		$mobileList = array();
		
		foreach($mobiles as $mobile)
		{
			$mobileList[] = "86".$mobile;
		}
		
		$content = utf8ToGB($content);
		$contentLen = mb_strlen($content,"GBK");
		$smsTotalCount = ceil($contentLen / $messageLen) * count($mobileList);
		
		$mobileList = array_chunk($mobileList,$mobileLen);
		$successNum = 0;
		
		foreach($mobileList as $mobileItem)
		{
			$smsCount = ceil($contentLen / $messageLen);
			$mobile = implode(",",$mobileItem);
			
			for($i=0;$i < $smsCount;$i++)
			{
				$msg = mb_substr($content,$i * $messageLen,$messageLen,"GBK");
				
				$smsLog['send_content'] = gbToUTF8($msg);
				
				$result= $this->doRequest($mobile,$msg);
				$status = $this->getStatus($result);
				
				$smsLog['send_mobiles'] = $mobile;
				
				$smsLog['success_mobiles'] = "";
				$smsLog['fail_mobiles'] = "";
					
				
				$smsLog['send_time'] = gmtTime();
				
				$sendCount = count($mobileItem);
				
				if($status['mterrcode'] = '000' && ($status['mtstat'] = 'DELIVRD' || $status['mtstat'] = 'ACCEPTD'))
				{
					$smsLog['action_message'] = "发送成功";
					$smsLog['success_count'] = $sendCount;
					$smsLog['fail_count'] = 0;
					$smsLog['expense_count'] = $sendCount;
					$successNum += $sendCount;
				}
				else
				{
					$smsLog['action_message'] = $this->statusStr[$status['mterrcode']]." ".$this->errStr[$status['mtstat']];
					$smsLog['success_count'] = 0;
					$smsLog['fail_count'] = $sendCount;
					$smsLog['expense_count'] = 0;
				}
				
				if(intval($GLOBALS['db']->getOne("SELECT val FROM ".$GLOBALS['db_config']['DB_PREFIX']."sys_conf WHERE name='SMS_SEND_LOG'")) == 1)
				{
					$sql = "insert into ".$GLOBALS['db_config']['DB_PREFIX']."sms_send_log (class_name,send_content,success_count,success_mobiles,fail_mobiles,expense_count,fail_count,action_message,send_time) values('Etonenet','".$smsLog['send_content']."','".$smsLog['success_count']."','".$smsLog['success_mobiles']."','".$smsLog['fail_mobiles']."','".$smsLog['expense_count']."','".$smsLog['fail_count']."','".$smsLog['action_message']."','".$smsLog['send_time']."')";
					
					$GLOBALS['db']->query($sql);
				}
			}
		}
		
		if($smsTotalCount > $successNum)
		{
			$this->message = $smsTotalCount."条短信中，有".($smsTotalCount - $successNum)."条未成功发送到手机".$mobiles;
			return 0;
		}
		else
		{
			$this->message ="成功发送短信【".$content."】，到手机".$mobiles;
			return 1;
		}
	}
	
	function getStatus($content)
	{
		$queryArr = explode('&',$content);
		$status = array();
		foreach($queryArr as $query)
		{
			$keyValue = explode('=',$query);
			$status[$keyValue[0]] = $keyValue[1];
		}
		return $status;
	}
	
	function doRequest($mobiles,$msg)
	{
		$spid=$this->smsInfo['user_name'];
		$spsc="00";
		$sppassword=$this->smsInfo['password'];
		$sa="10";
		$dc="15";
		$port=80;
		$host=$this->smsInfo['server_url'];
		
		$request = "/sms/mt";
		$request.="?command=MULTI_MT_REQUEST&spid=".$spid."&spsc=".$spsc."&sppassword=".$sppassword;
		$request.="&sa=".$sa."&das=".$mobiles."&dc=".$dc."&sm=";
		$request.= $this->encodeHexStr($dc,$msg);//下发内容转换HEX编码
		$content = $this->httpSend($host,$port,"GET",$request);
		return $content;
	}
	
	/**
	 * 使用http协议发送消息
	 *
	 * @param string $host
	 * @param int $port
	 * @param string $method
	 * @param string $request
	 * @return string
	 */
	public function httpSend($host,$port,$method,$request)
	{
		$httpHeader  = $method." ". $request. " HTTP/1.1\r\n";
		$httpHeader .= "Host: $host\r\n";
		$httpHeader .= "Connection: Close\r\n";
		$httpHeader .= "Content-type: text/plain\r\n";
		$httpHeader .= "Content-length: " . strlen($request) . "\r\n";
		$httpHeader .= "\r\n";
		$httpHeader .= $request;
		$httpHeader .= "\r\n\r\n";
		$fp = @fsockopen($host, $port,$errno,$errstr,5);
		$result = "";
		if ( $fp ) {
			fwrite($fp, $httpHeader);
			while(! feof($fp)) { //读取get的结果
				$result .= fread($fp, 1024);
			}
			fclose($fp);
		}
		else
		{
			return "连接短信网关超时！";//超时标志
		}
		
		list($header, $foo)  = explode("\r\n\r\n", $result);
		list($foo, $content) = explode($header, $result);
		$content=str_replace("\r\n","",$content);
		//返回调用结果
		return $content;
	}
	
	/**
	 *  decode Hex String
	 *
	 * @param string $dataCoding       charset
	 * @param string $hexStr      convert a hex string to binary string
	 * @return string binary string
	 */
	public function decodeHexStr($dataCoding, $hexStr)
	{
		$hexLenght = strlen($hexStr);
		// only hex numbers is allowed
		if ($hexLenght % 2 != 0 || preg_match("/[^\da-fA-F]/",$hexStr)) return FALSE;
	
		unset($binString);
		for ($x = 1; $x <= $hexLenght/2; $x++)
		{
			$binString .= chr(hexdec(substr($hexStr,2 * $x - 2,2)));
		}
	
		return $binString;
	}
	
	/**
	 * encode Hex String
	 *
	 * @param string $dataCoding
	 * @param string $realStr
	 * @return string hex string
	 */
	public function encodeHexStr($dataCoding, $realStr) {
		return bin2hex($realStr);
	}
}
?>