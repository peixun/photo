<?php
//团购服务接口文件

require('init.php');
if($_REQUEST['act'] == 'getStatus')
{
	$result = array("user"=>"","goods"=>"","message"=>"","tooltip"=>"",);
	$m = trim($_REQUEST['m']);
	$a = trim($_REQUEST['a']);
	$status = intval($_REQUEST['status']);
	$ma = strtolower("$m:$a");
	
	switch($ma)
	{
		case "index:index":
		case "goods:show":
		case "goods:showcate":
		case "goods:other":	
		{
			if (file_exists(ROOT_PATH.'app/Lang/'.LANG.'/xy_lang.php')){
				global $lang;
				$lang = include(ROOT_PATH.'app/Lang/'.LANG.'/xy_lang.php');
				$GLOBALS['tpl']->assign('lang',$lang);		
				//echo json_encode(ROOT_PATH.'app/Lang/'.LANG.'/xy_lang.php file_exists');		
			}else{
				//echo json_encode(ROOT_PATH.'app/Lang/'.LANG.'/xy_lang.php file_no_exists');
			}
			
			if(intval($_REQUEST['goodsID']) > 0)
				$result["goods"] = dotran(getGoodsStatus());
			//echo json_encode($result["goods"]);
			//return;
			if($status == 1)
			{
				$result["user"] = dotran(getUserStatus());
				if(intval($_REQUEST['goodsID']) > 0)
				{
					$result["message"] = dotran(getMessageStatus());
					$result["tooltip"] = dotran(getTooltipStatus());
				}
			}
		}
		break;
		
		case "belowline:index":
		case "advance:index":
		{
			$result["message"] = dotran(getMessageStatus());
			$result["user"] = dotran(getUserStatus());
		}
		break;
		
		case "goods:index":
		case "goods:other":
		case "article:show":
		case "message:comment":
		case "supplier:index":
		case "suppliercomment:index":
		{
			$result["user"] = dotran(getUserStatus());
		}
		break;
	}

	echo json_encode($result);
}
elseif($_REQUEST['act'] == 'smsSubscribe')
{
	//短信订阅
	
	$result = array("type"=>0,"message"=>"");
	
//	$isSmsSubscribe= $db->getOne("SELECT val FROM ".$db_config['DB_PREFIX']."sys_conf WHERE name='SMS_SUBSCRIBE'");
	$isSmsSubscribe = stripslashes($sys_config["SMS_SUBSCRIBE"]);
	
	$city_id = intval($_REQUEST['city']);
	$mobile_phone = trim($_REQUEST['mobile']);
	$verify = trim($_REQUEST['verify']);
	$isMobile = preg_match("/^(13\d{9}|18\d{9}|15\d{9})$/",$mobile_phone);
	
	if(intval($isSmsSubscribe) == 1 && $city_id > 0 && $isMobile == 1)
	{
		if(!isset($GLOBALS['lang']))
		{
			global $lang;
			$lang = include(ROOT_PATH.'app/Lang/'.LANG.'/xy_lang.php');	
		}
		
		if(md5($verify) != $_SESSION["smsSubscribe"] && !empty($_SESSION["smsSubscribe"]))
		{
			$result["message"]=$GLOBALS['lang']['XY_VERIFY_ERROR'];
			echo json_encode($result);
			exit;
		}
		
		$smsSubscribe = $db->getRow("select `id`,`mobile_phone`,`city_id`,`code`,`status`,`user_id`,`add_time`,`send_count` from ".$db_config['DB_PREFIX']."sms_subscribe where mobile_phone = '$mobile_phone' and city_id = '$city_id'");
		
		if(isset($smsSubscribe['status']) && intval($smsSubscribe['status']) == 1)
		{
			$result["type"]=2;
		}
		else
		{
			$user_id = intval($_SESSION['user_id']);
			$add_time = gmtTime();
			$tempcode = unpack('H4',str_shuffle(md5(uniqid())));
			$code = $tempcode[1];
			
			require('Sms/SmsPlf.class.php');
		
			$mail_template = $db->getRow("select `id`,`name`,`mail_title`,`mail_content`,`is_html` from ".$db_config['DB_PREFIX']."mail_template where name = 'sms_subscribe_code'");
					
			if($mail_template)
			{
				$tpl->assign('code',$code);
				$message = $tpl->fetch_str($mail_template['mail_content']);
				$message = $tpl->_eval($message);
			}
			
			if(!empty($message))
			{
				$mobiles[] = $mobile_phone;
				
				$sms= new SmsPlf();
				
				if($sms->sendSMS($mobiles,$message))
				{
					$result["type"]=1;
					
					if(isset($smsSubscribe['id']) && intval($smsSubscribe['id']) > 0)
					{
						$sql = "update ".$db_config['DB_PREFIX']."sms_subscribe set code = '$code' where id = '$smsSubscribe[id]'";
						$GLOBALS['db']->query($sql);
					}
					else
					{
						$sql = "insert into ".$db_config['DB_PREFIX']."sms_subscribe  (mobile_phone,city_id,code,status,user_id,add_time,send_count) values('$mobile_phone','$city_id','$code',0,'$user_id','$add_time',0)";
						$GLOBALS['db']->query($sql);
					}
				}
				else
					$result["message"]=$GLOBALS['lang']['XY_SMS_SEND_ERROR'];
			}
			else
				$result["message"]=$GLOBALS['lang']['XY_SMS_SEND_ERROR'];
		}
		
		echo json_encode($result);
	}
}
elseif($_REQUEST['act'] == 'smsSubscribeCode')
{
	//验证短信认识码
	
	$result = array("type"=>0,"message"=>"");
	
//	$isSmsSubscribe= $db->getOne("SELECT val FROM ".$db_config['DB_PREFIX']."sys_conf WHERE name='SMS_SUBSCRIBE'");
	$isSmsSubscribe = stripslashes($sys_config["SMS_SUBSCRIBE"]);
	
	$city_id = intval($_REQUEST['city']);
	$mobile_phone = trim($_REQUEST['mobile']);
	$code = trim($_REQUEST['code']);
	$isMobile = preg_match("/^(13\d{9}|18\d{9}|15\d{9})$/",$mobile_phone);
	
	if(intval($isSmsSubscribe) == 1 && $city_id > 0 && $isMobile == 1 && !empty($code))
	{
		if(!isset($GLOBALS['lang']))
		{
			global $lang;
			$lang = include(ROOT_PATH.'app/Lang/'.LANG.'/xy_lang.php');	
		}
		
		$smsSubscribe = $db->getRow("select `id`,`mobile_phone`,`city_id`,`code`,`status`,`user_id`,`add_time`,`send_count` from ".$db_config['DB_PREFIX']."sms_subscribe where mobile_phone = '$mobile_phone' and city_id = '$city_id' and code = '$code'");
		
		if($smsSubscribe !== false)
		{
			$sql = "update ".$db_config['DB_PREFIX']."sms_subscribe set status = 1 where id = '$smsSubscribe[id]'";
			$GLOBALS['db']->query($sql);
			$result["type"]=1;
		}
		else
			$result["message"]=$GLOBALS['lang']['XY_CODE_ERROR'];
		
		echo json_encode($result);
	}
}
elseif($_REQUEST['act'] == 'unSmsSubscribe')
{
	//短信退订
	
	$result = array("type"=>0,"message"=>"");
	
//	$isSmsSubscribe= $db->getOne("SELECT val FROM ".$db_config['DB_PREFIX']."sys_conf WHERE name='SMS_SUBSCRIBE'");
	$isSmsSubscribe = stripslashes($sys_config["SMS_SUBSCRIBE"]);
	
	$city_id = intval($_REQUEST['city']);
	$mobile_phone = trim($_REQUEST['mobile']);
	$verify = trim($_REQUEST['verify']);
	$isMobile = preg_match("/^(13\d{9}|18\d{9}|15\d{9})$/",$mobile_phone);
	
	if(intval($isSmsSubscribe) == 1 && $city_id > 0 && $isMobile == 1)
	{
		if(!isset($GLOBALS['lang']))
		{
			global $lang;
			$lang = include(ROOT_PATH.'app/Lang/'.LANG.'/xy_lang.php');	
		}
		
		if(md5($verify) != $_SESSION["smsSubscribe"] && !empty($_SESSION["smsSubscribe"]))
		{
			$result["message"]=$GLOBALS['lang']['XY_VERIFY_ERROR'];
			echo json_encode($result);
			exit;
		}
		
		$smsSubscribe = $db->getRow("select `id`,`mobile_phone`,`city_id`,`code`,`status`,`user_id`,`add_time`,`send_count` from ".$db_config['DB_PREFIX']."sms_subscribe where mobile_phone = '$mobile_phone' and city_id = '$city_id'");
		
		if(isset($smsSubscribe['status']) && intval($smsSubscribe['id']) > 0)
		{
			$tempcode = unpack('H4',str_shuffle(md5(uniqid())));
			$code = $tempcode[1];
			
			require('Sms/SmsPlf.class.php');
		
			$mail_template = $db->getRow("select `id`,`name`,`mail_title`,`mail_content`,`is_html` from ".$db_config['DB_PREFIX']."mail_template where name = 'sms_unsubscribe_code'");
					
			if($mail_template)
			{
				$tpl->assign('code',$code);
				$message = $tpl->fetch_str($mail_template['mail_content']);
				$message = $tpl->_eval($message);
			}
			
			if(!empty($message))
			{
				$mobiles[] = $mobile_phone;
				
				$sms= new SmsPlf();
				
				if($sms->sendSMS($mobiles,$message))
				{
					$result["type"]=1;
					
					$sql = "update ".$db_config['DB_PREFIX']."sms_subscribe set code = '$code' where id = '$smsSubscribe[id]'";
					$GLOBALS['db']->query($sql);
				}
				else
					$result["message"]=$GLOBALS['lang']['XY_SMS_SEND_ERROR'];
			}
			else
				$result["message"]=$GLOBALS['lang']['XY_SMS_SEND_ERROR'];
		}
		else
		{
			$cityName = $db->getOne("select name from ".$db_config['DB_PREFIX']."group_city where id = '$city_id'");
			$result["message"]=sprintf($GLOBALS['lang']['XY_SMS_SUBSCRIBE_NO'],$cityName);
		}
		
		echo json_encode($result);
	}
}
elseif($_REQUEST['act'] == 'unSmsSubscribeCode')
{
	//验证短信退订码
	
	$result = array("type"=>0,"message"=>"");
	
//	$isSmsSubscribe= $db->getOne("SELECT val FROM ".$db_config['DB_PREFIX']."sys_conf WHERE name='SMS_SUBSCRIBE'");
	$isSmsSubscribe = stripslashes($sys_config["SMS_SUBSCRIBE"]);
	
	$city_id = intval($_REQUEST['city']);
	$mobile_phone = trim($_REQUEST['mobile']);
	$code = trim($_REQUEST['code']);
	$isMobile = preg_match("/^(13\d{9}|18\d{9}|15\d{9})$/",$mobile_phone);
	
	if(intval($isSmsSubscribe) == 1 && $city_id > 0 && $isMobile == 1 && !empty($code))
	{
		if(!isset($GLOBALS['lang']))
		{
			global $lang;
			$lang = include(ROOT_PATH.'app/Lang/'.LANG.'/xy_lang.php');	
		}
		
		$smsSubscribe = $db->getRow("select `id`,`mobile_phone`,`city_id`,`code`,`status`,`user_id`,`add_time`,`send_count` from ".$db_config['DB_PREFIX']."sms_subscribe where mobile_phone = '$mobile_phone' and city_id = '$city_id' and code = '$code'");
		
		if($smsSubscribe !== false)
		{
			$sql = "delete from ".$db_config['DB_PREFIX']."sms_subscribe where id = '$smsSubscribe[id]'";
			$GLOBALS['db']->query($sql);
			$result["type"]=1;
		}
		else
			$result["message"]=$GLOBALS['lang']['XY_UNCODE_ERROR'];
		
		echo json_encode($result);
	}
}
elseif($_REQUEST['act'] == 'cs')
{
	$id = intval($_REQUEST['id']);
	echo "gmtTime:".gmtTime()."<br>";
	echo "toDate(gmtTime()):".toDate(gmtTime())."<br>";
	if ($id > 0){
		$sql = "SELECT `id`,`name_1`,`sn`,`cate_id`,`city_id`,`suppliers_id`,`click_count`,`cost_price`,`shop_price`,`market_price`,`promote_price`,`promote_begin_time`,`promote_end_time`,`create_time`,`update_time`,`type_id`,`goods_type`,`stock`,`brief_1`,`brand_id`,`is_best`,`is_hot`,`is_new`,`status`,`sort`,`seokeyword_1`,`seocontent_1`,`goods_desc_1`,`small_img`,`big_img`,`origin_img`,`define_small_img`,`is_define_small_img`,`is_inquiry`,`weight`,`spec_type`,`weight_unit`,`score`,`web_reviews`,`goods_reviews`,`min_user_time`,`special_note`,`max_bought`,`is_group_fail`,`complete_time`,`buy_count`,`group_user`,`user_count`,`earnest_money`,`group_bond_end_time`,`expand1`,`expand2`,`expand3`,`expand4`,`u_name`,`referrals`,`close_referrals`,`goods_short_name`,`fail_buy_count`,`free_delivery_amount`,`allow_combine_delivery`,`allow_sms` FROM ".$GLOBALS['db_config']['DB_PREFIX']."goods WHERE id=$id";
		$goods = $GLOBALS['db']->getRow($sql);
		echo '商品名称:'.$goods['name_1']."<br>";
		echo 'promote_begin_time:'.$goods['promote_begin_time']."<br>";
		echo 'promote_begin_time:'.toDate($goods['promote_begin_time'])."<br>";
		echo 'promote_end_time:'.toDate($goods['promote_end_time'])."<br>";
		echo 'promote_end_time:'.$goods['promote_end_time']."<br>";
		echo 'complete_time:'.$goods['complete_time']."<br>";
		echo 'complete_time:'.toDate($goods['complete_time'])."<br>";		
	}
}
elseif($_REQUEST['act'] == 'getReferrals')
{
		$id = intval($_REQUEST['id']);
    	$uid = intval($_REQUEST['uid']);
    	$goods = getReferralsGoods($id,$uid);
    	//$goods['urlbrief'] = urlencode($goods['name_1']);
    	//$goods['urlname'] = urlencode($goods['name_1']);
    	
    	$urllink = getDomain().__ROOT__."/".$goods['url'];
    	$base_urllink = getDomain().__ROOT__."/".$goods['share_url'];
    	
    	$tmpl_content = @file_get_contents(getcwd()."/../Public/fx.html");
    	//print_r($goods);exit;
    	$tpl->assign('goods',$goods);
    	$tpl->assign('urllink',$urllink);
    	$tpl->assign('base_urllink',$base_urllink);
		$content = $tpl->fetch_str($tmpl_content);
		$content = $tpl->_eval($content);
    	echo $content;
}


