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

// 短信配置
class SmsAction extends CommonAction{
	public function edit()
	{
		$langSet = C('DEFAULT_LANG');	
		
		$files = scandir($this->getRealPath()."/admin/Lang/".$langSet."/sms/");

		foreach($files as $file)
		{
			if($file!='.'&&$file!='..')
			{
				 L(include LANG_PATH.$langSet.'/sms/'.$file);
			}
		}
					
		$name=$this->getActionName();
		$model = M ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );
		$vo['config'] = unserialize($vo['config']);	

		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	
	public function update()
	{
		$sms_model = D("Sms");
		if (!$sms_model->create()){
			$this->error($sms_model->getError());
		}		
		$data = $_POST;
		$data['user_name']=str_replace(' ','',$data['user_name']);
		$data['password']=str_replace(' ','',$data['password']);
		$data['session_key']=str_replace(' ','',$data['session_key']);
		$sms_model->save($data);
		$this->success (L('EDIT_SUCCESS'));		
	}
	
	public function getSmsStatus($status,$id)
	{
		$contact_list = '';
		switch(M("Sms")->where("id=".$id)->getField("class_name"))
		{
			case 'QXT': //企信通
				$contact_list = '客服QQ: 312163722,331452188 电话：刘影英 13811774250' ;
				break;
			case 'Etonenet': //移通短信平台
				$contact_list = '';
				break;
			case 'Emay': //亿美短信平台
				$contact_list = '亿美魏立东 QQ: 1362342 电话：15801088930。';
				break;
			case 'Now':
				$contact_list = '';
				break;
			case 's020':
				$contact_list = '王舵 电话：010-85805385l转801 手机：13911237184';
				break;
		}
		if($status)
			return "<span style='color:#f00;'>默认短信平台</span>&nbsp;&nbsp;".$contact_list;
		else
			return "<a href='".u("Sms/setSmsDefault",array("id"=>$id))."'>设为默认短信平台</a>&nbsp;&nbsp;".$contact_list;
	}
	
	public function setSmsDefault()
	{
		$id = intval($_REQUEST ['id']);	
		M("Sms")->setField("status",0);
		
		M("Sms")->where("id = $id")->setField("status",1);
		
		$this->assign ('jumpUrl', u('Sms/index'));
		$this->success (L('EDIT_SUCCESS'));
	}
	
	public function sendDemo()
	{		
		$number = $_REQUEST['number'];		
		$number = array($number);
		$smsInfo = D("Sms")->where("status = 1")->find();
		$smsInfo['config'] = unserialize($smsInfo['config']);
		$sms_class = $smsInfo['class_name']."Sms";
		$smsobj = new $sms_class($smsInfo);
		$status = $smsobj->sendSMS($number,'测试短信发送成功');
		$this->success("短信已发出，请确认是否收到测试短信",1);		
	}
	
	//余额查询
	public function smsBalance()
	{
		$class_name = $_REQUEST['class_name'];
		$smsInfo = D("Sms")->where("class_name = '$class_name'")->find();
		$smsInfo['config'] = unserialize($smsInfo['config']);
		$sms_class = $smsInfo['class_name']."Sms";
		$smsobj = new $sms_class($smsInfo);
		$smsobj->getBalance();
		echo $smsobj->message;
	}
	
	//登陆
	public function smsLogin()
	{
		$class_name = $_REQUEST['class_name'];
		$smsInfo = D("Sms")->where("class_name = '$class_name'")->find();
		$smsInfo['config'] = unserialize($smsInfo['config']);
		$sms_class = $smsInfo['class_name']."Sms";
		$smsobj = new $sms_class($smsInfo);
		$smsobj->login();
		echo $smsobj->message;
	}
	
	//注销
	public function smsLogout()
	{
		$class_name = $_REQUEST['class_name'];
		$smsInfo = D("Sms")->where("class_name = '$class_name'")->find();
		$smsInfo['config'] = unserialize($smsInfo['config']);
		$sms_class = $smsInfo['class_name']."Sms";
		$smsobj = new $sms_class($smsInfo);
		$smsobj->logout();
		echo $smsobj->message;
	}
}
?>