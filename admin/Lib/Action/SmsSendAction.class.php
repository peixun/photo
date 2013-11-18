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
class SmsSendAction extends CommonAction{
	public function __construct()
	{
		parent::__construct();
//		$time = gmtTime();
//		$sendList = D("SmsSend")-> where("send_time <= $time and status = 0")->findAll();
//		foreach($sendList as $send)
//		{
//			if(D("AjaxSend")->where("send_type = 'SmsSend' and rec_id = $send[id]")->count() == 0)
//			{
//				$ajaxData['send_type'] = "SmsSend";
//				$ajaxData['rec_id'] = $send['id'];
//				$ajaxData['data'] = "";
//				D("AjaxSend")->add($ajaxData);
//				$send['status'] = 1;
//				D("SmsSend")->save($send);
//			}
//		}
	}
	
	public function index()
	{
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		$sql = "select m.* from ".C("DB_PREFIX")."sms_send as m left join ".C("DB_PREFIX")."goods as g on m.rec_id = g.id where m.rec_id = 0 or g.city_id in (".implode(",",$_SESSION['admin_city_ids']).")";
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			if(!$_SESSION['all_city'])
			$this->_Sql_list ( $model, $sql );
			else 
			$this->_list($model,$map);
		}

		$this->assign("map",$map);
		
		$this->display ();
		return;
	}
	
	public function add()
	{
		$user_group = D("UserGroup")-> where("status=1")-> findAll();
		$this->assign("user_group",$user_group);
		$this->display();
	}
	
	public function edit()
	{
		$user_group = D("UserGroup")-> where("status=1")-> findAll();
		$this->assign("user_group",$user_group);
		
		$name=$this->getActionName();
		$model = M ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );
		$this->assign ( 'vo', $vo );
		
		$where['id']  = array('in',$vo['custom_users']);
		
		$user_mobiles = D("User")->where($where)->field("id,user_name,mobile_phone")->findAll();
		$this->assign ( 'user_mobiles', $user_mobiles );
		$this->display();
	}
	
	public function getSendStatus($status)
	{
		$statusStr = array("未发送","<span style='color:#f00;'>发送中...</span>","<span style='color:#00f;'>已发送</span>");
		
		return $statusStr[$status];
	}
	
	public function getSendStatusLink($id,$status)
	{
		if($status == 0)
			return "<a href='".u("SmsSend/send",array("id"=>$id))."'>发送</a>";
		else
			return "";
	}
	
	public function send()
	{
		$id = intval($_REQUEST["id"]);
		
		if(D("AjaxSend")->where("send_type = 'SmsSend' and rec_id = $id")->count() == 0)
		{
			$send = D("SmsSend")-> where("id = $id")->find();
			$ajaxData['send_type'] = "SmsSend";
			$ajaxData['rec_id'] = $send['id'];
			D("AjaxSend")->add($ajaxData);
			$send['status'] = 1;
			D("SmsSend")->save($send);
		}
		
		$this->redirect('SmsSend/index');
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
	
	public function insert()
	{
		if(!$_SESSION['all_city'])
		{
		$rec_id = intval($_REQUEST['rec_id']);
		if($rec_id>0)
		{
			if(!in_array(intval(M("Goods")->where("id=".$rec_id)->getField("city_id")),$_SESSION['admin_city_ids']))
			{
				$this->error("不能群发其他地区的团购短信");
			}
		}
		}
		parent::insert();
	}
	public function update()
	{
		if(!$_SESSION['all_city'])
		{
		$rec_id = intval($_REQUEST['rec_id']);
		if($rec_id>0)
		{
			if(!in_array(intval(M("Goods")->where("id=".$rec_id)->getField("city_id")),$_SESSION['admin_city_ids']))
			{
				$this->error("不能群发其他地区的团购短信");
			}
		}
		}
		parent::update();
	}
}
?>