function getReferralsGoods($goodsID = 0,$uid = 0)
	{
		$db_config = $GLOBALS['db_config'];
		$curr_lang_id = 1;
		$time = gmtTime();
		if($goodsID == 0)
		{			   
			$where = " status = 1 AND promote_begin_time <= $time AND promote_end_time >= $time ";
			
			if($cityID == 0)
			{
				$sql = "select id from ".$db_config['DB_PREFIX']."group_city where status = 1 order by is_defalut desc,id asc limit 1";
				$cityID = $GLOBALS['db']->getOne($sql);
				$where .= " AND city_id = $cityID";
			}
			else
			{
				$where .= " AND city_id = $cityID";
			}
			
			$item = $GLOBALS['db']->getRow("select name_1,goods_short_name,u_name,id,brief_1 from ".$db_config['DB_PREFIX']."goods where ".$where." order by sort desc,id desc limit 1");
			//$item = $this->where($where)->field("name_1,goods_short_name,u_name,id")->order("sort desc,id desc")->find();
		}
		else{
			$item = $GLOBALS['db']->getRow("select name_1,goods_short_name,u_name,id,brief_1 from ".$db_config['DB_PREFIX']."goods where id=$goodsID and status = 1");
		}	
		//dump(eyooC("URL_ROUTE"));	
		
		if($item)
		{
//			$url_route = $GLOBALS['db']->getOne("select val from ".$db_config['DB_PREFIX']."sys_conf where name = 'URL_ROUTE'");
			$url_route = stripslashes($sys_config["URL_ROUTE"]);
			if($url_route==1)
			{
				if($item['u_name']!='')
				{
					$item['url'] = "g-".rawurlencode($item['u_name'])."-ru-".intval($uid).".html";
					$item['share_url'] = "g-".($item['u_name'])."-ru-".intval($uid).".html";
				}
				else
				{
					$item['url'] = "tg-".$item['id']."-ru-".intval($uid).".html";
					$item['share_url'] = "tg-".$item['id']."-ru-".intval($uid).".html";
				}
			}			
			else
			{
				$item['url'] = rawurlencode("index.php?m=Goods&a=show&id=".$item['id']."&ru=".intval($uid));
				$item['share_url'] = ("index.php?m=Goods&a=show&id=".$item['id']."&ru=".intval($uid));
			}
			//$mail = D("MailTemplate")->where("name = 'share'")->find();
			$mail = $GLOBALS['db']->getRow("select `id`,`name`,`mail_title`,`mail_content`,`is_html` from ".$db_config['DB_PREFIX']."mail_template where name ='share'");
			$mail['mail_title'] = str_replace('{$title}',$item['name_'.$curr_lang_id], $mail['mail_title']);
			$mail['mail_content'] = str_replace('{$title}',$item['name_'.$curr_lang_id], $mail['mail_content']);
			$item['urlgbname'] = urlencode(utf8ToGB($mail['mail_title']));
			$item['urlgbbody'] = urlencode(utf8ToGB($mail['mail_content']));
			$item['urlname'] = urlencode($item['name_'.$curr_lang_id]);
			$item['urlbrief'] = urlencode($item['brief_'.$curr_lang_id]);
		}
		
		//print_r($item);
		//exit;
		return $item;
	}
