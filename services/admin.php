<?php
//团购服务接口文件

require('init.php');

if($_REQUEST['act']=='checkAjaxSendRun')
{
	$time = gmtTime();
	$count = $db->getOne("select count(id) from ".$db_config['DB_PREFIX']."sms_send where send_time <= $time and status = 0");

	if(intval($count) == 0)
		$count = $db->getOne("select count(*) from ".$db_config['DB_PREFIX']."ajax_send");

	echo $count;
}

if($_REQUEST['act'] == 'ajaxSendRun')
{
	$time = gmtTime();

	$sendList = $db->getAll("select id from ".$db_config['DB_PREFIX']."sms_send where send_time <= $time and status = 0");
	foreach($sendList as $send)
	{
		if(intval($db->getOne("select count(*) from ".$db_config['DB_PREFIX']."ajax_send where send_type = 'SmsSend' and rec_id = $send[id]")) == 0)
		{
			$sql = "insert into ".$db_config['DB_PREFIX']."ajax_send (send_type,rec_id,data) values('SmsSend',$send[id],'');";
			$db->query($sql);

			$db->query("update ".$db_config['DB_PREFIX']."sms_send set status = 1 where id = $send[id]");
		}
	}

	$ajaxsend =  $db->getRow("select * from ".$db_config['DB_PREFIX']."ajax_send");

	if($ajaxsend)
	{
		//2010/6/10 修改批量短信发送流程，提前删除发送记录，避免重复发送
		$db->query("delete from ".$db_config['DB_PREFIX']."ajax_send where id = $ajaxsend[id]");

		require('Sms/SmsPlf.class.php');

		switch($ajaxsend['send_type'])
		{
			case "SmsSend":
			{
				$goodsSendType = $db->getOne("SELECT val FROM ".$db_config['DB_PREFIX']."sys_conf WHERE name='GOODS_SMS_SEND_TYPE'");

				$db->query("update ".$db_config['DB_PREFIX']."sms_send set status = 2 where id = $ajaxsend[rec_id]");

				$smsSend = $db->getRow("select * from ".$db_config['DB_PREFIX']."sms_send where id = $ajaxsend[rec_id]");

				$message = $smsSend['send_content'];
				$mobiles = array();

				if($smsSend['type'] == 2)
				{
					$goods = $db->getRow("select * from ".$db_config['DB_PREFIX']."goods where id = $smsSend[rec_id]");

					$goods_name = empty($goods['goods_short_name']) ? $goods['name_1'] : $goods['goods_short_name'];
					$mail_template = $db->getRow("select * from ".$db_config['DB_PREFIX']."mail_template where name = 'goods_sms'");
					if($mail_template)
					{
						$tpl->assign('goods_name',$goods_name);
						$tpl->assign('begin_time',toDate($goods['promote_begin_time'],'Y-m-d'));
						$message = $tpl->fetch_str($mail_template['mail_content']);
						$message = $tpl->_eval($message);
					}
				}

				if($smsSend['send_type'] == 1)
				{
					$where = " mobile_phone <> '' and LENGTH(mobile_phone) = 11 and LEFT(mobile_phone,1) = '1'";

					if($smsSend['type'] == 2)
						$where .= " and city_id = $goods[city_id] and is_receive_sms = 1";

					if($smsSend['user_group'] > 0)
						$where .= " and group_id = ".$smsSend['user_group'];

					if($smsSend['type'] == 2 && $goodsSendType == 0)
						$user_mobiles = array();
					else
						$user_mobiles = $db->getCol("select mobile_phone from ".$db_config['DB_PREFIX']."user where $where");
				}
				else
				{
					$where  = db_create_in($smsSend['custom_users'],"id");

					$user_mobiles = $db->getCol("select mobile_phone from ".$db_config['DB_PREFIX']."user where $where");
				}

				$mobiles = $user_mobiles;

				if(!empty($smsSend['custom_mobiles']))
				{
					$custom_mobiles = explode(",",$smsSend['custom_mobiles']);
					$mobiles = array_merge($mobiles,$custom_mobiles);
				}

				if($smsSend['type'] == 2 && $goodsSendType != 1)
				{
					$sms_subscribe = $db->getCol("select mobile_phone from ".$db_config['DB_PREFIX']."sms_subscribe where city_id = $goods[city_id] and status = 1");

					if(count($sms_subscribe) > 0)
						$mobiles = array_merge($mobiles,$sms_subscribe);
				}

				$mobiles = array_unique($mobiles);

				if(count($mobiles) > 0 && !empty($message))
				{
					$sms= new SmsPlf();
					$sms->sendSMS($mobiles,$message);
				}
			}
			break;
		}
	}

	//echo "ok";
	exit;
}
?>