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


interface Sms{
	
	/**
	 * 发送短信
	 * @param array $mobiles		手机号, 如 array('159xxxxxxxx'),如果需要多个手机号群发,如 array('159xxxxxxxx','159xxxxxxx2') 
	 * @param string $content		短信内容
	 * @param string $sendTime      定时发送时间，格式为 yyyymmddHHiiss
	 * return 1:成功 0:失败
	*/
	function sendSMS($mobiles=array(),$content,$sendTime='');
}
?>