function getTooltipStatus()
{
	if(intval($_SESSION['user_id']) > 0)
	{
		$id = intval($_REQUEST['goodsID']);
		
		$sql = "select o.id from ".$GLOBALS['db_config']['DB_PREFIX']."order as o left join ".$GLOBALS['db_config']['DB_PREFIX']."order_goods as og on og.order_id = o.id left join ".$GLOBALS['db_config']['DB_PREFIX']."goods as g on g.id = og.rec_id where g.id = $id and o.money_status < 2 and o.user_id = '".$_SESSION['user_id']."' and g.is_group_fail <> 1 and g.promote_end_time >= ".gmtTime()." and o.id is not null group by o.id order by o.create_time desc,o.update_time desc LIMIT 0,1";
			
		$orderID =  intval($GLOBALS['db']->getOne($sql));
			
		$sql = "select id,is_lookat from ".$GLOBALS['db_config']['DB_PREFIX']."group_bond where goods_id=$id and is_valid = 1 and user_id = '".$_SESSION['user_id']."' GROUP BY goods_id HAVING is_lookat = 0 ORDER BY id desc LIMIT 0,1";
		
		if($orderID > 0)
		{
			$GLOBALS['tpl']->assign('orderCheckUrl',str_replace("000",$orderID,$_REQUEST['orderCheckUrl']));;
			$GLOBALS['tpl']->assign('orderID',$orderID);
		}
			
		$groupBondID =  intval($GLOBALS['db']->getOne($sql));
		
		if($groupBondID > 0)
		{
//			$GROUPBOTH  = $GLOBALS['db']->getOne("SELECT val FROM ".$GLOBALS['db_config']['DB_PREFIX']."sys_conf WHERE name='GROUPBOTH'");
			$GROUPBOTH = eyooC("GROUPBOTH");
			$GLOBALS['tpl']->assign('GROUPBOTH',$GROUPBOTH);
			$GLOBALS['tpl']->assign('groupBondUrl',str_replace("000",$groupBondID,$_REQUEST['groupBondUrl']));
			$GLOBALS['tpl']->assign('groupBondID',$groupBondID);
		}
		
		//增加by hc, 当订单未付款时，且当前团购卖光时不再提示
		
		$goods_id = $GLOBALS['db']->getOne("select rec_id from ".$GLOBALS['db_config']['DB_PREFIX']."order_goods where order_id=".$orderID);
		$goods_info = $GLOBALS['db']->getRow("select `id`,`name_1`,`sn`,`cate_id`,`city_id`,`suppliers_id`,`click_count`,`cost_price`,`shop_price`,`market_price`,`promote_price`,`promote_begin_time`,`promote_end_time`,`create_time`,`update_time`,`type_id`,`goods_type`,`stock`,`brief_1`,`brand_id`,`is_best`,`is_hot`,`is_new`,`status`,`sort`,`seokeyword_1`,`seocontent_1`,`goods_desc_1`,`small_img`,`big_img`,`origin_img`,`define_small_img`,`is_define_small_img`,`is_inquiry`,`weight`,`spec_type`,`weight_unit`,`score`,`web_reviews`,`goods_reviews`,`min_user_time`,`special_note`,`max_bought`,`is_group_fail`,`complete_time`,`buy_count`,`group_user`,`user_count`,`earnest_money`,`group_bond_end_time`,`expand1`,`expand2`,`expand3`,`expand4`,`u_name`,`referrals`,`close_referrals`,`goods_short_name`,`fail_buy_count`,`free_delivery_amount`,`allow_combine_delivery`,`allow_sms` from ".$GLOBALS['db_config']['DB_PREFIX']."goods where id =".intval($goods_id));
		
		//增加by awfigq, 当库存设置为0时显示
		if (($groupBondID > 0 || $orderID > 0)&&($goods_info['stock']==0 || $goods_info['buy_count']<$goods_info['stock']))
			return $GLOBALS['tpl']->fetch('Inc/goods/head_tooltip.tpl');
	}
}

