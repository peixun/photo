<?php
// +----------------------------------------------------------------------
// | Fanwe 多语商城建站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: awfigq(awfigq@qq.com)
// +----------------------------------------------------------------------

// 短信订阅管理
class SmsSubscribeAction extends CommonAction{
	public function index()
	{
		$expireTime = gmtTime() - intval(eyooC('SMS_SUBSCRIBE_EXPIRE')) * 3600;
		D("SmsSubscribe")->where("status = 0 and add_time < $expireTime")->delete();
		
		$status = trim($_REQUEST['status']);
		
		if($status != "" && $status >= 0)
		{
			$where.=" and ss.status = $status";
			$parameter .= "status=" . $status ."&";
			$this->assign("status",$status);
		}
		else
		{
			$this->assign("status",-1);
		}
		
		$sql_str = 'SELECT ss.*,m.user_name as mun,c.name as city_name '.
					'FROM '.C("DB_PREFIX").'sms_subscribe as ss '.
					'left join '.C("DB_PREFIX").'user as m on m.id = ss.user_id '.
					'left join '.C("DB_PREFIX").'group_city as c on c.id = ss.city_id '.
					"where ss.id is not null $where ";
		
		L("FORBID",L("SMS_SUBSCRIBE_FORBID"));
		L("NORMAL",L("SMS_SUBSCRIBE_NORMAL"));
		
		$model = M();
        $voList = $this->_Sql_list($model, $sql_str, "&".$parameter, 'id', false);
		
		$this->display ();
		return;
	}
	
//导出邮件列表
	function expsms(){
		set_time_limit(0);
		$list = M("SmsSubscribe")->findAll();
		foreach($list as $k=>$v)
		{
			$list[$k]['mobile_phone'] = $v['mobile_phone'];
			$list[$k]['status'] = $v['status']==1?'启用':'禁用';
			$list[$k]['user_name'] = M("User")->where("id=".$v['user_id'])->getField('user_name');
			$list[$k]['city'] = M("GroupCity")->where("id=".$v['city_id'])->getField('name');
		}
		//dump($list);exit;
		//dump($sql);
    	/* csv文件数组 */
		
    	$mail_value = array('mobile_phone'=>'""', 'status'=>'""', 'user_name'=>'""', 'city'=>'""');
    	$content = "手机号码,状态,用户名,订阅城市";
    	
    	
    	$content = $content . "\n";
    	
		foreach($list as $k=>$v)
		{
			
			$mail_value['mobile_phone'] = '"' . $v['mobile_phone'] . '"';
			$mail_value['status'] = '"' . $v['status'] . '"';
			$mail_value['user_name'] = '"' . $v['user_name'] . '"';
			$mail_value['city'] = '"' . $v['city'] . '"';
			
			
			
			$content .= implode(",", $mail_value) . "\n";
		}	
		
		//dump($content);exit;
    	header("Content-Disposition: attachment; filename=sms_list.csv");
    	header("Content-Type: application/octet-stream");
    	//die();
    	echo utf8ToGB($content);   
	}
}
?>