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

// 短信群发
class SmsSendLogAction extends CommonAction{
	public function index()
	{
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		
		if(!empty($_REQUEST['begin_send_time']))
			$map['send_time'] = array("egt",localStrToTime($_REQUEST['begin_send_time']));
		
		if(!empty($_REQUEST['end_send_time']))
			$map['send_time'] = array("elt",localStrToTime($_REQUEST['end_send_time']));
		
		if(!empty($_REQUEST['begin_send_time']) && !empty($_REQUEST['end_send_time']))
			$map['send_time'] = array(array("egt",localStrToTime($_REQUEST['begin_send_time'])),array("elt",localStrToTime($_REQUEST['end_send_time'])),'and');

		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list( $model, $map);
		}

		$this->assign("map",$map);
		
		$sms_list = D("Sms")->order("status desc")->findAll();
		$this->assign("sms_list",$sms_list);
		
		$this->display ();
		return;
	}
	
	public function statistics()
	{
		$class_name = $_REQUEST['class_name'];
		$show_type = $_REQUEST['show_type'];
		$begin_send_time = trim($_REQUEST['begin_send_time']);
		$end_send_time = trim($_REQUEST['end_send_time']);
		$query="";
		$group="";
		$where="";
		$order=" order by send_time asc";
		
		if(!empty($begin_send_time))
			$where.=" and send_time >= ".localStrToTime($_REQUEST['begin_send_time']);
		
		if(!empty($end_send_time))
			$where.=" and send_time <= ".localStrToTime($_REQUEST['end_send_time']);
		
		if(!empty($show_type))
		{
			$group = "group by stime";
			$order=" order by stime asc";
		}
		
		switch($show_type)
		{
			case "y":
			{
				$query.=",FROM_UNIXTIME(send_time + ". date('Z') .",'%Y') as stime";
			}
			break;
			
			case "m":
			{
				$query.=",FROM_UNIXTIME(send_time + ". date('Z') .",'%Y-%m') as stime";
			}
			break;
			
			case "d":
			{
				$query.=",FROM_UNIXTIME(send_time + ". date('Z') .",'%Y-%m-%d') as stime";
			}
			break;
		}
		
		
		$sms_list = D("Sms")->order("status desc")->findAll();
		
		$list = array();
			
		if(empty($class_name))
		{
			foreach($sms_list as $sms)
			{
				$temwhere =$where . " and class_name='$sms[class_name]'";
				$sql="select sum(success_count) as scount,sum(fail_count) as fcount,sum(expense_count) as ecount $query from ".C("DB_PREFIX")."sms_send_log where id is not null $temwhere $group $order ";
				$list[$sms['class_name']]['logs'] = D("SmsSendLog")->query($sql);
				$list[$sms['class_name']]['name'] = $sms['name'];
			}
		}
		else
		{
			$where.=" and class_name='$class_name'";
			$sql="select sum(success_count) as scount,sum(fail_count) as fcount,sum(expense_count) as ecount $query from ".C("DB_PREFIX")."sms_send_log where id is not null $where $group $order ";
			
			$list[$class_name]['logs'] = D("SmsSendLog")->query($sql);
			$list[$class_name]['name'] = $this->getSmsName($class_name);
		}
		
		$this->assign("list",$list);
		$this->assign("sms_list",$sms_list);
		
		$this->display ();
	}
	
	public function getSmsName($class_name)
	{
		return D("Sms")->where("class_name = '$class_name'")->getField("name");
	}
	
	public function getSendStatusLink($id,$status)
	{
		if($status == 0)
			return "<a href='".u("SmsSend/send",array("id"=>$id))."'>发送</a>";
		else
			return "";
	}
	
	public function setSmsDefault()
	{
		$id = intval($_REQUEST ['id']);	
		M("Sms")->setField("status",0);
		
		M("Sms")->where("id = $id")->setField("status",1);
		
		$this->assign ('jumpUrl', u('Sms/index'));
		$this->success (L('EDIT_SUCCESS'));
	}
	
	public function getUserList()
	{
		$usergroup = intval($_REQUEST["usergroup"]);
		$username = trim($_REQUEST["username"]);
		
		$where = " status = 1 and mobile_phone <>''";
		
		if($usergroup > 0)
			$where .= " and group_id = $usergroup";
			
		if(!empty($username))
			$where .= " and user_name like '%$username%'";
			
		$userlist = D("User")->where($where)->field("id,user_name,mobile_phone")->findAll();
		
		echo json_encode($userlist);
	}
}
?>