function getUserStatus()
{
	if($_SESSION['user_id'] > 0)
	{
		//add by chenfq 2001-05-30 ,"other_sys"=>$_SESSION['other_sys']
		return array("user_id"=>$_SESSION['user_id'],"user_name"=>$_SESSION['user_name'],"user_email"=>$_SESSION['user_email'],"other_sys"=>$_SESSION['other_sys']);
	}
}

function getGoodsStatus()
{
	$result = array("dateHTML"=>"","statusHTML"=>"","btnHTML"=>"");
	$id = intval($_REQUEST['goodsID']);
	$sql = "SELECT `id`,`name_1`,`sn`,`cate_id`,`city_id`,`suppliers_id`,`click_count`,`cost_price`,`shop_price`,`market_price`,`promote_price`,`promote_begin_time`,`promote_end_time`,`create_time`,`update_time`,`type_id`,`goods_type`,`stock`,`brief_1`,`brand_id`,`is_best`,`is_hot`,`is_new`,`status`,`sort`,`seokeyword_1`,`seocontent_1`,`goods_desc_1`,`small_img`,`big_img`,`origin_img`,`define_small_img`,`is_define_small_img`,`is_inquiry`,`weight`,`spec_type`,`weight_unit`,`score`,`web_reviews`,`goods_reviews`,`min_user_time`,`special_note`,`max_bought`,`is_group_fail`,`complete_time`,`buy_count`,`group_user`,`user_count`,`earnest_money`,`group_bond_end_time`,`expand1`,`expand2`,`expand3`,`expand4`,`u_name`,`referrals`,`close_referrals`,`goods_short_name`,`fail_buy_count`,`free_delivery_amount`,`allow_combine_delivery`,`allow_sms` FROM ".$GLOBALS['db_config']['DB_PREFIX']."goods WHERE id=$id";
	$goods = $GLOBALS['db']->getRow($sql);
	$goods['name'] = $goods['name_'.$GLOBALS['langItem']['id']];//modify by chenfq 2010-05-30 $langItem['id'] ==> $GLOBALS['langItem']['id']
	$goods['market_price'] = floatval($goods['market_price']);
	$goods['shop_price'] = floatval($goods['shop_price']);
	$goods['earnest_money'] = floatval($goods['earnest_money']);
	$goods['market_price_format'] = formatPrice(floatval($goods['market_price']));
	$goods['shop_price_format'] = formatPrice(floatval($goods['shop_price']));
	$goods['earnest_money_format'] = (floatval($goods['earnest_money']) == 0) ? '免费' :formatPrice(floatval($goods['earnest_money']));
	
	if(intval($goods['promote_end_time']) < gmtTime())
		$goods['is_end'] = true;
	
	if(intval($goods['stock']) > 0)
	{
		$goods['surplusCount'] = intval($goods['stock']) - intval($goods['buy_count']);
		if($goods['surplusCount'] <= 0)
			$goods['is_none'] = true;
			
		$goods['stockbfb'] = ($goods['surplusCount'] / intval($goods['stock'])) * 100;
	}
	
	if($goods['promote_end_time'] < gmtTime())
	{
		if (($goods['group_user'] >= 0 && $goods['group_user'] > $goods['buy_count']))
		{
			$goods['is_group_fail'] = 1;
			$goods['complete_time'] = gmtTime();						
		}
		else
		{	
			$goods['is_group_fail'] = 2;
			$goods['complete_time'] = gmtTime();			
		}
	}
	
	if($goods['complete_time'] > 0)
		$goods['complete_time_format'] = toDate($goods['complete_time'],$GLOBALS['lang']['XY_TIMES_MOD_2']);
	else
		$goods['complete_time_format'] = "";
		
	$goods['rest_count'] = $goods['group_user'] - $goods['buy_count'];
	
	
	$GLOBALS['tpl']->assign('url',$_REQUEST['buyUrl']);
	$GLOBALS['tpl']->assign('goods',$goods);
	$GLOBALS['tpl']->assign('cityID',$_REQUEST['city']);
	
	$result['btnHTML'] = $GLOBALS['tpl']->fetch('Inc/goods/goods_btn_info.tpl');
	$result['dateHTML'] = $GLOBALS['tpl']->fetch('Inc/goods/goods_date_info.tpl');
	$result['statusHTML'] = $GLOBALS['tpl']->fetch('Inc/goods/goods_status_info.tpl'); 
	$result['tooltipHTML'] = $GLOBALS['tpl']->fetch('Inc/goods/goods_tooltip.tpl');
	//return '';
	return $result;
}

function getMessageStatus()
{
	$city_id = intval($_SESSION['cityID']);
	$sql = "SELECT id,content FROM ".$GLOBALS['db_config']['DB_PREFIX']."message WHERE rec_module='Message' AND rec_id = 0 AND pid = 0 AND reply_type = 0 AND status = 1 and city_id = ".$city_id." order by is_top desc,create_time desc LIMIT 0 , 3";
	$messages = $GLOBALS['db']->getAll($sql);
	
	$GLOBALS['tpl']->assign('messageUrl',$_REQUEST['messageUrl']);
	$GLOBALS['tpl']->assign('message_list',$messages);
	$html = $GLOBALS['tpl']->fetch('Inc/goods/message_list.tpl');
	
	$sql = "SELECT count(*) FROM ".$GLOBALS['db_config']['DB_PREFIX']."message WHERE rec_module='Message' AND rec_id = 0 AND pid = 0 AND reply_type = 0 AND status = 1 and city_id = ".$city_id;
	$count = intval($GLOBALS['db']->getOne($sql));
	
	return array("html"=>$html,"count"=>$count);
}
?>