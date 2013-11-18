<?php
require_once('init.php');
require('Sms/SmsPlf.class.php');
require('Mail/Mail.class.php');

// 生成soso文件 by awfigq 2010/07/23
require_once('sdd.php');

// 当前文件名
    if(!defined('_PHP_FILE_')) {
        if(IS_CGI) {
            //CGI/FASTCGI模式下
            $_temp  = explode('.php',$_SERVER["PHP_SELF"]);
            define('_PHP_FILE_',  rtrim(str_replace($_SERVER["HTTP_HOST"],'',$_temp[0].'.php'),'/'));
        }else {
            define('_PHP_FILE_',    rtrim($_SERVER["SCRIPT_NAME"],'/'));
        }
    }
    if(!defined('__ROOT__')) {
        // 网站URL根目录
        if( strtoupper(APP_NAME) == strtoupper(basename(dirname(_PHP_FILE_))) ) {
            $_root = dirname(dirname(_PHP_FILE_));
        }else {
            $_root = dirname(_PHP_FILE_);
        }
        $_root = (($_root=='/' || $_root=='\\')?'':$_root);
        $_root = str_replace("/services","",$_root);
        define('__ROOT__', $_root  );
    }
    
if(!defined('SHOP_NAME'))   
{
	if(isset($_SESSION['langItem']))
	{
		$langItem = $_SESSION['langItem'];
	}
	else 
	{
		$langItem = $db->getRow("SELECT `id`,`lang_name`,`show_name`,`time_zone`,`tmpl`,`seokeyword`,`seocontent`,`shop_title`,`shop_name`,`default`,`currency` FROM ".$db_config['DB_PREFIX']."lang_conf WHERE lang_name='$langSet'");
		$_SESSION['langItem'] = $langItem;
	}
	define("SHOP_NAME",$langItem['shop_name']);
}
    
if(!defined("AUTO_SEND_LOCK"))
{
	define("AUTO_SEND_LOCK",substr(getcwd(),0,-8)."Public/autosend.lock");
}
if(!defined("AUTO_SEND_MAIL_LOCK"))
{
	define("AUTO_SEND_MAIL_LOCK",substr(getcwd(),0,-8)."Public/autosendmail.lock");
}
if($_REQUEST['run']=='getRemainTime')
{
	$goods_id = intval($_REQUEST['id']);
	$now = gmtTime();
	$goods_end_time = intval($GLOBALS['db']->getOne("select promote_end_time from ".DB_PREFIX."goods where id=".$goods_id));
	echo intval($goods_end_time - $now);
}
if($_REQUEST['run']=='getRemainBeginTime')
{
	$goods_id = intval($_REQUEST['id']);
	$now = gmtTime();
	$goods_begin_time = intval($GLOBALS['db']->getOne("select promote_begin_time from ".DB_PREFIX."goods where id=".$goods_id));
	echo intval($goods_begin_time-$now);
}
if($_REQUEST['run']=='getNow')
{
	echo gmtTime()*1000;
}
if($_REQUEST['run']=='checkAutoSend')
{
	echo $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."send_list where status = 0");
}

if($_REQUEST['run']=='checkAutoSendMail')
{
	$time = gmtTime();
	$mail_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."mail_list where status = 0 and send_time<=".$time);
	$mail_list_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."mail_send_list where status = 0 and send_time<=".$time);
	if($mail_count>0||$mail_list_count>0)
	{
		echo 1;
	}
	else
		echo 0;
}

if($_REQUEST['run']=='autoSend')
{
	autoSend();
}

if($_REQUEST['run']=='autoSendMail')
{
    autoSendMail();
}

	// 定义重置队列群发
	function resetAutoSendIng()
	{

		@unlink(AUTO_SEND_LOCK);
		//$GLOBALS['db']->query("update ".DB_PREFIX."sys_conf set val ='0' where status = 1 and name = 'AUTO_SEND_ING'");
	}
	function resetAutoSendMailIng()
	{
		@unlink(AUTO_SEND_MAIL_LOCK);
		//$GLOBALS['db']->query("update ".DB_PREFIX."sys_conf set val ='0' where status = 1 and name = 'AUTO_SEND_MAIL_ING'");
	}
	function autoSend()
	{		
		//清空3天前的发送队列
		$GLOBALS['db']->query("delete from ".DB_PREFIX."send_list where status = 1 and ".gmtTime()."-send_time>".(3600*24*3));
		//ignore_user_abort(true);
		//服务端的全量变量
		$auto_begin_time = @file_get_contents(AUTO_SEND_LOCK);	
		$auto_begin_time = intval($auto_begin_time);	
		if (!file_exists(AUTO_SEND_LOCK)){
			
			@file_put_contents(AUTO_SEND_LOCK,gmtTime());
			register_shutdown_function("resetAutoSendIng");		
			send_msg_list();	
			resetAutoSendIng();
			//$GLOBALS['db']->query("update ".DB_PREFIX."sys_conf set val ='0' where status = 1 and name = 'AUTO_SEND_ING'");
			
		}else{
			//在自动执行中....
			if ( gmtTime() - $auto_begin_time > 300 ){//(5分钟)超时后，自动把状态改为：false
				resetAutoSendIng();
			}
		}
	}
	
	function autoSendMail()
	{
		//清空1天前的发送队列		
		$GLOBALS['db']->query("delete from ".DB_PREFIX."mail_send_list where status = 1 and ".gmtTime()."-send_time>".(3600*24));
		$auto_begin_time = @file_get_contents(AUTO_SEND_MAIL_LOCK);	
		$auto_begin_time = intval($auto_begin_time);	
		if (!file_exists(AUTO_SEND_MAIL_LOCK)){
			@file_put_contents(AUTO_SEND_MAIL_LOCK,gmtTime());
			register_shutdown_function("resetAutoSendMailIng");		
			pushMail(); //插入队列
			send_mail_list();		
			resetAutoSendMailIng();
		}else{
			//在自动执行中....
			if ( gmtTime() - $auto_begin_time > 300 ){//(5分钟)超时后，自动把状态改为：false
				resetAutoSendMailIng();
			}
		}
	}
	
	
	//发送函数
	
	// 发送邮件/短信消息队列 by hc
	function send_msg_list()
	{
//		$msg_list = M("SendList")->where("status=0")->findAll();
		$msg_list = $GLOBALS['db']->getAll("select `id`,`dest`,`title`,`content`,`create_time`,`send_type`,`status`,`send_time`,`bond_id` from ".DB_PREFIX."send_list where status = 0 limit 500");
		foreach($msg_list as $msg)
		{
//			$msg['status'] = 1;
//			$msg['send_time'] = gmtTime();
//			M("SendList")->save($msg);
			$GLOBALS['db']->query("update ".DB_PREFIX."send_list set status = 1,send_time =".gmtTime()." where id=".$msg['id']);
			//默认为已发送
			
			if($msg['send_type'] == 1)
			{
				if(eyooC("IS_SMS")==1)
				{
					$sms= new SmsPlf();
					
					if($sms->sendSMS($msg['dest'],$msg['content']))
					{
						if($msg['bond_id']>0) //团购券的发送，记录发送状态
						{
							$GLOBALS['db']->query("update ".DB_PREFIX."group_bond set is_send_msg = 1 where id=".$msg['bond_id']);
							$GLOBALS['db']->query("update ".DB_PREFIX."group_bond set send_count = send_count+1 where id=".$msg['bond_id']);
						}
					}
					else
					{
//						$msg['status'] = 0;
//						M("SendList")->save($msg);
//						$GLOBALS['db']->query("update ".DB_PREFIX."send_list set status = 0 where id=".$msg['id']);
//						if($msg['bond_id']>0) //团购券的发送，记录发送状态
//						{
//							$GLOBALS['db']->query("update ".DB_PREFIX."group_bond set is_send_msg = 0 where id=".$msg['bond_id']);
//						}
					}
				}				
			}
			if($msg['send_type'] == 0)
			{
				if(eyooC("MAIL_ON")==1)
				{
					$mail = new Mail();		
					$mail->AddAddress($msg['dest']);
					$mail->IsHTML(1); 
					$mail->Subject = $msg['title']; // 标题
					$mail->Body = $msg['content']; // 内容
					$mail->Send();
//					if($mail->ErrorInfo!='')
//					{
////						$msg['status'] = 0;
////						M("SendList")->save($msg);
//						$GLOBALS['db']->query("update ".DB_PREFIX."send_list set status = 0 where id=".$msg['id']);
//					}
				}
			}
		}
	}	
	
	// 发送邮件群发队列 by hc
	function send_mail_list()
	{
//		$msg_list = M("MailSendList")->where("status=0 and send_time <=".gmtTime())->findAll();
//echo "select * from ".DB_PREFIX."mail_send_list where status = 0 and send_time<=".gmtTime();exit;
		$msg_list = $GLOBALS['db']->getAll("select `id`,`mail_address`,`mail_title`,`mail_content`,`send_time`,`status`,`rec_module`,`rec_id` from ".DB_PREFIX."mail_send_list where status = 0 and send_time<=".gmtTime()." limit 500");
		
		foreach($msg_list as $k=>$msg)
		{
//			$msg['status'] = 1;			
//			M("MailSendList")->save($msg);
			$GLOBALS['db']->query("update ".DB_PREFIX."mail_send_list set status = 1 where id=".$msg['id']);
//			if($msg['rec_module']=='Email')
//			{
//				//M("MailList")->where("id=".$msg['rec_id'])->setField("status",1);  //设为已发送
//				$GLOBALS['db']->query("update ".DB_PREFIX."mail_list set status = 1 where id=".$msg['rec_id']);
//			}
			//默认为已发送
			
			if(eyooC("MAIL_ON")==1)
			{
					$mail = new Mail();		
					$mail->AddAddress($msg['mail_address']);
					$mail->IsHTML(1); 
					$mail->Subject = $msg['mail_title']; // 标题
					$mail->Body = $msg['mail_content']; // 内容
					$mail->Send();
//					echo $k."<br />";
//					if($mail->ErrorInfo!='')
//					{
////						$msg['status'] = 0;			
////						M("MailSendList")->save($msg);
//						$GLOBALS['db']->query("update ".DB_PREFIX."mail_send_list set status = 0 where id=".$msg['id']);
//					}
			}
		}
	}
	
	function pushMail()
	{
			$time = gmtTime();
   			//$mail_list = D("MailList")->where('send_time<='.$time.' and status=0')->findAll();	
   			$mail_list = $GLOBALS['db']->getAll("select `id`,`mail_title`,`mail_content`,`is_html`,`send_time`,`status`,`goods_id` from ".DB_PREFIX."mail_list where status = 0 and send_time<=".$time);
//			$allmail_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."mail_list");
//			//先删除邮件的发送人
//			foreach($allmail_list as $mail_item)
//			{
//				//M("MailSendList")->where("status=0 and rec_module='Email' and rec_id=".$mail_item['id'])->delete();
//				$GLOBALS['db']->query("delete from ".DB_PREFIX."mail_send_list where status = 0 and rec_module='Email' and rec_id=".$mail_item['id']);
//			}			
			foreach($mail_list as $mail_item)
			{		
				
				$pages = ceil(intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."mail_address_send_list where mail_id=".$mail_item['id']." and is_push=0"))/100);					
				
				for($i=1;$i<=$pages;$i++)
				{
					$limit = (($i-1)*100).",100";
					$address_send_list = $GLOBALS['db']->getAll("select `id`,`mail_address_id`,`mail_id`,`is_push` from ".DB_PREFIX."mail_address_send_list where mail_id=".$mail_item['id']." and is_push=0 limit ".$limit);
					foreach($address_send_list as $address_item)
					{
						$address_send_item = $address_item;
						//$address_item = D("MailAddressList")->where("status=1 and id='".$address_item['mail_address_id']."'")->find();
						$address_item = $GLOBALS['db']->getRow("select `id`,`mail_address`,`status`,`user_id`,`city_id` from ".DB_PREFIX."mail_address_list where status = 1 and id=".$address_item['mail_address_id']);
						if($address_item)
						{
							//$userinfo = D("User")->getById($address_item['user_id']);
							$userinfo = $GLOBALS['db']->getRow("select `id`,`user_name`,`nickname` from ".DB_PREFIX."user where id=".$address_item['user_id']);
							if($userinfo)
							{
								$username = $userinfo['user_name'];
								if($userinfo['nickname']!='')
								{
									$username.="(".$userinfo['nickname'].")";
								}
							}
							else 
							{
								$username = '匿名用户';
							}
	//						$mail = new Mail();	
	//						$mail->IsHTML(1); // 设置邮件格式为 HTML
							$GLOBALS['tpl']->assign("username",$username);
							$GLOBALS['tpl']->assign("uesrinfo",$userinfo);
							$mail_title = $mail_item['mail_title'];
							$GLOBALS['tpl']->assign("mail_title",$mail_title);
							//开始为邮件内容赋值
							if($mail_item['goods_id']==0)
							$mail_content = $mail_item['mail_content'];
							else
							{
									//$tpl = Think::instance('ThinkTemplate');
									$mail_tpl = file_get_contents(getcwd()."/../Public/mail_template/".eyooC("GROUP_MAIL_TMPL")."/".eyooC("GROUP_MAIL_TMPL").".html");  //邮件群发的模板				
									$mail_tpl = str_replace(eyooC("GROUP_MAIL_TMPL")."_files/",eyooC("SHOP_URL")."/Public/mail_template/".eyooC("GROUP_MAIL_TMPL")."/".eyooC("GROUP_MAIL_TMPL")."_files/",$mail_tpl);
				
									//开始定义模板变量
									//$v = M("Goods")->getById($mail_item['goods_id']);
									$v = $GLOBALS['db']->getRow("select `id`,`name_1`,`sn`,`cate_id`,`city_id`,`suppliers_id`,`click_count`,`cost_price`,`shop_price`,`market_price`,`promote_price`,`promote_begin_time`,`promote_end_time`,`create_time`,`update_time`,`type_id`,`goods_type`,`stock`,`brief_1`,`brand_id`,`is_best`,`is_hot`,`is_new`,`status`,`sort`,`seokeyword_1`,`seocontent_1`,`goods_desc_1`,`small_img`,`big_img`,`origin_img`,`define_small_img`,`is_define_small_img`,`is_inquiry`,`weight`,`spec_type`,`weight_unit`,`score`,`web_reviews`,`goods_reviews`,`min_user_time`,`special_note`,`max_bought`,`is_group_fail`,`complete_time`,`buy_count`,`group_user`,`user_count`,`earnest_money`,`group_bond_end_time`,`expand1`,`expand2`,`expand3`,`expand4`,`goods_show_name`,`u_name`,`referrals`,`close_referrals`,`goods_short_name`,`fail_buy_count`,`free_delivery_amount`,`allow_combine_delivery`,`allow_sms` from ".DB_PREFIX."goods where id=".$mail_item['goods_id']);
									
									//$city_name
									//$city_name = M("GroupCity")->where("id=".$v['city_id'])->getField("name");
									$city_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."group_city where id=".$v['city_id']);
									$GLOBALS['tpl']->assign("city_name",$city_name);
									
									//$shop_name
									$shop_name = SHOP_NAME;
									$GLOBALS['tpl']->assign("shop_name",$shop_name);
									
									//$cancel_url
									$cancel_url = eyooC("SHOP_URL")."/index.php?m=Index&a=unSubScribe&email=".$address_item['mail_address'];
									$GLOBALS['tpl']->assign("cancel_url",$cancel_url);
									
									//$sender_email
									$sender_email = eyooC("REPLY_ADDRESS");
									$GLOBALS['tpl']->assign("sender_email",$sender_email);
									
									//$send_date 
									$send_date = toDate(gmtTime(),'Y年m月d日');
									$weekarray = array("日","一","二","三","四","五","六");
									$send_date .= " 星期".$weekarray[toDate(gmtTime(),"w")];
									$GLOBALS['tpl']->assign("send_date",$send_date);
									
									//$shop_url
									$shop_url = eyooC("SHOP_URL");
									$GLOBALS['tpl']->assign("shop_url",$shop_url);
									
									//$tel_number
									$tel_number = eyooC("TEL");
									$GLOBALS['tpl']->assign("tel_number",$tel_number);
									
									//$tg_info
									//$tg_info = D("Goods")->getGoodsItem($v['id'],$v['city_id']);
									$tg_info = $GLOBALS['db']->getRow("select `id`,`name_1`,`sn`,`cate_id`,`city_id`,`suppliers_id`,`click_count`,`cost_price`,`shop_price`,`market_price`,`promote_price`,`promote_begin_time`,`promote_end_time`,`create_time`,`update_time`,`type_id`,`goods_type`,`stock`,`brief_1`,`brand_id`,`is_best`,`is_hot`,`is_new`,`status`,`sort`,`seokeyword_1`,`seocontent_1`,`goods_desc_1`,`small_img`,`big_img`,`origin_img`,`define_small_img`,`is_define_small_img`,`is_inquiry`,`weight`,`spec_type`,`weight_unit`,`score`,`web_reviews`,`goods_reviews`,`min_user_time`,`special_note`,`max_bought`,`is_group_fail`,`complete_time`,`buy_count`,`group_user`,`user_count`,`earnest_money`,`group_bond_end_time`,`expand1`,`expand2`,`expand3`,`expand4`,`u_name`,`referrals`,`close_referrals`,`goods_short_name`,`fail_buy_count`,`free_delivery_amount`,`allow_combine_delivery`,`allow_sms` from ".DB_PREFIX."goods where id=".$v['id']);
									$tg_info['title'] = $tg_info['goods_short_name']!=''?$tg_info['goods_short_name']:$tg_info['name_1'];
									$tg_info['price'] = formatPrice($tg_info['shop_price']);
									$tg_info['origin_price'] = formatPrice($tg_info['market_price']);
									if($tg_info['market_price']!=0)
									$tg_info['discount'] = round($tg_info['shop_price']/$tg_info['market_price'],2)*10;
									else 
									$tg_info['discount'] = 10;
									$tg_info['save_money'] = formatPrice($tg_info['market_price'] - $tg_info['shop_price']);
									$tg_info['big_img'] = eyooC("SHOP_URL").$tg_info['big_img'];
									$tg_info['desc'] = str_replace("./Public/",eyooC("SHOP_URL")."/Public/",$tg_info['goods_desc_1']);
									$GLOBALS['tpl']->assign("tg_info",$tg_info);
									
									//$sale_info
									$sql = "select sd.*,(select s.web from ".DB_PREFIX."suppliers as s where s.id = sd.supplier_id) as url from ".DB_PREFIX."suppliers_depart as sd where sd.is_main=1 and sd.supplier_id= ".$v['suppliers_id'];

									$sale_info = $GLOBALS['db']->getRow($sql);
									$sale_info['map_url'] = $sale_info['map'];
									$sale_info['tel_num'] = $sale_info['tel'];
									$sale_info['title'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."suppliers where id =".$sale_info['supplier_id']);
			
									$GLOBALS['tpl']->assign("sale_info",$sale_info);
									
									//$referral
									$referral['amount'] = eyooC("REFERRALS_MONEY");
									
									if(eyooC("REFERRAL_TYPE") == 0)
									{
										$referral['amount'] = formatPrice(($referral['amount']));
									}
									else
									{
										$referral['amount'] = $referral['amount']."积分";
									}
								
									if(eyooC("URL_ROUTE")==0)
									$referral['url'] = eyooC("SHOP_URL")."/index.php?m=Referrals&a=index";
									else
									$referral['url'] = eyooC("SHOP_URL")."/Referrals-index.html";
									$GLOBALS['tpl']->assign("referral",$referral);
									
	//								ob_start();
	//								eval('?' . '>' .$tpl->parse($mail_tpl));
	//								$content = ob_get_clean();	
									
									$content = $GLOBALS['tpl']->fetch_str($mail_tpl);
									$content = $GLOBALS['tpl']->_eval($content);
									
									$mail_content = $content;
									
									
							}//end 通知模板的赋值
							
							//$cancel_url
							$cancel_url = eyooC("SHOP_URL")."/index.php?m=Index&a=unSubScribe&email=".$address_item['mail_address'];
							
							$mail_content = "如不想继续收".SHOP_NAME."的邮件，您可随时<a href='".$cancel_url."' title='取消订阅'>取消订阅</a><br /><br />".$mail_content;
							
							$mail_title = str_replace("{\$username}",$username,$mail_title);
							$mail_content = str_replace("{\$username}",$username,$mail_content);
							//echo $mail_content;exit;
	//						$mail->Subject = $mail_title; // 标题					
	//						$mail->Body =  $mail_content; // 内容
	//						$mail->AddAddress($address_item['mail_address'],$username);	
	//						if(!$mail->Send())
	//						{
	//							$this->error($mail->ErrorInfo,$ajax);
	//						}	
	
							// 修改为插入邮件群发队列
							$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."mail_send_list where status = 0 and mail_address='".$address_item['mail_address']."' and rec_module='Email' and rec_id=".$mail_item['id']);
							//if(M("MailSendList")->where("status=0 and mail_address='".$address_item['mail_address']."' and rec_module='Email' and rec_id=".$mail_item['id'])->count()==0)
							if($count==0)
							{
								$sql = "insert into ".DB_PREFIX."mail_send_list values(0,'".$address_item['mail_address']."','".$mail_title."','".addslashes($mail_content)."','".$mail_item['send_time']."',0,'Email','".$mail_item['id']."')";
								$GLOBALS['db']->query($sql);
								$sql = "update ".DB_PREFIX."mail_address_send_list set is_push = 1 where id =".$address_send_item['id'];
								$GLOBALS['db']->query($sql);
							} //为避免重复插入队列						
						}			
					}
				}			
				//验证插入队列数与关联数是否一样
				$address_list = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."mail_address_send_list where mail_id=".$mail_item['id']);
				$push_address_list = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."mail_address_send_list where mail_id=".$mail_item['id']." and is_push=1");
				if($push_address_list==$address_list)
				{
				//将要插入队列的邮件设为已发送 

					if($address_list>0)
					$GLOBALS['db']->query("update ".DB_PREFIX."mail_list set status = 1 where id = ".$mail_item['id']);	
				}					
			}// end foreach
			
	}

?>