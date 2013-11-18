<?php
include_once ('./global/constant.php');
function setBaseMoney($money, $currency_id)
{
	return $money;
}
function getBaseMoney($money, $currency_id)
{
	return str_replace ( ",", "", number_format ( round ( $money, 2 ), 2 ) );
}

function msubstr($str, $start = 5, $length, $charset = "utf-8", $suffix = true)
{

	if (function_exists ( "mb_substr" ))
	{
		$slice = mb_substr ( $str, $start, $length, $charset );
		if ($suffix & $slice != $str)
			return $slice . "…";
		return $slice;
	} elseif (function_exists ( 'iconv_substr' ))
	{
		return iconv_substr ( $str, $start, $length, $charset );
	}
	$re ['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	$re ['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
	$re ['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
	$re ['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
	preg_match_all ( $re [$charset], $str, $match );
	$slice = join ( "", array_slice ( $match [0], $start, $length ) );
	if ($suffix && $slice != $str)
		return $slice . "…";
	return $slice;
}



function msubstrs($str, $start = 0, $length = 10, $charset = "utf-8", $suffix = true)
{

	if (function_exists ( "mb_substr" ))
	{
		$slice = mb_substr ( $str, $start, $length, $charset );
		if ($suffix & $slice != $str)
			return $slice . "…";
		return $slice;
	} elseif (function_exists ( 'iconv_substr' ))
	{
		return iconv_substr ( $str, $start, $length, $charset );
	}
	$re ['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
	$re ['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
	$re ['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
	$re ['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
	preg_match_all ( $re [$charset], $str, $match );
	$slice = join ( "", array_slice ( $match [0], $start, $length ) );
	if ($suffix && $slice != $str)
		return $slice . "…";
	return $slice;
}
//用于加载读取相应语言包中的语言变量，前台语言
function load_lang($key, $lang_id)
{
	$lang_set = S ( "CACHE_LANG_SET" );
	echo '0k22';
	if ($currency_radio === false)
	{
		//$lang_set = D("LangConf")->where("id=".$lang_id)->getField("lang_name"); ==SQL优化==
		$lang_set = M ()->query ( "select lang_name from " . C ( "DB_PREFIX" ) . "lang_conf where id=" . $lang_id );
		$lang_set = $lang_set [0] ['lang_name'];
		S ( "CACHE_LANG_SET", $lang_set );
	}
	//加载当前语言的语言包
	L ( include './home/Lang/' . $lang_set . '/common.php' );
	return L ( $key );
}
//记录会员预存款变化明细
//$memo 格式为 #LANG_KEY#memos  ##之间所包含的是语言包的变量
function user_money_log($user_id, $rec_id, $rec_module, $money, $memo, $onlylog = false)
{
	$user_id = intval ( $user_id );
	$money = floatval ( $money );
	//$langs = D("LangConf")->findAll();  ==SQL优化==
	$langs = M ()->query ( "select id from " . C ( "DB_PREFIX" ) . "lang_conf" );
	$log_data = array ();
	$log_data ['user_id'] = $user_id;
	$log_data ['money'] = $money;
	$log_data ['rec_id'] = $rec_id;
	$log_data ['rec_module'] = $rec_module;
	$log_data ['create_time'] = gmtTime ();
	foreach ( $langs as $lang )
	{
		$lang_memo = $memo;
		preg_match_all ( "/#([^#]*)#/", $memo, $keys );
		foreach ( $keys [1] as $key )
		{
			$lang_memo = preg_replace ( "/#[^#]*#/", load_lang ( $key, $lang ['id'] ), $lang_memo );
		}
		$log_data ['memo_' . $lang ['id']] = $lang_memo;
	}
	//记录会员预存款变化明细
	M ( "UserMoneyLog" )->add ( $log_data );
	if ($onlylog == false)
	{
		//增加会员的预存款金额
		$sql_str = 'update ' . C ( "DB_PREFIX" ) . 'user set money = money + ' . floatval ( $money ) . ' where id = ' . $user_id;
		D ()->execute ( $sql_str );
	}
	return true;
}
/**
 * 记录帐户资金变化明细
 * @param unknown_type $payment_id 支付id fanwe_payment.id
 * @param unknown_type $operator_id 会员ID或管理员ID
 * @param unknown_type $operator_module User或Admin
 * @param unknown_type $money 变更金额
 * @param unknown_type $memo 备注
 * @param unknown_type $onlylog 仅插入备注，而不变更fanwe_payment.money
 * @param unknown_type $payment_name fanwe_payment.name
 * @param unknown_type $operator_name 会员名或管理员名称
 * @return unknown
 */
function payment_money_log($payment_id, $operator_id, $rec_id, $rec_module, $money, $memo, $onlylog = false, $operator_module = 'User', $payment_name = '', $operator_name = '')
{
	$payment_id = intval ( $payment_id );
	$operator_id = intval ( $operator_id );
	$money = floatval ( $money );
	if (empty ( $payment_name ))
	{
		$payment_name = S ( "CACHE_PAYMENT_NAME_" . $payment_id );
		if ($payment_name === false)
		{
			//$payment_name = M('Payment')->where("id=".$payment_id)->getField("name_1"); ==SQL优化==
			$payment_name = M ()->query ( "select name_1 from " . C ( "DB_PREFIX" ) . "payment where id=" . $payment_id );
			$payment_name = $payment_name [0] ['name_1'];
			S ( "CACHE_PAYMENT_NAME_" . $payment_id, $payment_name );
		}
	}
	if (empty ( $operator_name ))
	{
		if ($operator_module == 'User')
		{
			//$operator_name = M('User')->where("id=".$operator_id)->getField("user_name"); ==SQL优化==
			$operator_name = M ()->query ( "Select user_name from " . C ( "DB_PREFIX" ) . "user where id = " . $operator_id );
			$operator_name = $operator_name [0] ['user_name'];
		} elseif ($operator_module == 'Admin')
		{
			//$operator_name = M('Admin')->where("id=".$operator_id)->getField("adm_name"); ==SQL优化==
			$operator_name = M ()->query ( "select adm_name from " . C ( "DB_PREFIX" ) . "admin where id =" . $operator_id );
			$operator_name = $operator_name [0] ['adm_name'];
		}
	}
	$log_data = array ();
	$log_data ['payment_id'] = $payment_id;
	$log_data ['payment_name'] = $payment_name;
	$log_data ['operator_id'] = $operator_id;
	$log_data ['operator_name'] = $operator_name;
	$log_data ['money'] = $money;
	$log_data ['operator_module'] = $operator_module;
	$log_data ['rec_id'] = $rec_id;
	$log_data ['rec_module'] = $rec_module;
	$log_data ['log_msg'] = $memo;
	$log_data ['create_time'] = gmtTime ();
	$log_data ['ip'] = get_client_ip ();
	//dump($log_data);
	M ( "PaymentMoneyLog" )->add ( $log_data );
	if ($onlylog == false)
	{
		$sql_str = 'update ' . C ( "DB_PREFIX" ) . 'payment set money = money + ' . floatval ( $money ) . ' where id = ' . $payment_id;
		M ()->execute ( $sql_str );
	}
	return true;
}
//记录会员预存款变化明细
//$memo 格式为 #LANG_KEY#memos#LANG_KEY#  ##之间所包含的是语言包的变量
function user_score_log($user_id, $rec_id, $rec_module, $score, $memo, $onlylog = false)
{
	$user_id = intval ( $user_id );
	//$langs = D("LangConf")->findAll(); ==SQL优化==
	$langs = M ()->query ( "select id from " . C ( "DB_PREFIX" ) . "lang_conf" );
	$log_data = array ();
	$log_data ['user_id'] = $user_id;
	$log_data ['score'] = $score;
	$log_data ['rec_id'] = $rec_id;
	$log_data ['rec_module'] = $rec_module;
	$log_data ['create_time'] = gmtTime ();
	foreach ( $langs as $lang )
	{
		$lang_memo = $memo;
		preg_match_all ( "/#([^#]*)#/", $memo, $keys );
		foreach ( $keys [1] as $key )
		{
			$lang_memo = preg_replace ( "/#[^#]*#/", load_lang ( $key, $lang ['id'] ), $lang_memo );
		}
		$log_data ['memo_' . $lang ['id']] = $lang_memo;
	}
	//记录会员预存款变化明细
	M ( "UserScoreLog" )->add ( $log_data );
	if ($onlylog == false)
	{
		$sql_str = 'update ' . C ( "DB_PREFIX" ) . 'user set score = score + ' . intval ( $score ) . ' where id = ' . $user_id;
		M ()->execute ( $sql_str );
	}
	return true;
}
function order_paid($payment_log_id, $money, $payment_id, $currency_id)
{
	$result = array ();
	$result ['order_id'] = 0;
	$PaymentLog = M ( "PaymentLog" );
	//$payment_log_vo = $PaymentLog->getById ($payment_log_id);==SQL优化==
	$payment_log_vo = M ()->query ( "select id,is_paid,rec_module,rec_id,payment_id from " . C ( "DB_PREFIX" ) . "payment_log where id = " . $payment_log_id );
	$payment_log_vo = $payment_log_vo [0];
	Log::record ( "order_paid:" . $payment_log_id . ";" . $money );
	Log::save ();
	if ($payment_log_vo == false)
	{
		$result ['status'] = false;
		$result ['info'] = L ( 'INVALID_PAY_LOG_ID' ) . $payment_log_id;
		$result ['data'] = L ( 'INVALID_PAY_LOG_ID' ) . $payment_log_id;
		return $result;
	}
	//add by chenfq 2010-04-05
	if ($payment_log_vo ['is_paid'] == 1)
	{
		/*
        	$result['status'] = false;
        	$result['info'] = L('PAY_LOG_ID').$payment_log_id.L('PAY_LOG_ID_INVALID');
        	$result['data'] = L('PAY_LOG_ID').$payment_log_id.L('PAY_LOG_ID_INVALID');
        	*/
		$result ['status'] = true;
		$result ['info'] = L ( 'PAY_LOG_ID' ) . ':' . $payment_log_id . L ( 'PAY_MONEY' ) . ':' . $money . L ( 'PAY_SUCCESS' );
		$result ['data'] = L ( 'PAY_LOG_ID' ) . ':' . $payment_log_id . L ( 'PAY_MONEY' ) . ':' . $money . L ( 'PAY_SUCCESS' );
		if ($payment_log_vo ['rec_module'] == 'Order')
		{
			$result ['order_id'] = $payment_log_vo ['rec_id'];
		}
		return $result;
	}
	if ($payment_log_vo ['rec_module'] == 'Order')
	{
		$status = order_paid_in ( $payment_log_id, $money, $payment_id, $currency_id );
	} elseif ($payment_log_vo ['rec_module'] == 'UserIncharge')
	{ //在线冲值
		/* 修改此次支付操作的状态为已付款  add by chenfq 2010-04-05*/
		//$payment_log_vo['is_paid'] = 1;==SQL优化==
		//$PaymentLog->save($payment_log_vo);==SQL优化==
		M ()->execute ( "update " . C ( "DB_PREFIX" ) . "payment_log set is_paid = 1 where id = " . $payment_log_id );
		$model = M ( "UserIncharge" );
		//$vo = $model->getById($payment_log_vo['rec_id']);==SQL优化==
		$vo = M ()->query ( "select id,user_id,money from " . C ( "DB_PREFIX" ) . "user_incharge where id = " . $payment_log_vo ['rec_id'] );
		$vo = $vo [0];
		if ($vo == false)
		{
			$status = L ( 'INVALID_USER_INCHAREG_ID' );
		}
		//$currency = M( "Currency" )->getById(intval($currency_id));
		//$vo['money'] = $money * $currency['radio'];
		//$vo['update_time'] = gmtTime();==SQL优化==
		//$vo['status'] = 1;==SQL优化==
		//$model->save($vo);==SQL优化==
		M ()->execute ( "update " . C ( "DB_PREFIX" ) . "user_incharge set update_time =" . gmtTime () . ",status = 1 where id=" . $payment_log_vo ['rec_id'] );
		$status = user_money_log ( $vo ['user_id'], $vo ['id'], 'UserIncharge', $vo ['money'], L ( "ORDER_CHARGE_MEMO_3" ) );
		//add by chenfq 2010-06-5  记录帐户资金变化明细 begin
		payment_money_log ( $payment_log_vo ['payment_id'], $vo ['user_id'], $vo ['id'], 'UserIncharge', $vo ['money'], '会员在线冲值：' . $vo ['money'], false, 'User', '', '' );
		//add by chenfq 2010-06-5  记录帐户资金变化明细 end
		clear_user_order_cache ( 0 );
		S ( "CACHE_USER_INFO_" . $vo ['user_id'], NULL );
		send_userincharge_sms ( $vo ['id'] );
	}
	if ($status === true)
	{
		$result ['status'] = true;
		$result ['info'] = L ( 'PAY_LOG_ID' ) . ':' . $payment_log_id . L ( 'PAY_MONEY' ) . ':' . $money . L ( 'PAY_SUCCESS' );
		$result ['data'] = L ( 'PAY_LOG_ID' ) . ':' . $payment_log_id . L ( 'PAY_MONEY' ) . ':' . $money . L ( 'PAY_SUCCESS' );
		if ($payment_log_vo ['rec_module'] == 'Order')
		{
			$result ['order_id'] = $payment_log_vo ['rec_id'];
		}
	} else
	{
		$result ['status'] = false;
		$result ['info'] = $status;
		$result ['data'] = $status;
	}
	return $result;
}
//为定单收款发送短信, $order_incharge_id : 收款单ID  //增加的邮件收款通知补充在里面
//修改 by hc 默认send 为false 存入DB
function send_orderpaid_sms($order_incharge_id, $send = false)
{
	//开始短信通知
	if (eyooC ( "IS_SMS" ) == 1 && eyooC ( "PAYMENT_SMS" ) == 1)
	{
		//$order_incharge_vo = M("OrderIncharge")->getById($order_incharge_id);==SQL优化==
		$order_incharge_vo = M ()->query ( "select order_id,money from " . C ( "DB_PREFIX" ) . "order_incharge where id = " . $order_incharge_id );
		$order_incharge_vo = $order_incharge_vo [0];
		//获取定单号
		//$payment_notify['order_sn'] = D("Order")->where("id=".$order_incharge_vo['order_id'])->getField("sn");==SQL优化==
		$order_sn = M ()->query ( "select sn from " . C ( "DB_PREFIX" ) . "order where id=" . $order_incharge_vo ['order_id'] );
		$order_sn = $order_sn [0] ['sn'];
		$payment_notify ['order_sn'] = $order_sn;
		//$user_id =  D("Order")->where("id=".$order_incharge_vo['order_id'])->getField("user_id");==SQL优化==
		//$user = D("User")->getById($user_id);==SQL优化==
		$user = M ()->query ( "select u.mobile_phone from " . C ( "DB_PREFIX" ) . "order as o left join " . C ( "DB_PREFIX" ) . "user as u on o.user_id = u.id where o.id = " . $order_incharge_vo ['order_id'] );
		$user = $user [0];
		//$currency = M( "Currency" )->getById(intval($order_incharge_vo['currency_id']));
		$payment_notify ['money'] = formatPrice ( $order_incharge_vo ['money'] );
		//模板解析
		//$payment_sms_tmpl = M("MailTemplate")->where("name='payment_sms'")->getField("mail_content");==SQL优化==
		$payment_sms_tmpl = M ()->query ( "select mail_content from " . C ( "DB_PREFIX" ) . " where name = 'payment_sms'" );
		$payment_sms_tmpl = $payment_sms_tmpl [0] ['mail_content'];
		$tpl = Think::instance ( 'ThinkTemplate' );
		ob_start ();
		eval ( '?' . '>' . $tpl->parse ( $payment_sms_tmpl ) );
		$content = ob_get_clean ();
		if (! empty ( $user ['mobile_phone'] ))
		{
			if ($send)
			{
				$sms = D ( "SmsPlf" );
				$sms->sendSMS ( $user ['mobile_phone'], $content );
			} else
			{
				$sendData ['dest'] = $user ['mobile_phone'];
				$sendData ['title'] = '';
				$sendData ['content'] = $content;
				$sendData ['create_time'] = gmtTime ();
				$sendData ['send_type'] = 1; //短信
				M ( "SendList" )->add ( $sendData );
			}
		}
	}
	//开始邮件通知
	if (eyooC ( "MAIL_ON" ) == 1 && eyooC ( "SEND_PAID_MAIL" ) == 1)
	{
		//$order_incharge_vo = M("OrderIncharge")->getById($order_incharge_id);==SQL优化==
		$order_incharge_vo = M ()->query ( "select order_id,money from " . C ( "DB_PREFIX" ) . "order_incharge where id = " . $order_incharge_id );
		$order_incharge_vo = $order_incharge_vo [0];
		//获取定单号
		//$payment_notify['order_sn'] = D("Order")->where("id=".$order_incharge_vo['order_id'])->getField("sn");==SQL优化==
		$order_sn = M ()->query ( "select sn from " . C ( "DB_PREFIX" ) . "order where id=" . $order_incharge_vo ['order_id'] );
		$order_sn = $order_sn [0] ['sn'];
		$payment_notify ['order_sn'] = $order_sn;
		//$user_id =  D("Order")->where("id=".$order_incharge_vo['order_id'])->getField("user_id");==SQL优化==
		//$user = D("User")->getById($user_id);==SQL优化==
		$user = M ()->query ( "select u.email,u.user_name from " . C ( "DB_PREFIX" ) . "order as o left join " . C ( "DB_PREFIX" ) . "user as u on o.user_id = u.id where o.id = " . $order_incharge_vo ['order_id'] );
		$user = $user [0];
		//$currency = M( "Currency" )->getById(intval($order_incharge_vo['currency_id']));
		//$payment_notify['money'] = $currency['unit']. (round(($order_incharge_vo['money'] * $currency['radio']),2));
		$payment_notify ['money'] = formatPrice ( $order_incharge_vo ['money'] );
		//模板解析
		//$payment_tmpl = M("MailTemplate")->where("name='payment_mail'")->find();==SQL优化==
		$payment_tmpl = M ()->query ( "select mail_content,mail_title from " . C ( "DB_PREFIX" ) . "mail_template where name = 'payment_mail'" );
		$payment_tmpl = $payment_tmpl [0];
		$tpl = Think::instance ( 'ThinkTemplate' );
		ob_start ();
		eval ( '?' . '>' . $tpl->parse ( $payment_tmpl ['mail_content'] ) );
		$content = ob_get_clean ();
		if ($send)
		{
			$mail = new Mail ();
			$mail->AddAddress ( $user ['email'], $user ['user_name'] );
			$mail->IsHTML ( 0 );
			$mail->Subject = $payment_tmpl ['mail_title']; // 标题
			$mail->Body = $content; // 内容
			$mail->Send ();
		} else
		{
			$sendData ['dest'] = $user ['email'];
			$sendData ['title'] = $payment_tmpl ['mail_title'];
			$sendData ['content'] = $content;
			$sendData ['create_time'] = gmtTime ();
			$sendData ['send_type'] = 0;
			M ( "SendList" )->add ( $sendData );
		}
	}
}
//为充值发送短信, $order_incharge_id : 收款单ID
//修改 by hc
function send_userincharge_sms($user_incharge_id, $send = false)
{
	//开始短信通知
	if (eyooC ( "IS_SMS" ) == 1 && eyooC ( "PAYMENT_SMS" ) == 1)
	{
		//$user_incharge_vo = M("UserIncharge")->getById($user_incharge_id);==SQL优化==
		$user_incharge_vo = M ()->query ( "select id,sn,user_id,money from " . C ( "DB_PREFIX" ) . "user_incharge where id = " . $user_incharge_id );
		$user_incharge_vo = $user_incharge_vo [0];
		//获取定单号
		$payment_notify ['order_sn'] = $user_incharge_vo ['sn'];
		$user_id = $user_incharge_vo ['user_id'];
		//$user = D("User")->getById($user_id);==SQL优化==
		$user = M ()->query ( "select mobile_phone from " . C ( "DB_PREFIX" ) . "user where id = " . $user_id );
		$user = $user [0];
		//$currency = M( "Currency" )->getById(intval(M("Payment")->where("id=".$user_incharge_vo['payment'])->getField("currency")));
		//$payment_notify['money'] = $currency['unit']. (round(($user_incharge_vo['money'] * $currency['radio']),2));
		$payment_notify ['money'] = formatPrice ( $user_incharge_vo ['money'] );
		//模板解析
		//$payment_sms_tmpl = M("MailTemplate")->where("name='payment_sms'")->getField("mail_content");==SQL优化==
		$payment_sms_tmpl = M ()->query ( "select mail_content from " . C ( "DB_PREFIX" ) . "mail_template where name = 'payment_sms'" );
		$payment_sms_tmpl = $payment_sms_tmpl [0] ['mail_content'];
		$tpl = Think::instance ( 'ThinkTemplate' );
		ob_start ();
		eval ( '?' . '>' . $tpl->parse ( $payment_sms_tmpl ) );
		$content = ob_get_clean ();
		if (! empty ( $user ['mobile_phone'] ))
		{
			if ($send)
			{
				$sms = D ( "SmsPlf" );
				$sms->sendSMS ( $user ['mobile_phone'], $content );
			} else
			{
				$sendData ['dest'] = $user ['mobile_phone'];
				$sendData ['title'] = '';
				$sendData ['content'] = $content;
				$sendData ['create_time'] = gmtTime ();
				$sendData ['send_type'] = 1; //短信
				M ( "SendList" )->add ( $sendData );
			}
		}
	}
	//开始邮件通知
	if (eyooC ( "MAIL_ON" ) == 1 && eyooC ( "SEND_PAID_MAIL" ) == 1)
	{
		//$user_incharge_vo = M("UserIncharge")->getById($user_incharge_id);==SQL优化==
		$user_incharge_vo = M ()->query ( "select id,sn,user_id,money from " . C ( "DB_PREFIX" ) . "user_incharge where id = " . $user_incharge_id );
		$user_incharge_vo = $user_incharge_vo [0];
		//获取定单号
		$payment_notify ['order_sn'] = $user_incharge_vo ['sn'];
		$user_id = $user_incharge_vo ['user_id'];
		//$user = D("User")->getById($user_id);==SQL优化==
		$user = M ()->query ( "select id,email,user_name from " . C ( "DB_PREFIX" ) . "user where id =" . $user_id );
		$user = $user [0];
		//$currency = M( "Currency" )->getById(intval(M("Payment")->where("id=".$user_incharge_vo['payment'])->getField("currency")));
		//$payment_notify['money'] = $currency['unit']. (round(($user_incharge_vo['money'] * $currency['radio']),2));
		$payment_notify ['money'] = formatPrice ( $user_incharge_vo ['money'] );
		//模板解析
		//$payment_tmpl = M("MailTemplate")->where("name='payment_mail'")->find();==SQL优化==
		$payment_tmpl = M ()->query ( "select mail_content,mail_title from " . C ( "DB_PREFIX" ) . "mail_template where name ='payment_mail'" );
		$payment_tmpl = $payment_tmpl [0];
		$tpl = Think::instance ( 'ThinkTemplate' );
		ob_start ();
		eval ( '?' . '>' . $tpl->parse ( $payment_tmpl ['mail_content'] ) );
		$content = ob_get_clean ();
		if ($send)
		{
			$mail = new Mail ();
			$mail->AddAddress ( $user ['email'], $user ['user_name'] );
			$mail->IsHTML ( 0 );
			$mail->Subject = $payment_tmpl ['mail_title']; // 标题
			$mail->Body = $content; // 内容
			$mail->Send ();
		} else
		{
			$sendData ['dest'] = $user ['email'];
			$sendData ['title'] = $payment_tmpl ['mail_title'];
			$sendData ['content'] = $content;
			$sendData ['create_time'] = gmtTime ();
			$sendData ['send_type'] = 0;
			M ( "SendList" )->add ( $sendData );
		}
	}
}
//发货短信通知   发货邮件通知也补在里面
function send_delivery_sms($delivery_id, $send = false)
{
	if (eyooC ( "IS_SMS" ) == 1 && eyooC ( "DELIVERY_SMS" ) == 1)
	{
		//$delivery_vo = M("OrderConsignment")->getById($delivery_id);==SQL优化==
		$delivery_vo = M ()->query ( "select id,order_id,delivery_code from " . C ( "DB_PREFIX" ) . "order_consignment where id = " . $delivery_id );
		$delivery_vo = $delivery_vo [0];
		//获取定单号
		//$delivery_notify['order_sn'] = M("Order")->where("id=".$delivery_vo['order_id'])->getField("sn");==SQL优化==
		$order_sn = M ()->query ( "select sn from " . C ( "DB_PREFIX" ) . "order where id = " . $delivery_vo ['order_id'] );
		$order_sn = $order_sn [0] ['sn'];
		$delivery_notify ['order_sn'] = $order_sn;
		//$user_id =  M("Order")->where("id=".$delivery_vo['order_id'])->getField("user_id");==SQL优化==
		//$user = D("User")->getById($user_id);==SQL优化==
		$user = M ()->query ( "select u.mobile_phone from " . C ( "DB_PREFIX" ) . "order as o left join " . C ( "DB_PREFIX" ) . "user as u on o.user_id = u.id where o.id = " . $order_incharge_vo ['order_id'] );
		$user = $user [0];
		$delivery_notify ['delivery_code'] = $delivery_vo ['delivery_code'];
		//模板解析
		//$payment_sms_tmpl = M("MailTemplate")->where("name='delivery_sms'")->getField("mail_content");==SQL优化==
		$payment_sms_tmpl = M ()->query ( "select mail_content from " . C ( "DB_PREFIX" ) . "mail_template where name='delivery_sms'" );
		$payment_sms_tmpl = $payment_sms_tmpl [0] ['mail_content'];
		$tpl = Think::instance ( 'ThinkTemplate' );
		ob_start ();
		eval ( '?' . '>' . $tpl->parse ( $payment_sms_tmpl ) );
		$content = ob_get_clean ();
		if ($send)
		{
			$sms = D ( "SmsPlf" );
			$sms->sendSMS ( $user ['mobile_phone'], $content );
		} else
		{
			$sendData ['dest'] = $user ['mobile_phone'];
			$sendData ['title'] = '';
			$sendData ['content'] = $content;
			$sendData ['create_time'] = gmtTime ();
			$sendData ['send_type'] = 1; //短信
			M ( "SendList" )->add ( $sendData );
		}
	}
	if (eyooC ( "MAIL_ON" ) == 1 && eyooC ( "SEND_DELIVERY_MAIL" ) == 1)
	{
		//$delivery_vo = M("OrderConsignment")->getById($delivery_id);==SQL优化==
		$delivery_vo = M ()->query ( "select order_id,delivery_code from " . C ( "DB_PREFIX" ) . "order_consignment where id =" . $delivery_id );
		$delivery_vo = $delivery_vo [0];
		//获取定单号
		//$delivery_notify['order_sn'] = M("Order")->where("id=".$delivery_vo['order_id'])->getField("sn");==SQL优化==
		$order_sn = M ()->query ( "select sn from " . C ( "DB_PREFIX" ) . "order where id =" . $delivery_vo ['order_id'] );
		$order_sn = $order_sn [0] ['sn'];
		$delivery_notify ['order_sn'] = $order_sn;
		//$user_id =  M("Order")->where("id=".$delivery_vo['order_id'])->getField("user_id");==SQL优化==
		//$user = D("User")->getById($user_id);==SQL优化==
		$user = M ()->query ( "select u.email,u.user_name from " . C ( "DB_PREFIX" ) . "order as o left join " . C ( "DB_PREFIX" ) . "user as u on o.user_id = u.id where o.id = " . $order_incharge_vo ['order_id'] );
		$user = $user [0];
		$delivery_notify ['delivery_code'] = $delivery_vo ['delivery_code'];
		//模板解析
		//$payment_tmpl = M("MailTemplate")->where("name='delivery_mail'")->find();==SQL优化==
		$payment_tmpl = M ()->query ( "select mail_title,mail_content from " . C ( "DB_PREFIX" ) . "mail_template where name ='delivery_mail'" );
		$payment_tmpl = $payment_tmpl [0];
		$tpl = Think::instance ( 'ThinkTemplate' );
		ob_start ();
		eval ( '?' . '>' . $tpl->parse ( $payment_tmpl ['mail_content'] ) );
		$content = ob_get_clean ();
		if ($send)
		{
			$mail = new Mail ();
			$mail->AddAddress ( $user ['email'], $user ['user_name'] );
			$mail->IsHTML ( 0 );
			$mail->Subject = $payment_tmpl ['mail_title']; // 标题
			$mail->Body = $content; // 内容
			$mail->Send ();
		} else
		{
			$sendData ['dest'] = $user ['email'];
			$sendData ['title'] = $payment_tmpl ['mail_title'];
			$sendData ['content'] = $content;
			$sendData ['create_time'] = gmtTime ();
			$sendData ['send_type'] = 0;
			M ( "SendList" )->add ( $sendData );
		}
	}
}
//处理成功返回true，处理失败返回，错误消息
function order_paid_in($payment_log_id, $money, $payment_id, $currency_id)
{
	$PaymentLog = M ( "PaymentLog" );
	//$payment_log_vo = $PaymentLog->getById ($payment_log_id);==SQL优化==
	$payment_log_vo = M ()->query ( "select id,is_paid,rec_id,payment_id,create_time from " . C ( "DB_PREFIX" ) . "payment_log where id = " . $payment_log_id );
	$payment_log_vo = $payment_log_vo [0];
	if ($payment_log_vo == false)
	{
		return L ( 'INVALID_PAY_LOG_ID' ) . $payment_log_id;
	}
	if ($payment_log_vo ['is_paid'] == 1)
	{
		return L ( 'PAY_LOG_ID' ) . $payment_log_id . L ( 'PAY_LOG_ID_INVALID' );
	}
	/* 修改此次支付操作的状态为已付款 */
	//$payment_log_vo['is_paid'] = 1;==SQL优化==
	//$PaymentLog->save($payment_log_vo);==SQL优化==
	M ()->execute ( "update " . C ( "DB_PREFIX" ) . "payment_log set is_paid = 1 where id=" . $payment_log_id );
	$payment = S ( "CACHE_PAYMENT_" . intval ( $payment_id ) );
	if ($payment === false)
	{
		$payment = M ( "Payment" )->getById ( intval ( $payment_id ) );
		S ( "CACHE_PAYMENT_" . intval ( $payment_id ), $payment );
	}
	if ($payment == false)
	{
		return L ( 'INVALID_PAYMENT_ID' );
	}
	$model = M ( "Order" );
	//$order_vo = $model->getById($payment_log_vo['rec_id']);==SQL优化==
	$order_vo = M ()->query ( "select id,user_id,sn,currency_id,money_status from " . C ( "DB_PREFIX" ) . "order where id = " . $payment_log_vo ['rec_id'] );
	$order_vo = $order_vo [0];
	if ($order_vo == false)
	{
		return L ( 'INVALID_ORDER_ID' );
	} else
	{
		if (intval ( $currency_id ) == 0)
		{
			$currency_id = $order_vo ['currency_id'];
		}
		//			$currency = M( "Currency" )->getById(intval($currency_id));
		//		    if ($currency['radio'] == 0){
		$currency = array ();
		$currency ['unit'] = eyooC ( "BASE_CURRENCY_UNIT" );
		$currency ['radio'] = 1;
		//}
		//add by chenfq 添加判断团购是否已经结束？商品是否已经销售完 2010-06-28
		//预存款支付 在cart-->done时，会调用本函数
		$orderGoods = S ( "CACHE_ORDER_GOODS_" . $payment_log_vo ['rec_id'] );
		if ($orderGoods === false)
		{
			$orderGoods = D ( "OrderGoods" )->where ( 'order_id = ' . $payment_log_vo ['rec_id'] )->find ();
			S ( "CACHE_ORDER_GOODS_" . $payment_log_vo ['rec_id'], $orderGoods );
		}
		//$goods = M("Goods")->getById($orderGoods['rec_id']);==SQL优化==
		$goods = M ()->query ( "select promote_end_time,is_group_fail,stock,buy_count,stock from " . C ( "DB_PREFIX" ) . "goods where id = " . $orderGoods ['rec_id'] );
		$goods = $goods [0];
		if (($goods ['stock'] > 0 && $goods ['buy_count'] + $orderGoods ['number'] > $goods ['stock']) || $order_vo ['money_status'] == 2)
		{ //add by chenfq 2010-05-30 判断时间是否结束
			if ($payment ['class_name'] == 'Accountpay')
			{ //会员使用预存款支付
				return '购物结束，终止支付';
			} else
			{
				user_money_log ( $order_vo ['user_id'], $order_vo ['id'], 'Order', $money * $currency ['radio'], $order_vo ['sn'] . '订单支付金额转会员存款' );
				//add by chenfq 2010-06-30  记录帐户资金变化明细 begin
				payment_money_log ( $payment_log_vo ['payment_id'], $order_vo ['user_id'], $order_vo ['id'], 'Order', $money * $currency ['radio'], $order_vo ['sn'] . '订单支付金额转会员存款:' . $money * $currency ['radio'], false, 'User', '', '' );
				if ($order_vo ['money_status'] == 2)
				{
					M ( "Order" )->where ( "id=" . $order_vo ['id'] )->setField ( "repay_status", 1 );
					//添加一收款单
					$vo = M ( "OrderIncharge" )->create ();
					$vo ['id'] = null;
					$vo ['order_id'] = $order_vo ['id'];
					$vo ['cost_payment_fee'] = 0;
					$vo ['currency_id'] = $currency_id;
					$vo ['currency_radio'] = $currency ['radio'];
					$vo ['money'] = $money * $currency ['radio'];
					$vo ['create_time'] = gmtTime ();
					$vo ['memo'] = '重复支付的收款充值到会员中心:' . $money;
					$vo ['payment_id'] = $payment_id;
					//修改 by hc 增加收款单时，存入支付单号
					$payment ['config'] = unserialize ( $payment ['config'] );
					if ($payment ['class_name'] == 'TenpayBank' || $payment ['class_name'] == 'Tencentpay')
					{
						$today = toDate ( $payment_log_vo ['create_time'], 'Ymd' );
						/* 将商户号+年月日+流水号 */
						$bill_no = str_pad ( $payment_log_vo ['id'], 10, 0, STR_PAD_LEFT );
						$vo ['payment_log_sn'] = $payment ['config'] ['tencentpay_id'] . $today . $bill_no;
					} elseif ($payment ['class_name'] == 'Alipay')
					{
						$vo ['payment_log_sn'] = 'fw123456' . $payment_log_vo ['id'];
					} else
						$vo ['payment_log_sn'] = $payment_log_vo ['id'];
					M ( "OrderIncharge" )->add ( $vo );
					return '订单【' . $order_vo ['sn'] . '】已经全款支付，金额自动转存会员中心';
				} else
				{
					//add by chenfq 2010-06-30 记录帐户资金变化明细 end
					M ( "Order" )->where ( "id=" . $order_vo ['id'] )->setField ( "repay_status", 2 );
					//添加一收款单
					//$vo = M("OrderIncharge")->create();==SQL优化==
					//$vo['id'] = null;==SQL优化==
					$vo ['order_id'] = $order_vo ['id'];
					$vo ['cost_payment_fee'] = 0;
					$vo ['currency_id'] = $currency_id;
					$vo ['currency_radio'] = $currency ['radio'];
					$vo ['money'] = $money * $currency ['radio'];
					$vo ['create_time'] = gmtTime ();
					$vo ['memo'] = '过期支付的收款充值到会员中心:' . $money;
					$vo ['payment_id'] = $payment_id;
					//修改 by hc 增加收款单时，存入支付单号
					$payment ['config'] = unserialize ( $payment ['config'] );
					if ($payment ['class_name'] == 'TenpayBank' || $payment ['class_name'] == 'Tencentpay')
					{
						$today = toDate ( $payment_log_vo ['create_time'], 'Ymd' );
						/* 将商户号+年月日+流水号 */
						$bill_no = str_pad ( $payment_log_vo ['id'], 10, 0, STR_PAD_LEFT );
						$vo ['payment_log_sn'] = $payment ['config'] ['tencentpay_id'] . $today . $bill_no;
					} elseif ($payment ['class_name'] == 'Alipay')
					{
						$vo ['payment_log_sn'] = 'fw123456' . $payment_log_vo ['id'];
					} else
						$vo ['payment_log_sn'] = $payment_log_vo ['id'];
					M ( "OrderIncharge" )->add ( $vo );
					return '购物结束，终止对该订单支付。【' . $order_vo ['sn'] . '】支付订单金额自动转存会员中心';
				}
			}
		}
	}
	$user_id = intval ( $order_vo ['user_id'] );
	if ($payment ['class_name'] == 'Accountpay')
	{ //会员使用预存款支付
		if ($user_id > 0)
		{
			$user = M ( 'User' )->getById ( $user_id );
			if (($user ['money'] < 0) || ($money - $user ['money'] > 0.01))
			{
				return L ( 'USER_MONEY_DEFICIT' );
			}
		} else
		{
			return L ( 'INVALID_USER_ID' );
		}
	}
	$cost_payment_fee = 0;
	if ($payment ['cost_fee_type'] == 1)
	{
		$cost_payment_fee = $payment ['cost_fee'] * $currency ['radio'];
	} else
	{
		$cost_payment_fee = $money * $payment ['cost_fee'] / 100 / $currency ['radio'];
	}
	//添加一收款单
	$OrderIncharge = M ( "OrderIncharge" );
	//$vo = $OrderIncharge->create();==SQL优化==
	//$vo['id'] = null;==SQL优化==
	$vo ['order_id'] = $order_vo ['id'];
	$vo ['cost_payment_fee'] = $cost_payment_fee;
	$vo ['currency_id'] = $currency_id;
	$vo ['currency_radio'] = $currency ['radio'];
	$vo ['money'] = $money * $currency ['radio'];
	$vo ['create_time'] = gmtTime ();
	$vo ['memo'] = L ( 'ORDER_ONLINE_PAY' ) . ':' . $money;
	$vo ['payment_id'] = $payment_id;
	//修改 by hc 增加收款单时，存入支付单号
	$payment ['config'] = unserialize ( $payment ['config'] );
	if ($payment ['class_name'] == 'TenpayBank' || $payment ['class_name'] == 'Tencentpay')
	{
		$today = toDate ( $payment_log_vo ['create_time'], 'Ymd' );
		/* 将商户号+年月日+流水号 */
		$bill_no = str_pad ( $payment_log_vo ['id'], 10, 0, STR_PAD_LEFT );
		$vo ['payment_log_sn'] = $payment ['config'] ['tencentpay_id'] . $today . $bill_no;
	} elseif ($payment ['class_name'] == 'Alipay')
	{
		$vo ['payment_log_sn'] = 'fw123456' . $payment_log_vo ['id'];
	} else
		$vo ['payment_log_sn'] = $payment_log_vo ['id'];
	$id = $OrderIncharge->add ( $vo );
	//modfiy by chenfq 2010-06-5 添加 $onlinepay=true 参数
	inc_order_incharge ( $id, true );
	return true;
}
//增加已收金额 modfiy by chenfq 2010-06-5 添加 $onlinepay 在线支付参数
function inc_order_incharge($order_incharge_id, $onlinepay = false)
{
	//发送短信
	send_orderpaid_sms ( $order_incharge_id );
	//$incharge_vo = M("OrderIncharge")->getById ( $order_incharge_id );==SQL优化==
	$incharge_vo = M ()->query ( "select order_id,money,cost_payment_fee,payment_id from " . C ( "DB_PREFIX" ) . "order_incharge where id = " . $order_incharge_id );
	$incharge_vo = $incharge_vo [0];
	$model = M ( "Order" );
	$order_vo = $model->getById ( $incharge_vo ['order_id'] );
	$order_vo ['order_incharge'] = $order_vo ['order_incharge'] + $incharge_vo ['money'];
	$order_vo ["cost_payment_fee"] = floatval ( $order_vo ["cost_payment_fee"] ) + $incharge_vo ['cost_payment_fee'];
	$payment = S ( "CACHE_PAYMENT_" . intval ( $incharge_vo ['payment_id'] ) );
	if ($payment === false)
	{
		$payment = M ( "Payment" )->getById ( intval ( $incharge_vo ['payment_id'] ) );
		S ( "CACHE_PAYMENT_" . intval ( $incharge_vo ['payment_id'] ), $payment );
	}
	if ($payment ['class_name'] == 'Accountpay' && $order_vo ['user_id'] > 0)
	{ //会员使用预存款支付，减少预存款
		//记录会员预存款变化明细
		user_money_log ( $order_vo ['user_id'], $order_incharge_id, 'OrderIncharge', $incharge_vo ['money'] * - 1, L ( "ORDER_CHARGE_MEMO_1" ) );
	}
	//add by chenfq 2010-06-5  记录帐户资金变化明细 begin
	if ($payment)
	{
		if ($onlinepay)
		{
			payment_money_log ( $payment ['id'], $order_vo ['user_id'], $order_incharge_id, 'OrderIncharge', $incharge_vo ['money'], '会员在线支付订单金额：' . $incharge_vo ['money'], false, 'User', '', '' );
		} else
		{
			payment_money_log ( $payment ['id'], $_SESSION [C ( 'USER_AUTH_KEY' )], $order_incharge_id, 'OrderIncharge', $incharge_vo ['money'], $_SESSION ['adm_name'] . '管理员后台收订单金额：' . $incharge_vo ['money'], false, 'Admin', $payment ['name_1'], $_SESSION ['adm_name'] );
		}
	}
	//add by chenfq 2010-06-5  记录帐户资金变化明细 end
	/*del by chenfq 2010-04-07 全额支付时，再计算会员积分
		//会员积明细, 第一次收款时，计算积分 注：收款以后 $order_vo["money_status"] 的值都会大小0
		if ($order_vo['user_id'] > 0 && $order_vo['order_score'] > 0 && $order_vo["money_status"] == 0){
				if ($order_vo['order_score'] > 0){
					$Remark = L("ORDER_SCORE_MEMO_1").'('.$order_vo['sn'].')';//订单获得积分
				}else if($order_vo['order_score'] < 0){
					$Remark = L("ORDER_SCORE_MEMO_2").'('.$order_vo['sn'].')';//订单消费积分
				}

				$sql_str = 'insert into '.C("DB_PREFIX").'user_score_log(user_id, create_time, score, memo_1) values('.$order_vo['user_id'].','.gmtTime().','.$order_vo['order_score'].','.'\''.$Remark.'\')';
				$model->execute($sql_str);

			//增加会员积分
			$sql_str = 'update '.C("DB_PREFIX").'user set score = score + '.$order_vo['order_score'].' where id = '.$order_vo['user_id'];
			$model->execute($sql_str);
		}
		*/
	order_incharge_handle ( $order_vo );
}
function ecv_order_incharge($order_id)
{
	$model = M ( "Order" );
	$order_vo = $model->getById ( $order_id );
	$order_vo ['order_incharge'] = $order_vo ['order_incharge'] + $order_vo ['ecv_money'];
	order_incharge_handle ( $order_vo );
}
function order_incharge_handle($order_vo)
{
	$model = M ( "Order" );
	//已收金额 > 订单总金额
	//收款状态：0:未收款; 1:部分收款; 2:全部收款; 3:部分退款; 4:全部退款
	/*if ($order_vo["order_incharge"] <= 0)
		{
			$order_vo["money_status"] = 0;
		}
		else*/
	if (abs ( $order_vo ["order_incharge"] - $order_vo ['order_total_price'] ) < 0.001)
	{
		$order_vo ["money_status"] = 2;
	} else if ($order_vo ["order_incharge"] < $order_vo ['order_total_price'])
	{
		if ($order_vo ["order_incharge"] == 0)
			$order_vo ["money_status"] = 0;
		else
			$order_vo ["money_status"] = 1;
	} else if ($order_vo ["order_incharge"] >= $order_vo ['order_total_price'])
	{
		$order_vo ["money_status"] = 2;
	}
	/*del by chenfq 2010-04-08
		//取款手续费
		$order_vo["cost_payment_fee"] = floatval($order_vo["cost_payment_fee"]) + $incharge_vo['cost_payment_fee'];
		*/
	$re = $model->save ( $order_vo );
	if ($re)
	{
		if ($order_vo ["money_status"] == 2)
		{
			$userid = intval ( $order_vo ['user_id'] );
			D ( "User" )->setInc ( 'buy_count', "id= $userid" ); // 用户购买次数加1
			$user = S ( "CACHE_USER_INFO_" . $userid );
			if ($user === false)
			{
				$user = D ( "User" )->where ( 'id = ' . $userid )->find ();
				S ( "CACHE_USER_INFO_" . $userid, $user );
			} else
			{
				$user ['buy_count'] = $user ['buy_count'] + 1;
				S ( "CACHE_USER_INFO_" . $userid, $user );
			}
			$parentID = intval ( $order_vo ['parent_id'] );
			$orderGoods = S ( "CACHE_ORDER_GOODS_" . $order_vo ['id'] );
			if ($orderGoods === false)
			{
				$sql = "select * from " . C ( "DB_PREFIX" ) . "order_goods where order_id = " . $order_vo ['id'] . " limit 1";
				$orderGoods = M ()->query ( $sql );
				$orderGoods = $orderGoods [0];
				S ( "CACHE_ORDER_GOODS_" . $order_vo ['id'], $orderGoods );
			}
			//$orderGoods = D("OrderGoods")->where('order_id = '.$order_vo['id'])->find();
			//$orderGoodsCount = intval($orderGoods['number']);
			//$goods['buy_count'] = intval($goods['buy_count']) + $orderGoodsCount;
			$goods = M ( "Goods" )->getById ( $orderGoods ['rec_id'] );
			if ($goods)
			{
				//计算已经购买了几个商品
				$sql = "select sum(og.number) as number from " . C ( "DB_PREFIX" ) . "order as o left join " . C ( "DB_PREFIX" ) . "order_goods  as og on og.order_id = o.id where og.rec_id = $goods[id] and o.money_status = 2";
				$number = M ()->query ( $sql );
				$buy_count = intval ( $number [0] ['number'] );
				$goods ['buy_count'] = $buy_count;
			}
			//$userBuyOrderGoods = D("OrderGoods")->where("user_id = '$userid' and rec_id = '$orderGoods[rec_id]'")->count();
			//$userIsBuy  = count($userBuyOrderGoods);
			$sql = "select count(*) as number from " . C ( "DB_PREFIX" ) . "order_goods where user_id = '$userid' and rec_id = '$orderGoods[rec_id]'";
			$userBuyOrderGoods = M ()->query ( $sql );
			$userIsBuy = intval ( $userBuyOrderGoods [0] ['number'] );
			if ($userIsBuy > 0)
			{
				$sql = "select count(o.id) as number from " . C ( "DB_PREFIX" ) . "order as o left join " . C ( "DB_PREFIX" ) . "order_goods  as og on og.order_id = o.id where og.rec_id = $orderGoods[rec_id] and o.money_status = 2";
				$number = M ()->query ( $sql );
				$user_count = intval ( $number [0] ['number'] );
				$goods ['user_count'] = $user_count;

		//$goods['user_count'] = intval($goods['user_count']) + 1;
			}
			if ($goods ['user_count'] >= $goods ['group_user'] && $goods ['complete_time'] == 0)
			{
				//$goods['complete_time'] = gmtTime();
			}
			D ( "Goods" )->save ( $goods );
			//修改 by hc, 每下一单付款后即生成一张团购券，去除原is_group_fail == 2的判断，在团购成功时仅作短信和邮件通知队列生成
			// if(($goods['type_id'] == 0 || $goods['type_id'] == 2) && $goods['is_group_fail'] == 2)
			if (($goods ['type_id'] == 0 || $goods ['type_id'] == 2))
				sendOrderGroupBonds ( $order_vo ['id'] );

		//return true;
			if ($parentID > 0 && $order_vo ["offline"] == 0 && $goods ['close_referrals'] == 0 && gmtTime () - $user ['create_time'] < (eyooC ( "REFERRAL_TIME" ) * 3600) && M ( "Referrals" )->where ( "user_id=" . $userid . " and parent_id=" . $parentID )->count () == 0)
			{ // add by chenfq 2010-04-07 offline 0：非线下订单；1：线下订单
				Log::record ( $parentID );
				Log::save ();
				if ($parentID > 0 && $parentID != $userid)
				{
					$referrals ['user_id'] = $userid;
					$referrals ['parent_id'] = $parentID;
					$referrals ['order_id'] = $order_vo ['id'];
					$referrals ['goods_id'] = $goods ['id'];
					$referrals_amount = $goods ['referrals'] == 0 ? intval ( eyooC ( "REFERRALS_MONEY" ) ) : $goods ['referrals'];
					if (eyooC ( "REFERRAL_TYPE" ) == 0)
						$referrals ['money'] = $referrals_amount;
					else
						$referrals ['score'] = $referrals_amount;
					$referrals ['is_pay'] = 0;
					$referrals ['create_time'] = gmtTime ();
					$referrals ['city_id'] = intval ( $goods ['city_id'] );
					$re_id = D ( "Referrals" )->add ( $referrals );
				}
			}
			//add by chenfq 2010-04-07  会员积明细, 全额支付时，计算积分 注：收款以后 $order_vo["money_status"] 的值都会大小0
			if ($order_vo ['user_id'] > 0 && $order_vo ['order_score'] > 0 && $order_vo ["offline"] == 0)
			{
				if ($order_vo ['order_score'] > 0)
				{
					$Remark = L ( "ORDER_SCORE_MEMO_1" ) . '(' . $order_vo ['sn'] . ')'; //订单获得积分
				} else if ($order_vo ['order_score'] < 0)
				{
					$Remark = L ( "ORDER_SCORE_MEMO_2" ) . '(' . $order_vo ['sn'] . ')'; //订单消费积分
				}
				$sql_str = 'insert into ' . C ( "DB_PREFIX" ) . 'user_score_log(user_id, create_time, score, memo_1) values(' . $order_vo ['user_id'] . ',' . gmtTime () . ',' . $order_vo ['order_score'] . ',' . '\'' . $Remark . '\')';
				$model->execute ( $sql_str );
				//增加会员积分
				$sql_str = 'update ' . C ( "DB_PREFIX" ) . 'user set score = score + ' . $order_vo ['order_score'] . ' where id = ' . $order_vo ['user_id'];
				$model->execute ( $sql_str );
			}
		}
	}
	clear_user_order_cache ( $order_vo ['id'] );
	S ( "CACHE_USER_INFO_" . $order_vo ['user_id'], NULL );
}
//支付返利
function payReferrals($id)
{
	$referrals = M ( "Referrals" )->getById ( $id );
	if ($referrals)
	{
		//现金返利
		$user = D ( "User" )->getById ( $referrals ['user_id'] );
		if ($referrals ['money'] > 0)
		{
			$msg = sprintf ( L ( "PAY_REFERRALS_MONEY_INFO" ), $user ['user_name'] );
			$sql_str = 'insert into ' . C ( "DB_PREFIX" ) . "user_money_log(user_id, rec_id,money,create_time,rec_module,memo_1) values($referrals[parent_id],$id,$referrals[money]," . gmtTime () . ",'Referrals','$msg')";
			M ()->execute ( $sql_str );
			$sql_str = 'update ' . C ( "DB_PREFIX" ) . 'user set money = money + ' . $referrals ['money'] . ' where id = ' . $referrals ['parent_id'];
			M ()->execute ( $sql_str );
		}
		if ($referrals ['score'] > 0)
		{
			$msg = sprintf ( L ( "PAY_REFERRALS_SCORE_INFO" ), $user ['user_name'] );
			$sql_str = 'insert into ' . C ( "DB_PREFIX" ) . "user_score_log(user_id, rec_id,score,create_time,rec_module,memo_1) values($referrals[parent_id],$id,$referrals[score]," . gmtTime () . ",'Referrals','$msg')";
			M ()->execute ( $sql_str );
			$sql_str = 'update ' . C ( "DB_PREFIX" ) . 'user set score = score + ' . $referrals ['score'] . ' where id = ' . $referrals ['parent_id'];
			M ()->execute ( $sql_str );
		}
		$referrals ['is_pay'] = 1;
		$referrals ['pay_time'] = gmtTime ();
		M ( "Referrals" )->save ( $referrals );
		clear_user_order_cache ( 0 );
	}
}
//退还返利
function unPayReferrals($id)
{
	$referrals = D ( "Referrals" )->getById ( $id );
	if ($referrals)
	{
		//现金返利
		$user = D ( "User" )->getById ( $referrals ['user_id'] );
		if ($referrals ['money'] > 0)
		{
			$msg = sprintf ( L ( "UNPAY_REFERRALS_MONEY_INFO" ), $user ['user_name'] );
			$sql_str = 'insert into ' . C ( "DB_PREFIX" ) . "user_money_log(user_id, rec_id,money,create_time,rec_module,memo_1) values($referrals[parent_id],$id,-$referrals[money]," . gmtTime () . ",'Referrals','$msg')";
			M ()->execute ( $sql_str );
			$sql_str = 'update ' . C ( "DB_PREFIX" ) . 'user set money = money - ' . $referrals ['money'] . ' where id = ' . $referrals ['parent_id'];
			M ()->execute ( $sql_str );
		}
		if ($referrals ['score'] > 0)
		{
			$msg = sprintf ( L ( "UNPAY_REFERRALS_SCORE_INFO" ), $user ['user_name'] );
			$sql_str = 'insert into ' . C ( "DB_PREFIX" ) . "user_score_log(user_id, rec_id,money,create_time,rec_module,memo_1) values($referrals[parent_id],$id,-$referrals[score]," . gmtTime () . ",'Referrals','$msg')";
			M ()->execute ( $sql_str );
			$sql_str = 'update ' . C ( "DB_PREFIX" ) . 'user set score = score - ' . $referrals ['score'] . ' where id = ' . $referrals ['parent_id'];
			M ()->execute ( $sql_str );
		}
		$referrals ['create_time'] = 0;
		$referrals ['is_pay'] = 0;
		$referrals ['pay_time'] = 0;
		D ( "Referrals" )->save ( $referrals );
	}
}
//由数据库取出系统的配置
function eyooC($name)
{
	if (! file_exists ( getcwd () . "/config/sys_config.php" ))
	{
		//开始写入配置文件
		$sys_configs = M ()->query ( "select name,val from " . C ( "DB_PREFIX" ) . "sys_conf" );
		$config_str = "<?php\n";
		$config_str .= "return array(\n";
		foreach ( $sys_configs as $k => $v )
		{
			$config_str .= "'" . $v ['name'] . "'=>'" . addslashes ( $v ['val'] ) . "',\n";
		}
		$config_str .= ");\n ?>";
		@file_put_contents ( getcwd () . "/config/sys_config.php", $config_str );
	}
	static $config = array ();
	$config = require './config/sys_config.php';
	if ($name != 'SHOP_URL')
	{
		$val = S ( "SYS_CONF_" . $name );
		if ($val === false)
		{
			if ($name == 'INTEGRATE_CODE')
			{
				//$val = M("SysConf")->where("name='".$name."'")->getField("val");
				$val = stripslashes ( $config [$name] );
				if (! $val)
					$val = 'fanwe';
			} else
			{
				$val = stripslashes ( $config [$name] );
			}
			S ( "SYS_CONF_" . $name, $val );
		}
	}
	//$val = M("SysConf")->where("name='".$name."'")->getField("val");
	if ($name == 'SHOP_URL')
		return "http://" . $_SERVER ['HTTP_HOST'] . __ROOT__;
	elseif ($val != '')
	{
		return $val;
	} else
	{
		return C ( $name );
	}
}
//已发货数量 统计
function order_send_num($order_id)
{
	$sql_str = 'UPDATE  ' . C ( "DB_PREFIX" ) . 'ORDER_GOODS AS A' . '   SET A.SEND_NUMBER = IFNULL((SELECT SUM(B.NUMBER)' . '                       FROM  ' . C ( "DB_PREFIX" ) . 'ORDER_CONSIGNMENT_GOODS AS B' . '                      WHERE B.ORDER_GOODS_ID = A.ID),0) -' . '                    IFNULL((SELECT SUM(B.NUMBER)' . '                       FROM  ' . C ( "DB_PREFIX" ) . 'ORDER_RE_CONSIGNMENT_GOODS AS B' . '                      WHERE B.ORDER_GOODS_ID = A.ID),0)' . ' WHERE A.ORDER_ID = ' . $order_id;
	$sql_str = strtolower ( $sql_str );
	$Model = new Model ();
	$Model->execute ( $sql_str );
}
//减库存
function order_dec_stock($order_consignment_id)
{
	//		$sql_str =	'UPDATE '.C("DB_PREFIX").'GOODS G'.
//					'   SET G.STOCK = G.STOCK - IFNULL('.
//					'                        (SELECT SUM(A.NUMBER)'.
//					'                           FROM '.C("DB_PREFIX").'ORDER_CONSIGNMENT_GOODS A'.
//					'                           LEFT OUTER JOIN '.C("DB_PREFIX").'ORDER_GOODS B ON B.ID = A.ORDER_GOODS_ID'.
//					'                          WHERE G.ID = B.rec_id'.
//					'                            AND A.ORDER_CONSIGNMENT_ID = '.$order_consignment_id.'), 0)'.
//					' WHERE G.ID IN'.
//					'       (SELECT B.rec_id'.
//					'          FROM '.C("DB_PREFIX").'ORDER_CONSIGNMENT_GOODS A'.
//					'          LEFT OUTER JOIN '.C("DB_PREFIX").'ORDER_GOODS B ON B.ID = A.ORDER_GOODS_ID'.
//					'         WHERE A.ORDER_CONSIGNMENT_ID = '.$order_consignment_id.')';
//		$sql_str = strtolower($sql_str);
//		$Model = new Model();
//		$Model->execute($sql_str);
}
//增加库存
function order_inc_stock($order_re_consignment_id)
{
	//		$sql_str =	'UPDATE '.C("DB_PREFIX").'GOODS G'.
//					'   SET G.STOCK = G.STOCK + IFNULL('.
//					'                        (SELECT SUM(A.NUMBER)'.
//					'                           FROM '.C("DB_PREFIX").'ORDER_RE_CONSIGNMENT_GOODS A'.
//					'                           LEFT OUTER JOIN '.C("DB_PREFIX").'ORDER_GOODS B ON B.ID = A.ORDER_GOODS_ID'.
//					'                          WHERE G.ID = B.rec_id'.
//					'                            AND A.ORDER_RE_CONSIGNMENT_ID = '.$order_re_consignment_id.'), 0)'.
//					' WHERE G.ID IN'.
//					'       (SELECT B.rec_id'.
//					'          FROM '.C("DB_PREFIX").'ORDER_RE_CONSIGNMENT_GOODS A'.
//					'          LEFT OUTER JOIN '.C("DB_PREFIX").'ORDER_GOODS B ON B.ID = A.ORDER_GOODS_ID'.
//					'         WHERE A.ORDER_RE_CONSIGNMENT_ID = '.$order_re_consignment_id.')';
//		//dump($sql_str);
//		$sql_str = strtolower($sql_str);
//		$Model = new Model();
//		$Model->execute($sql_str);
}
/**
 * 生成优惠卡号
 *
 * @param integer $id fanwe_promote_card.id
 * @return string
 */
function buildCard($id)
{
	$tmp = String::keyGen ();
	$tmp = substr ( $tmp, 0, 16 - strlen ( $id ) ) . $id;
	return $tmp;
}
function formatMoney($money, $currency_id)
{
	//		$currency = D("Currency")->where("id=".$currency_id)->find();
	//		if(!$currency)
	//			$currency_radio = 1;
	//		else
	//			$currency_radio = $currency['radio'];
	//		$money = number_format($money * $currency_radio,2);
	//		return $currency['unit']." ".$money;
	return formatPrice ( $money );
}
function get_all_files($path)
{
	$list = array ();
	foreach ( glob ( $path . '/*' ) as $item )
	{
		if (is_dir ( $item ))
		{
			$list = array_merge ( $list, get_all_files ( $item ) );
		} else
		{
			//if(eregi(".php",$item)){}//这里可以增加判断文件名或其他。changed by:edlongren
			$list [] = $item;
		}
	}
	return $list;
}
// 定义重置队列群发
function reset_auto_runing()
{
	//$lock_file = getcwd()."/Public/autorun.lock";
	//@unlink($lock_file);
	S ( "CACHE_LOCK_AUTO_RUN", NULL );
}
//修正by hc 20100804, 将autoRun的文件锁模式改为缓存模式
function autoRun()
{
	$auto_begin_time = S ( "CACHE_LOCK_AUTO_RUN" );
	// echo gmtTime()-intval($auto_begin_time);
	// 服务端的全量变量
	if (intval ( $auto_begin_time ) == 0)
	{
		//echo 'a';
		//			D()->query("update ".C("DB_PREFIX")."sys_conf set val ='1' where status = 1 and name = 'AUTO_RUN_ING'");
		//			D()->query("update ".C("DB_PREFIX")."sys_conf set val ='".gmtTime()."' where status = 1 and name = 'AUTO_RUN_BEGIN_TIME'");
		//@file_put_contents($lock_file,gmtTime());
		S ( "CACHE_LOCK_AUTO_RUN", gmtTime () );
		//自动发放团购卷
		autoSendGroupBond ();
		//开始自动发放返利
		if (eyooC ( "AUTO_REFERRAL" ) == 1)
		{
			if (gmtTime () - intval ( S ( "CHECK_REFERRALS_TIME" ) ) > 300)
			{
				$referrals = M ( "Referrals" )->where ( "is_pay=0 and create_time<>0" )->findAll ();
				foreach ( $referrals as $k => $v )
				{
					if (gmtTime () - $v ['create_time'] >= eyooC ( "REFERRALS_LIMIT_TIME" ) * 3600)
					{
						payReferrals ( $v ['id'] );
					}
				}
				S ( "CHECK_REFERRALS_TIME", gmtTime () );
			}
		}
		// 5分钟修正一次
		if (gmtTime () - intval ( S ( "CHECK_FIX_STATUS_TIME" ) ) > 300)
		{
			//自动修正团购状态
			// 1、将团购未结束的 且 标识团购失败的，自动更新为：团购进行中
			$sql = "update " . C ( "DB_PREFIX" ) . "goods set is_group_fail = 0, complete_time = 0 where is_group_fail = 1 and promote_end_time > " . gmtTime ();
			M ()->query ( $sql );
			// 2、 将  标识团购成功的 and 当前购买人数等于0 and group_user > 0 and  团购未结束的  ; 自动更新为：团购进行中
			$sql = "update " . C ( "DB_PREFIX" ) . "goods set is_group_fail = 0, complete_time = 0 where is_group_fail = 2 and buy_count = 0 and group_user > 0 and promote_end_time > " . gmtTime ();
			M ()->query ( $sql );
			//注：团购成功后，当前购买人数大于0时，不能再自动改为团购进行中了，因为团购成功后，会自动发放团购卷
			//D()->query("update ".C("DB_PREFIX")."sys_conf set val ='0' where status = 1 and name = 'AUTO_RUN_ING'");
			S ( "CHECK_FIX_STATUS_TIME", gmtTime () );
		}
		reset_auto_runing ();
	} else
	{
		//在自动执行中....
		//$auto_begin_time = intval(M("SysConf")->where("name='AUTO_RUN_BEGIN_TIME'")->getField('val'));
		if (gmtTime () - $auto_begin_time > 300)
		{ //(5分钟)超时后，自动把状态改为：false
			reset_auto_runing ();
		}
	}
}
function autoSend()
{
	//清空10小时前的发送队列
	M ( "SendList" )->where ( "status=1 and " . gmtTime () . "-send_time>36000" )->delete ();
	set_time_limit ( 0 );
	ignore_user_abort ( true );
	//服务端的全量变量
	if (M ( "SysConf" )->where ( "name='AUTO_SEND_ING'" )->getField ( 'val' ) == 0)
	{
		D ()->query ( "update " . C ( "DB_PREFIX" ) . "sys_conf set val ='1' where status = 1 and name = 'AUTO_SEND_ING'" );
		D ()->query ( "update " . C ( "DB_PREFIX" ) . "sys_conf set val ='" . gmtTime () . "' where status = 1 and name = 'AUTO_SEND_BEGIN_TIME'" );
		send_msg_list ();
		D ()->query ( "update " . C ( "DB_PREFIX" ) . "sys_conf set val ='0' where status = 1 and name = 'AUTO_SEND_ING'" );
	} else
	{
		//在自动执行中....
		$auto_begin_time = intval ( M ( "SysConf" )->where ( "name='AUTO_SEND_BEGIN_TIME'" )->getField ( 'val' ) );
		if (gmtTime () - $auto_begin_time > 600)
		{ //(10分钟)超时后，自动把状态改为：false
			//				D("SysConf")->where("status=1 and name='AUTO_RUN_ING'")->setField("val",0);
			D ()->query ( "update " . C ( "DB_PREFIX" ) . "sys_conf set val ='0' where status = 1 and name = 'AUTO_SEND_ING'" );
		}
	}
}
function autoSendMail()
{
	//清空10小时前的发送队列
	M ( "MailSendList" )->where ( "status=1 and " . gmtTime () . "-send_time>36000" )->delete ();
	set_time_limit ( 0 );
	ignore_user_abort ( true );
	//服务端的全量变量
	if (M ( "SysConf" )->where ( "name='AUTO_SEND_MAIL_ING'" )->getField ( 'val' ) == 0)
	{
		D ()->query ( "update " . C ( "DB_PREFIX" ) . "sys_conf set val ='1' where status = 1 and name = 'AUTO_SEND_MAIL_ING'" );
		D ()->query ( "update " . C ( "DB_PREFIX" ) . "sys_conf set val ='" . gmtTime () . "' where status = 1 and name = 'AUTO_SEND_MAIL_BEGIN_TIME'" );
		send_mail_list ();
		D ()->query ( "update " . C ( "DB_PREFIX" ) . "sys_conf set val ='0' where status = 1 and name = 'AUTO_SEND_MAIL_ING'" );
	} else
	{
		//在自动执行中....
		$auto_begin_time = intval ( M ( "SysConf" )->where ( "name='AUTO_SEND_MAIL_BEGIN_TIME'" )->getField ( 'val' ) );
		if (gmtTime () - $auto_begin_time > 1800)
		{ //群发邮件半小时后超时，自动把状态改为：false
			//				D("SysConf")->where("status=1 and name='AUTO_RUN_ING'")->setField("val",0);
			D ()->query ( "update " . C ( "DB_PREFIX" ) . "sys_conf set val ='0' where status = 1 and name = 'AUTO_SEND_MAIL_ING'" );
		}
	}
}
function autoSendGroupBond()
{
	//is_group_fail:0、团购中....;1、表示团购失败;2、表示团购成功
	//group_user：最低团购人数,设为 0 则不限制团购人数; max_bought：用户最大购买数量,设为 0 则不限制用户最大购买数量; user_count:购买商品的人数
	//团购时间结束 或 购买人数 大于 最低团购人数 时，就自动放发方维卷
	//		$goods_list = S("CACHE_SUCCESS_GOODS_LIST");
	//		if($goods_list === false)
	//		{
	//			$goods_list = D("Goods")->where("is_group_fail = 0 and buy_count >= 0 and (promote_end_time <".gmtTime()." or buy_count >= group_user) ")->findAll();
	//			if(!$goods_list)
	//			{
	//				$goods_list = array();
	//			}
	//			S("CACHE_SUCCESS_GOODS_LIST",$goods_list);
	//		}
	$goods_list = D ( "Goods" )->where ( "is_group_fail = 0 and buy_count >= 0 and (promote_end_time <" . gmtTime () . " or buy_count >= group_user) " )->findAll ();
	if (! $goods_list)
	{
		$goods_list = array ();
	}
	if (count ( $goods_list ) > 0)
	{
		foreach ( $goods_list as $goods )
		{
			//group_user：最低团购人数,不为0时：购买人数小于最低限定人数；
			if (($goods ['group_user'] >= 0 && $goods ['group_user'] > $goods ['buy_count']))
			{
				$goods ['is_group_fail'] = 1;
				$goods ['complete_time'] = gmtTime ();
				$goods ['fail_buy_count'] = $goods ['buy_count'];
				D ( "Goods" )->save ( $goods );
			} else
			{
				//if ($goods['promote_end_time'] <gmtTime()){ //add by chenfq 2010-05-30 判断时间是否结束
				if ($goods ['type_id'] == 0 || $goods ['type_id'] == 2)
					sendGroupBond ( $goods ['id'] );
				$goods ['is_group_fail'] = 2;
				$goods ['complete_time'] = gmtTime ();
				D ( "Goods" )->save ( $goods );

		//}
			}
		}
		clear_cache ();
	}
}
//修改 by hc ， 去除原有补全功能， 在该函数执行时执行操作：1. 将要发下去的团购券的is_valid改为1, 2. 需要短信和邮件通知时通知下去
function sendGroupBond($goods_id)
{
	$goodsID = intval ( $goods_id );
	$goods = D ( "Goods" )->where ( "id = '$goodsID'" )->find ();
	$time = gmtTime ();
	$typeID = $goods ['type_id'];
	if ($goods ['is_group_fail'] == 1)
	{
	} elseif ((intval ( $goods ['promote_end_time'] ) < $time) || (($goods ['is_group_fail'] == 0) && ($goods ['buy_count'] >= $goods ['group_user'])))
	{
		if ($typeID == 0 || $typeID == 2)
		{
			$langItem = S ( "CACHE_LANG_ITEM" );
			if ($langItem === false)
			{
				$langItem = D ( "LangConf" )->where ( "lang_name='" . eyooC ( 'DEFAULT_LANG' ) . "'" )->find ();
				S ( "CACHE_LANG_ITEM", $langItem );
			}
			$default_lang_id = $langItem ['id']; //默认语言的ID
			//$select_dispname = "name_".$default_lang_id;
			$sql = "select o.*,og.number,og.attr from " . C ( "DB_PREFIX" ) . "order as o left join " . C ( "DB_PREFIX" ) . "order_goods  as og on og.order_id = o.id where og.rec_id = '$goodsID' and o.money_status = 2";
			$orderList = M ()->query ( $sql );
			$groupBond_m = D ( "GroupBond" );
			foreach ( $orderList as $order )
			{
				$sql_update = "update " . C ( "DB_PREFIX" ) . "group_bond set status = 1, buy_time =" . $order ['create_time'] . ",create_time =" . gmtTime () . ",is_valid=1  where goods_id=" . $goodsID . " and order_id='" . $order ['sn'] . "'";
				M ()->execute ( $sql_update );
				//修改 by hc 不再查询所有的团购券进行分发，以免错位，仅查询当前订单的团购券进行分发有效性
				$groupBonds = D ( "GroupBond" )->where ( "goods_id = '$goodsID' and order_id = '" . $order ['sn'] . "' and is_valid = 1" )->findAll ();
				foreach ( $groupBonds as $gbdata )
				{
					//发放团购卷时，自动短信通知
					if (eyooC ( 'AUTO_SEND_SMS' ) == 1 && M ( "Goods" )->where ( "id=" . $goodsID )->getField ( "allow_sms" ) == 1)
					{
						send_sms ( $order ['user_id'], $gbdata ['id'] );

		//dump('AUTO_SEND_SMS');
					}
					if (eyooC ( "MAIL_ON" ) == 1 && eyooC ( "SEND_GROUPBOND_MAIL" ) == 1)
					{
						send_grounp_bond_mail ( $order ['user_id'], $gbdata ['id'] );
					}
				}
				$order ['goods_status'] = 5; //不需配送的商品，直接设置成：无需配送  add by chenfq 2010-05-06
				$order ['status'] = 0;
				D ( "Order" )->save ( $order );
			}
		}
	}
}
//$send 为 true时默认为直接发送, 为false时为存储到数据库的发送队列  修改 by hc
function send_sms($user_id, $groupbond_id, $send = false)
{
	$is_valid = intval ( M ( "GroupBond" )->where ( "id=" . $groupbond_id )->getField ( "is_valid" ) ); //修改by hc 当无效时不发送
	if (eyooC ( "IS_SMS" ) != 1 || $is_valid == 0)
		return;
	$userid = intval ( $user_id );
	$id = intval ( $groupbond_id );
	$user = D ( "User" )->where ( "id =" . $userid . " and mobile_phone is not null and mobile_phone <> ''" )->find ();
	//开始判断是否发送给其他人
	if (eyooC ( "SMS_SEND_OTHER" ) == 1)
	{
		$order_sn = M ( "GroupBond" )->where ( "id=" . $id )->getField ( 'order_id' );
		$mobile_other = M ( "Order" )->where ( "sn='" . $order_sn . "'" )->getField ( "mobile_phone_sms" );
		if ($mobile_other && trim ( $mobile_other ) != '')
		{
			$user ['mobile_phone'] = $mobile_other;
		} else
		{
			$user ['mobile_phone'] = '';
		}
	}
	Log::record ( "send_sms_$user_id:" . $userid . ";groupbond_id:" . $groupbond_id );
	Log::save ();
	//dump($user);
	//return;
	if (! empty ( $user ['mobile_phone'] ))
	{
		$sms = D ( "SmsPlf" );
		$bond = D ( "GroupBond" )->where ( "id = $id" )->find ();
		$goods_short_name = M ( "Goods" )->where ( "id=" . $bond ['goods_id'] )->getField ( "goods_short_name" );
		$seller_info_id = M ( "Goods" )->where ( "id=" . $bond ['goods_id'] )->getField ( "suppliers_id" );
		$seller_info = M ( "SuppliersDepart" )->where ( "supplier_id=" . $seller_info_id . " and is_main=1" )->find ();
		$smsObjs = array ("user_name" => $user ['user_name'], "bond" => array ("goods_name" => $bond ['goods_name'], "goods_short_name" => $goods_short_name, "name" => eyooC ( 'GROUPBOTH' ), "sn" => $bond ['sn'], "password" => $bond ['password'], "order_sn" => $bond ['order_id'], "id" => $bond ['id'], "tel" => $seller_info ['tel'], "address" => $seller_info ['address'], "endtime" => toDate ( $bond ['end_time'], 'Y-m-d' ) ) );
		$mail_template = M ( "MailTemplate" )->where ( "name='group_bond_sms'" )->find ();
		if ($mail_template)
			$str = templateFetch ( $mail_template ['mail_content'], $smsObjs );

		//2010/6/7 awfigq 自动发送团购券成功后，标记团购券为已发送
		if ($send)
		{
			if ($sms->sendSMS ( $user ['mobile_phone'], $str ))
			{
				$bond = D ( "GroupBond" )->where ( "id = $id" )->setField ( "is_send_msg", 1 );
				M ( "GroupBond" )->setInc ( "send_count", "id = $id", 1 );
				Log::record ( "SendSMSStatus:" . $sms->message );
				Log::save ();
				return true;
			} else
				return false;
		} else
		{
			$sendData ['dest'] = $user ['mobile_phone'];
			$sendData ['title'] = '';
			$sendData ['content'] = $str;
			$sendData ['create_time'] = gmtTime ();
			$sendData ['send_type'] = 1; //短信
			$sendData ['bond_id'] = $groupbond_id;
			if (M ( "SendList" )->where ( "bond_id=" . $groupbond_id . " and dest='" . $user ['mobile_phone'] . "' and status = 0" )->count () == 0)
				M ( "SendList" )->add ( $sendData );
			D ( "GroupBond" )->where ( "id = $id" )->setField ( "is_send_msg", 1 );
			return true;
		}
	}
}
function send_grounp_bond_mail($user_id, $groupbond_id, $send = false)
{
	$is_valid = intval ( M ( "GroupBond" )->where ( "id=" . $groupbond_id )->getField ( "is_valid" ) ); //修改by hc 当无效时不发送
	if ($is_valid == 0)
		return;
	$userid = intval ( $user_id );
	$id = intval ( $groupbond_id );
	$user = D ( "User" )->getById ( $user_id );
	$bond_data = D ( "GroupBond" )->where ( "id = $id" )->find ();
	$goods_short_name = M ( "Goods" )->where ( "id=" . $bond_data ['goods_id'] )->getField ( "goods_short_name" );
	$seller_info_id = M ( "Goods" )->where ( "id=" . $bond_data ['goods_id'] )->getField ( "suppliers_id" );
	$seller_info = M ( "SuppliersDepart" )->where ( "supplier_id=" . $seller_info_id . " and is_main=1" )->find ();
	//开始模板赋值
	$user_name = $user ['user_name'];
	$bond = array ("goods_name" => $bond_data ['goods_name'], "goods_short_name" => $goods_short_name, "name" => eyooC ( 'GROUPBOTH' ), "sn" => $bond_data ['sn'], "password" => $bond_data ['password'], "order_sn" => $bond_data ['order_id'], "id" => $bond_data ['id'], "tel" => $seller_info ['tel'], "address" => $seller_info ['address'], "endtime" => toDate ( $bond_data ['end_time'] ) );
	//模板解析
	$payment_tmpl = M ( "MailTemplate" )->where ( "name='group_bond_mail'" )->find ();
	$tpl = Think::instance ( 'ThinkTemplate' );
	ob_start ();
	eval ( '?' . '>' . $tpl->parse ( $payment_tmpl ['mail_content'] ) );
	$content = ob_get_clean ();
	if ($send)
	{
		$mail = new Mail ();
		$mail->AddAddress ( $user ['email'], $user ['user_name'] );
		$mail->IsHTML ( 0 );
		$mail->Subject = $payment_tmpl ['mail_title']; // 标题
		$mail->Body = $content; // 内容
		$mail->Send ();
	} else
	{
		$sendData ['dest'] = $user ['email'];
		$sendData ['title'] = $payment_tmpl ['mail_title'];
		$sendData ['content'] = $content;
		$sendData ['create_time'] = gmtTime ();
		$sendData ['send_type'] = 0; //邮件
		$sendData ['bond_id'] = $groupbond_id;
		if (M ( "SendList" )->where ( "bond_id=" . $groupbond_id . " and dest='" . $user ['email'] . "' and status = 0" )->count () == 0)
			M ( "SendList" )->add ( $sendData );
	}
}
function utf8ToGB($str)
{
	Vendor ( 'iconv' );
	$chinese = new Chinese ();
	return $chinese->Convert ( "UTF-8", "GBK", $str );
}
function gbToUTF8($str)
{
	Vendor ( 'iconv' );
	$chinese = new Chinese ();
	return $chinese->Convert ( "GBK", "UTF-8", $str );
}
function templateFetch($templateContent, $templateVars = '', $isFile = false)
{
	if (is_array ( $templateVars ))
	{
		foreach ( $templateVars as $key => $var )
		{
			$$key = $var;
		}
	}
	if ($isFile)
	{
		$templateContent = FANWE_LANG_TMPL . "@" . $templateContent;
		if (strpos ( $templateContent, '@' ))
		{
			$templateContent = TMPL_PATH . str_replace ( array ('@', ':' ), '/', $templateContent ) . C ( 'TMPL_TEMPLATE_SUFFIX' );
		} elseif (strpos ( $templateContent, ':' ))
		{
			$templateContent = TEMPLATE_PATH . '/' . str_replace ( ':', '/', $templateContent ) . C ( 'TMPL_TEMPLATE_SUFFIX' );
		} elseif (! is_file ( $templateContent ))
		{
			$templateContent = dirname ( C ( 'TMPL_FILE_NAME' ) ) . '/' . $templateContent . C ( 'TMPL_TEMPLATE_SUFFIX' );
		}
		$templateContent = file_get_contents ( $templateContent );
	}
	$tpl = Think::instance ( 'ThinkTemplate' );
	ob_start ();
	ob_implicit_flush ( 0 );
	eval ( '?' . '>' . $tpl->parse ( $templateContent ) );
	$content = ob_get_clean ();
	return $content;
}
function sendOrderGroupBonds($orderID)
{
	$sql = "select o.*,og.number,og.attr,og.data_name,og.rec_id,g.group_bond_end_time,g.goods_short_name,g.name_1 as goods_name from " . C ( "DB_PREFIX" ) . "order as o left join " . C ( "DB_PREFIX" ) . "order_goods as og on og.order_id = o.id left join " . C ( "DB_PREFIX" ) . "goods as g on g.id = og.rec_id where o.id = '$orderID' and o.money_status = 2";
	$order = M ()->query ( $sql );
	$order = current ( $order );
	$goodsID = $order ['rec_id'];
	if ($order ['attr'] != '')
	{
		if ($order ['goods_short_name'] != '') //修改 by hc
			$goodsName = $order ['goods_short_name'] . "(" . str_replace ( "\n", ",", $order ['attr'] ) . ")";
		else
			$goodsName = $order ['goods_name'] . "(" . str_replace ( "\n", ",", $order ['attr'] ) . ")";
	} else
	{
		if ($order ['goods_short_name'] != '') //修改 by hc
			$goodsName = $order ['goods_short_name'];
		else
			$goodsName = $order ['goods_name'];
	}
	$send_count = intval ( $order ['number'] ); //本订单需要发放的方维卷
	$groupBond_m = D ( "GroupBond" );
	//修改 by hc ， 购买时发送团购券不再自动补全， 以免造成本单补全的被其他人团购时占用，改为直接下单直接发放，发放失败的自动生成.
	$groupBonds = D ( "GroupBond" )->where ( "goods_id = '$goodsID' and ((order_id = '') or (order_id = '" . $order ['sn'] . "'))" )->findAll ();
	//修改 by hc 增加验证发放下的团购券是否有效 is_valid, 存在问题，在此处验证无效时，有可能团购生成被另一进程更改，需要在autoRun中再次修复is_valid值
	$is_group_fail = M ( "Goods" )->where ( "id=" . $goodsID )->getField ( "is_group_fail" );
	$is_valid = $is_group_fail == 2 ? 1 : 0; //团购成功时，有效性为1.否则为0
	for($i = 0; $i < $send_count; $i ++)
	{
		$groupBond = $groupBonds [$i];
		//修改 by hc 修正了当同时生成团购券时，团购券被另一会员占用的BUG，在发放时再次增加验证，被占时重新生成新的， 产生的问题， 将团购券数量将有可能超出购买数量， 超出的团购券无用处.并修改buy_time的更新为当前时间。 为保证有必要的修改
		$sql_update = "update " . C ( "DB_PREFIX" ) . "group_bond set user_id=" . $order ['user_id'] . ", order_id='" . $order ['sn'] . "',is_valid=" . $is_valid . ", status = 1, buy_time =" . $order ['create_time'] . ", create_time =" . gmtTime () . ", goods_name = '" . $goodsName . "' where goods_id=" . $goodsID . " and (order_id='' or order_id='" . $order ['sn'] . "') and id = " . intval ( $groupBond ['id'] );
		if (M ()->execute ( $sql_update ) == 0)
		{
			if (intval ( $groupBonds [$i + 1] ['id'] ) != 0)
			{
				//修改by hc, 下张团购券有ID. 直接进入下轮循环，直到所有预设团购券都被人分配光，重新生成团购券。
				continue;
			}
			//被占用时再，或没更新成功，即团购券不足时
			$groupBond_new = $groupBond_m->create ();
			$tempsn = gen_groupbond_sn ( $goodsID );
			$groupBond_new ['user_id'] = $order ['user_id'];
			$groupBond_new ['order_id'] = $order ['sn'];
			$groupBond_new ['goods_id'] = $goodsID;
			$groupBond_new ['goods_name'] = $goodsName;
			$groupBond_new ['sn'] = $tempsn;
			$password = unpack ( 'H8', str_shuffle ( md5 ( uniqid () ) ) );
			$groupBond_new ['password'] = $password [1];
			$groupBond_new ['create_time'] = gmtTime ();
			$groupBond_new ['end_time'] = $order ['group_bond_end_time'];
			if (! empty ( $order ['group_bond_end_time'] ))
			{
				$groupBond_new ['end_time'] = $order ['group_bond_end_time'];
			} else
			{
				$groupBond_new ['end_time'] = gmtTime () + 3600 * 24 * 30; //设置一个月后过期
			}
			$groupBond_new ['status'] = 1;
			$groupBond_new ['buy_time'] = $order ['create_time'];
			$groupBond_new ['is_valid'] = $is_valid; //修改 by hc,新生成团购券时生效有效状态
			$bondID = $groupBond_m->add ( $groupBond_new );
			//dump($groupBond_m->getLastSql());
			while ( $bondID == 0 || $bondID == false )
			{
				$tempsn = gen_groupbond_sn ( $goodsID );
				$groupBond_new ['sn'] = $tempsn;
				$bondID = $groupBond_m->add ( $groupBond_new );

		//dump($groupBond_m->getLastSql());
			}
			$groupBond = M ( "GroupBond" )->getById ( $bondID );

		//再次补发结束
		}
		//dump(D("GroupBond")->getLastSql());
		//发放团购卷时，自动短信通知
		//修改 by hc 增加团购券的是否发短信的设置
		if (eyooC ( 'AUTO_SEND_SMS' ) == 1 && M ( "Goods" )->where ( "id=" . $goodsID )->getField ( "allow_sms" ) == 1)
		{
			send_sms ( $order ['user_id'], $groupBond ['id'] );
		}
		if (eyooC ( "MAIL_ON" ) == 1 && eyooC ( "SEND_GROUPBOND_MAIL" ) == 1)
		{
			send_grounp_bond_mail ( $order ['user_id'], $groupBond ['id'] );
		}
	}
	M ( "Order" )->where ( "id = '$orderID'" )->setField ( array ('goods_status', 'status' ), array (5, 0 ) ); //不需配送的商品，直接设置成：无需配送  add by chenfq 2010-05-06
}
function getCol($sql, $field_name)
{
	//$res = $this->query($sql);
	$item_list = M ()->query ( $sql );
	if ($item_list !== false)
	{
		$arr = array ();
		foreach ( $item_list as $item )
		{
			$arr [] = $item [$field_name];
		}
		return $arr;
	} else
	{
		return false;
	}
}
/**
 * 创建像这样的查询: "IN('a','b')";
 *
 * @access   public
 * @param    mix      $item_list      列表数组或字符串
 * @param    string   $field_name     字段名称
 *
 * @return   void
 */
function db_create_in($item_list, $field_name = '')
{
	if (empty ( $item_list ))
	{
		return $field_name . " IN ('') ";
	} else
	{
		if (! is_array ( $item_list ))
		{
			$item_list = explode ( ',', $item_list );
		}
		$item_list = array_unique ( $item_list );
		$item_list_tmp = '';
		foreach ( $item_list as $item )
		{
			if ($item !== '')
			{
				$item_list_tmp .= $item_list_tmp ? ",'$item'" : "'$item'";
			}
		}
		if (empty ( $item_list_tmp ))
		{
			return $field_name . " IN ('') ";
		} else
		{
			return $field_name . ' IN (' . $item_list_tmp . ') ';
		}
	}
}
/**
 * 初始化会员数据整合类
 *
 * @access  public
 * @return  object
 */
function &init_users()
{
	$set_modules = false;
	static $cls = null;
	if ($cls != null)
	{
		return $cls;
	}
	$code = eyooC ( 'INTEGRATE_CODE' );
	if (empty ( $code ))
		$code = 'fanwe';
	include_once (VENDOR_PATH . 'integrates/' . $code . '.php');
	$cfg = unserialize ( eyooC ( 'INTEGRATE_CONFIG' ) );
	$cls = new $code ( $cfg );
	return $cls;
}
/**
 * 调用UCenter的函数
 *
 * @param   string  $func
 * @param   array   $params
 *
 * @return  mixed
 */
function uc_call($func, $params = null)
{
	restore_error_handler ();
	if (! function_exists ( $func ))
	{
		include_once (VENDOR_PATH . 'uc_client/client.php');
	}
	$res = call_user_func_array ( $func, $params );
	set_error_handler ( 'exception_handler' );
	return $res;
}
//全站通用的清除所有缓存的方法
function clear_cache()
{
	Dir::delDir ( getcwd () . "/admin/Runtime/Cache/" );
	Dir::delDir ( getcwd () . "/admin/Runtime/Data/" );
	Dir::delDir ( getcwd () . "/admin/Runtime/Temp/" );
	@unlink ( getcwd () . "/admin/Runtime/~app.php" );
	@unlink ( getcwd () . "/admin/Runtime/~runtime.php" );
	Dir::delDir ( getcwd () . "/home/Runtime/Cache/" );
	Dir::delDir ( getcwd () . "/home/Runtime/Data/" );
	Dir::delDir ( getcwd () . "/home/Runtime/Temp/" );
	Dir::delDir ( getcwd () . "/home/Runtime/caches/" );
	Dir::delDir ( getcwd () . "/home/Runtime/compiled/" );
	Dir::delDir ( getcwd () . "/home/Runtime/" . HTML_DIR . '/' );
	@unlink ( getcwd () . "/home/Runtime/~app.php" );
	@unlink ( getcwd () . "/home/Runtime/~runtime.php" );
	@unlink ( getcwd () . "/home/Runtime/js_lang.js" );
	Dir::delDir ( getcwd () . "/install/Runtime/Cache/" );
	Dir::delDir ( getcwd () . "/install/Runtime/Data/" );
	Dir::delDir ( getcwd () . "/install/Runtime/Temp/" );
	@unlink ( getcwd () . "/install/Runtime/~app.php" );
	@unlink ( getcwd () . "/install/Runtime/~runtime.php" );
	Dir::delDir ( getcwd () . "/mobile/Runtime/Cache/" );
	Dir::delDir ( getcwd () . "/mobile/Runtime/Data/" );
	Dir::delDir ( getcwd () . "/mobile/Runtime/Temp/" );
	@unlink ( getcwd () . "/mobile/Runtime/~app.php" );
	@unlink ( getcwd () . "/mobile/Runtime/~runtime.php" );
	Dir::delDir ( getcwd () . "/update/Runtime/Cache/" );
	Dir::delDir ( getcwd () . "/update/Runtime/Data/" );
	Dir::delDir ( getcwd () . "/update/Runtime/Temp/" );
	@unlink ( getcwd () . "/update/Runtime/~app.php" );
	@unlink ( getcwd () . "/update/Runtime/~runtime.php" );
}
//过滤请求
function filter_request(&$request)
{
	if (MAGIC_QUOTES_GPC)
	{
		foreach ( $request as $k => $v )
		{
			if (is_array ( $v ))
			{
				filter_request ( $v );
			} else
			{
				$request [$k] = stripslashes ( trim ( $v ) );
			}
		}
	}
}
/**
 * 获得当前格林威治时间的时间戳
 *
 * @return  integer
 */
function gmtTime()
{
	return (time () - date ( 'Z' ));
}
function toDate($time, $format = 'Y-m-d H:i:s')
{
	if (empty ( $time ))
	{
		return '';
	}
	//$timezone = intval(eyooC('TIME_ZONE'));
	//echo $timezone;
	$time = $time + $timezone * 3600;
	$format = str_replace ( '#', ':', $format );
	return date ( $format, $time );
}
function write_timezone()
{
	$var = array ('0' => 'UTC', '8' => 'PRC' );
	//开始将$db_config写入配置
	$timezone_config_str = "<?php\r\n";
	$timezone_config_str .= "return array(\r\n";
	$timezone_config_str .= "'DEFAULT_TIMEZONE'=>'" . $var [eyooC ( 'TIME_ZONE' )] . "',\r\n";
	$timezone_config_str .= ");\r\n";
	$timezone_config_str .= "?>";
	@file_put_contents ( getcwd () . "/config/global_config.php", $timezone_config_str );
}
// 发送邮件/短信消息队列 by hc
function send_msg_list()
{
	$msg_list = M ( "SendList" )->where ( "status=0" )->findAll ();
	$sms = D ( "SmsPlf" );
	foreach ( $msg_list as $msg )
	{
		$msg ['status'] = 1;
		$msg ['send_time'] = gmtTime ();
		M ( "SendList" )->save ( $msg );
		//默认为已发送
		if ($msg ['send_type'] == 1)
		{
			if (eyooC ( "IS_SMS" ) == 1)
			{
				if (empty ( $msg ['dest'] ))
				{
					M ( "SendList" )->where ( "id = " . $msg ['id'] )->delete ();
				} else
				{
					if ($sms->sendSMS ( $msg ['dest'], $msg ['content'] ))
					{
						if ($msg ['bond_id'] > 0) //团购券的发送，记录发送状态
						{
							D ( "GroupBond" )->where ( "id =" . $msg ['bond_id'] )->setField ( "is_send_msg", 1 );
							M ( "GroupBond" )->setInc ( "send_count", "id =" . $msg ['bond_id'], 1 );
							Log::record ( "SendSMSStatus:" . $sms->message );
							Log::save ();
						}
					} else
					{
						$msg ['status'] = 0;
						M ( "SendList" )->save ( $msg );
						if ($msg ['bond_id'] > 0) //团购券的发送，记录发送状态
						{
							D ( "GroupBond" )->where ( "id =" . $msg ['bond_id'] )->setField ( "is_send_msg", 0 );
							Log::record ( "SendSMSStatus:" . $sms->message );
							Log::save ();
						}
					}
				}
			}
		}
		if ($msg ['send_type'] == 0)
		{
			if (eyooC ( "MAIL_ON" ) == 1)
			{
				$mail = new Mail ();
				$mail->AddAddress ( $msg ['dest'] );
				$mail->IsHTML ( 1 );
				$mail->Subject = $msg ['title']; // 标题
				$mail->Body = $msg ['content']; // 内容
				$mail->Send ();

		//					if($mail->ErrorInfo!='')
			//					{
			//						$msg['status'] = 0;
			//						M("SendList")->save($msg);
			//					}
			}
		}
	}
}
// 发送邮件群发队列 by hc
function send_mail_list()
{
	$msg_list = M ( "MailSendList" )->where ( "status=0 and send_time <=" . gmtTime () )->findAll ();
	foreach ( $msg_list as $msg )
	{
		$msg ['status'] = 1;
		M ( "MailSendList" )->save ( $msg );
		if ($msg ['rec_module'] == 'Email')
		{
			M ( "MailList" )->where ( "id=" . $msg ['rec_id'] )->setField ( "status", 1 ); //设为已发送
		}
		//默认为已发送
		if (eyooC ( "MAIL_ON" ) == 1)
		{
			$mail = new Mail ();
			$mail->AddAddress ( $msg ['mail_address'] );
			$mail->IsHTML ( 1 );
			$mail->Subject = $msg ['mail_title']; // 标题
			$mail->Body = $msg ['mail_content']; // 内容
			$mail->Send ();

		//					if($mail->ErrorInfo!='')
		//					{
		//						$msg['status'] = 0;
		//						M("MailSendList")->save($msg);
		//					}
		}
	}
}
function pushMail()
{
	$time = gmtTime ();
	$mail_list = D ( "MailList" )->where ( 'send_time<=' . $time . ' and status=0' )->findAll ();
	$allmail_list = D ( "MailList" )->findAll ();
	//先删除邮件的发送人
	foreach ( $allmail_list as $mail_item )
	{
		M ( "MailSendList" )->where ( "status=0 and rec_module='Email' and rec_id=" . $mail_item ['id'] )->delete ();
	}
	foreach ( $mail_list as $mail_item )
	{
		$address_send_list = D ( "MailAddressSendList" )->where ( "mail_id=" . $mail_item ['id'] )->findAll ();
		foreach ( $address_send_list as $address_item )
		{
			$address_item = D ( "MailAddressList" )->where ( "status=1 and id='" . $address_item ['mail_address_id'] . "'" )->find ();
			if ($address_item)
			{
				$userinfo = D ( "User" )->getById ( $address_item ['user_id'] );
				if ($userinfo)
				{
					$username = $userinfo ['user_name'];
					if ($userinfo ['nickname'] != '')
					{
						$username .= "(" . $userinfo ['nickname'] . ")";
					}
				} else
				{
					$username = '匿名用户';
				}
				//						$mail = new Mail();
				//						$mail->IsHTML(1); // 设置邮件格式为 HTML
				$mail_title = $mail_item ['mail_title'];
				//开始为邮件内容赋值
				if ($mail_item ['goods_id'] == 0)
					$mail_content = $mail_item ['mail_content'];
				else
				{
					$tpl = Think::instance ( 'ThinkTemplate' );
					$mail_tpl = file_get_contents ( getcwd () . "/Public/mail_template/" . eyooC ( "GROUP_MAIL_TMPL" ) . "/" . eyooC ( "GROUP_MAIL_TMPL" ) . ".html" ); //邮件群发的模板
					$mail_tpl = str_replace ( eyooC ( "GROUP_MAIL_TMPL" ) . "_files/", eyooC ( "SHOP_URL" ) . __ROOT__ . "/Public/mail_template/" . eyooC ( "GROUP_MAIL_TMPL" ) . "/" . eyooC ( "GROUP_MAIL_TMPL" ) . "_files/", $mail_tpl );
					//开始定义模板变量
					$v = M ( "Goods" )->getById ( $mail_item ['goods_id'] );
					//$city_name
					$city_name = M ( "GroupCity" )->where ( "id=" . $v ['city_id'] )->getField ( "name" );
					//$shop_name
					$shop_name = SHOP_NAME;
					//$cancel_url
					$cancel_url = eyooC ( "SHOP_URL" ) . __ROOT__ . "/index.php?m=Index&a=unSubScribe&email=" . $address_item ['mail_address'];
					//$sender_email
					$sender_email = eyooC ( "REPLY_ADDRESS" );
					//$send_date
					$send_date = toDate ( gmtTime (), 'Y年m月d日' );
					$weekarray = array ("日", "一", "二", "三", "四", "五", "六" );
					$send_date .= " 星期" . $weekarray [toDate ( gmtTime (), "w" )];
					//$shop_url
					$shop_url = eyooC ( "SHOP_URL" ) . __ROOT__;
					//$tel_number
					$tel_number = eyooC ( "TEL" );
					//$tg_info
					$tg_info = D ( "Goods" )->getGoodsItem ( $v ['id'], $v ['city_id'] );
					$tg_info ['title'] = $tg_info ['name_1'];
					$tg_info ['price'] = $tg_info ['shop_price_format'];
					$tg_info ['origin_price'] = $tg_info ['market_price_format'];
					$tg_info ['discount'] = $tg_info ['discountfb'];
					$tg_info ['save_money'] = $tg_info ['save'];
					$tg_info ['big_img'] = eyooC ( "SHOP_URL" ) . __ROOT__ . $tg_info ['big_img'];
					$tg_info ['desc'] = str_replace ( "./Public/", eyooC ( "SHOP_URL" ) . __ROOT__ . "/Public/", $tg_info ['goods_desc_1'] );
					//$sale_info
					$sale_info ['title'] = $tg_info ['suppliers'] ['name'];
					$sale_info ['url'] = $tg_info ['suppliers'] ['web'];
					$sale_info ['tel_num'] = $tg_info ['suppliers'] ['tel'];
					$sale_info ['map_url'] = $tg_info ['suppliers'] ['map'];
					//$referral
					$referral ['amount'] = eyooC ( "REFERRALS_MONEY" );
					if (eyooC ( "REFERRAL_TYPE" ) == 0)
					{
						$referral ['amount'] = formatPrice ( $referral ['amount'] );
					} else
					{
						$referral ['amount'] = $referral ['amount'] . "" . L ( "SCORE_UNIT" );
					}
					if (eyooC ( "URL_ROUTE" ) == 0)
						$referral ['url'] = eyooC ( "SHOP_URL" ) . __ROOT__ . "/index.php?m=Referrals&a=index";
					else
						$referral ['url'] = eyooC ( "SHOP_URL" ) . __ROOT__ . "/Referrals-index.html";
					ob_start ();
					eval ( '?' . '>' . $tpl->parse ( $mail_tpl ) );
					$content = ob_get_clean ();
					$mail_content = $content;
				} //end 通知模板的赋值
				//$cancel_url
				$cancel_url = eyooC ( "SHOP_URL" ) . __ROOT__ . "/index.php?m=Index&a=unSubScribe&email=" . $address_item ['mail_address'];
				$mail_content = "如不想继续收" . SHOP_NAME . "的邮件，您可随时<a href='" . $cancel_url . "' title='取消订阅'>取消订阅</a><br /><br />" . $mail_content;
				$mail_title = str_replace ( "{\$username}", $username, $mail_title );
				$mail_content = str_replace ( "{\$username}", $username, $mail_content );
				//						$mail->Subject = $mail_title; // 标题
				//						$mail->Body =  $mail_content; // 内容
				//						$mail->AddAddress($address_item['mail_address'],$username);
				//						if(!$mail->Send())
				//						{
				//							$this->error($mail->ErrorInfo,$ajax);
				//						}
				// 修改为插入邮件群发队列
				if (M ( "MailSendList" )->where ( "status=0 and mail_address='" . $address_item ['mail_address'] . "' and rec_module='Email' and rec_id=" . $mail_item ['id'] )->count () == 0)
				{
					$sendData ['mail_address'] = $address_item ['mail_address'];
					$sendData ['mail_title'] = $mail_title;
					$sendData ['mail_content'] = $mail_content;
					$sendData ['send_time'] = $mail_item ['send_time'];
					$sendData ['rec_module'] = 'Email';
					$sendData ['rec_id'] = $mail_item ['id'];
					M ( "MailSendList" )->add ( $sendData );
				} //为避免重复插入队列
			}
		}
	}
}
function gen_groupbond_sn($goodsID)
{
	do
	{
		$r_sn = rand ( 100000, 999999 );
		$sn = str_pad ( $r_sn, 6, '0', STR_PAD_LEFT );
	} while ( M ( "GroupBond" )->where ( "sn='" . $sn . "' and goods_id=" . $goodsID )->count () > 0 );
	return $sn;
}
//订单相关操作时的缓存更新
function clear_user_order_cache($order_id)
{
	$user_id = intval ( $_SESSION ['user_id'] );
	//删除会员订单缓存
	$order_page_count = M ( "Order" )->where ( "user_id=" . $user_id )->count ();
	$order_page_count = ceil ( $order_page_count / eyooC ( "PAGE_LISTROWS" ) );
	for($i = 0; $i <= $order_page_count; $i ++)
	{
		S ( "CACHE_ORDER_LIST_" . $user_id . "_" . $i, NULL );
		S ( "CACHE_BELOW_ORDER_LIST_" . $user_id . "_" . $i, NULL );
	}
	//更新配送缓存
	$sql = "select max(id) as maxid from " . C ( "DB_PREFIX" ) . "user_consignee where user_id = " . $user_id;
	$tmp = M ()->query ( $sql );
	$consignee_id = $tmp [0] ['maxid'];
	S ( "CACHE_CONSIGNEE_" . $consignee_id, NULL );
	//更新商品缓存
	$goods_id = M ( "OrderGoods" )->where ( "order_id=" . $order_id )->getField ( "rec_id" );
	S ( "CACHE_GOODS_CACHE_" . $goods_id, NULL );
	S ( "CACHE_USER_BUY_COUNT_" . intval ( $_SESSION ['user_id'] ) . "_" . $goods_id, NULL );
	S ( "CACHE_CART_GOODS_CACHE_" . $goods_id, NULL );
	//优惠券
	$ecv_page_count = M ( "Ecv" )->where ( "user_id=" . $user_id )->count ();
	$ecv_page_count = ceil ( $ecv_page_count / eyooC ( "PAGE_LISTROWS" ) );
	for($i = 1; $i <= $ecv_page_count; $i ++)
	{
		S ( "CACHE_ECV_LIST_" . $user_id . "_0_" . $i, NULL );
		S ( "CACHE_ECV_LIST_" . $user_id . "_1_" . $i, NULL );
		S ( "CACHE_ECV_LIST_" . $user_id . "_2_" . $i, NULL );
	}
	//团购券
	$gb_page_count = M ( "GroupBond" )->where ( "user_id=" . $user_id )->count ();
	$gb_page_count = ceil ( $gb_page_count / eyooC ( "PAGE_LISTROWS" ) );
	for($i = 1; $i <= $gb_page_count; $i ++)
	{
		S ( "CACHE_GROUP_BOND_LIST_" . $user_id . "_0_" . $i, NULL );
		S ( "CACHE_GROUP_BOND_LIST_" . $user_id . "_1_" . $i, NULL );
		S ( "CACHE_GROUP_BOND_LIST_" . $user_id . "_2_" . $i, NULL );
		S ( "CACHE_GROUP_BOND_LIST_" . $user_id . "_3_" . $i, NULL );
	}
	//更新用户数据
	S ( "CACHE_USER_INFO_" . $user_id, NULL );
	//更新需发团购券商品
	S ( "CACHE_SUCCESS_GOODS_LIST", NULL );
	S ( "CACHE_ORDER_DELIVERYS_" . $user_id, NULL );
}
//当会员登录修改时更新用户缓存
function upd_user_cache()
{
	$user_id = intval ( $_SESSION ['user_id'] );
	//团购券
	$gb_page_count = M ( "GroupBond" )->where ( "user_id=" . $user_id )->count ();
	$gb_page_count = ceil ( $gb_page_count / eyooC ( "PAGE_LISTROWS" ) );
	for($i = 1; $i <= $gb_page_count; $i ++)
	{
		S ( "CACHE_GROUP_BOND_LIST_" . $user_id . "_0_" . $i, NULL );
		S ( "CACHE_GROUP_BOND_LIST_" . $user_id . "_1_" . $i, NULL );
		S ( "CACHE_GROUP_BOND_LIST_" . $user_id . "_2_" . $i, NULL );
		S ( "CACHE_GROUP_BOND_LIST_" . $user_id . "_3_" . $i, NULL );
	}
	//更新用户数据
	S ( "CACHE_USER_INFO_" . $user_id, NULL );
	//更新需发团购券商品
	S ( "CACHE_SUCCESS_GOODS_LIST", NULL );
}
//帮助人数
function getBgCount($id)
{
	$Answer = D ( "Answer" );
	$bganswerwhere ["user_id"] = $id;
	$bganswer = $Answer->where ( $bganswerwhere )->group ( "ask_id" )->Select ();
	$bgcount = count ( $bganswer );
	return $bgcount;
}
//帮助人数采纳率
function getBgCountCnl($id)
{
	$Answer = D ( "Answer" );
	$bgcounts = $Answer->where ( $bganswerwhere )->Count ();
	$bgIstrueWhere ["is_true"] = 1;
	$bgIstrueWhere ["user_id"] = $id;
	$bgIstrue = $Answer->where ( $bgIstrueWhere )->Count ();
	$bgCnl = substr ( $bgIstrue / $bgcounts * 100, 0, 5 );
	return $bgCnl;
}
function getIntor($id)
{
	$UserInfo = D ( "UserInfo" );
	$where ["uid"] = $id;
	$UserInfo = $UserInfo->where ( $where )->find ();
	return $UserInfo ["intor"];
}

function imagezoom( $srcimage, $dstimage,  $dst_width, $dst_height, $backgroundcolor ) {



         // 中文件名乱码

         if ( PHP_OS == 'WINNT' ) {

                 $srcimage = iconv('UTF-8', 'GBK', $srcimage);

                 $dstimage = iconv('UTF-8', 'GBK', $dstimage);

        }



     $dstimg = imagecreatetruecolor( $dst_width, $dst_height );

     $color = imagecolorallocate($dstimg

         , hexdec(substr($backgroundcolor, 1, 2))

         , hexdec(substr($backgroundcolor, 3, 2))

         , hexdec(substr($backgroundcolor, 5, 2))

     );

     imagefill($dstimg, 0, 0, $color);



     if ( !$arr=getimagesize($srcimage) ) {

                 echo "要生成缩略图的文件不存在";

                 exit;

         }



     $src_width = $arr[0];

     $src_height = $arr[1];

     $srcimg = null;

     $method = getcreatemethod( $srcimage );

     if ( $method ) {

         eval( '$srcimg = ' . $method . ';' );

     }



     $dst_x = 0;

     $dst_y = 0;

     $dst_w = $dst_width;

     $dst_h = $dst_height;

     if ( ($dst_width / $dst_height - $src_width / $src_height) > 0 ) {

         $dst_w = $src_width * ( $dst_height / $src_height );

         $dst_x = ( $dst_width - $dst_w ) / 2;

     } elseif ( ($dst_width / $dst_height - $src_width / $src_height) < 0 ) {

         $dst_h = $src_height * ( $dst_width / $src_width );

         $dst_y = ( $dst_height - $dst_h ) / 2;

     }



     imagecopyresampled($dstimg, $srcimg, $dst_x

         , $dst_y, 0, 0, $dst_w, $dst_h, $src_width, $src_height);



     // 保存格式

     $arr = array(

         'jpg' => 'imagejpeg'

         , 'jpeg' => 'imagejpeg'

         , 'png' => 'imagepng'

         , 'gif' => 'imagegif'

         , 'bmp' => 'imagebmp'

     );

     $suffix = strtolower( array_pop(explode('.', $dstimage ) ) );

     if (!in_array($suffix, array_keys($arr)) ) {

         echo "保存的文件名错误";

         exit;

     } else {

         eval( $arr[$suffix] . '($dstimg, "'.$dstimage.'");' );

     }



     imagejpeg($dstimg, $dstimage);



     imagedestroy($dstimg);

     imagedestroy($srcimg);



 }





 function getcreatemethod( $file ) {

         $arr = array(

                 '474946' => "imagecreatefromgif('$file')"

                 , 'FFD8FF' => "imagecreatefromjpeg('$file')"

                 , '424D' => "imagecreatefrombmp('$file')"

                 , '89504E' => "imagecreatefrompng('$file')"

         );

         $fd = fopen( $file, "rb" );

        $data = fread( $fd, 3 );



         $data = str2hex( $data );



        if ( array_key_exists( $data, $arr ) ) {

                 return $arr[$data];

         } elseif ( array_key_exists( substr($data, 0, 4), $arr ) ) {

                 return $arr[substr($data, 0, 4)];

         } else {

                 return false;

         }

 }



 function str2hex( $str ) {

         $ret = "";



         for( $i = 0; $i < strlen( $str ) ; $i++ ) {

                 $ret .= ord($str[$i]) >= 16 ? strval( dechex( ord($str[$i]) ) )

                        : '0'. strval( dechex( ord($str[$i]) ) );

         }



         return strtoupper( $ret );

 }



 // BMP 创建函数  php本身无

 function imagecreatefrombmp($filename)
 {

    if (! $f1 = fopen($filename,"rb")) return FALSE;



    $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));

    if ($FILE['file_type'] != 19778) return FALSE;



    $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.

                  '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.

                  '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));

    $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);

    if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];

    $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;

    $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);

    $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);

    $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);

    $BMP['decal'] = 4-(4*$BMP['decal']);

    if ($BMP['decal'] == 4) $BMP['decal'] = 0;



    $PALETTE = array();

    if ($BMP['colors'] < 16777216)

    {

    $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));

    }



   $IMG = fread($f1,$BMP['size_bitmap']);

    $VIDE = chr(0);



    $res = imagecreatetruecolor($BMP['width'],$BMP['height']);

    $P = 0;

    $Y = $BMP['height']-1;

    while ($Y >= 0)

    {

         $X=0;

         while ($X < $BMP['width'])
         {

          if ($BMP['bits_per_pixel'] == 24)

             $COLOR = unpack("V",substr($IMG,$P,3).$VIDE);

          elseif ($BMP['bits_per_pixel'] == 16)

          {

             $COLOR = unpack("n",substr($IMG,$P,2));

             $COLOR[1] = $PALETTE[$COLOR[1]+1];

          }

          elseif ($BMP['bits_per_pixel'] == 8)

          {

             $COLOR = unpack("n",$VIDE.substr($IMG,$P,1));

             $COLOR[1] = $PALETTE[$COLOR[1]+1];

          }

          elseif ($BMP['bits_per_pixel'] == 4)

          {

             $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));

             if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);

             $COLOR[1] = $PALETTE[$COLOR[1]+1];

          }

          elseif ($BMP['bits_per_pixel'] == 1)

          {

             $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));

             if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;

             elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;

             elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;

             elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;

             elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;

             elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;

             elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;

             elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);

             $COLOR[1] = $PALETTE[$COLOR[1]+1];

          }
          else

             return FALSE;

          imagesetpixel($res,$X,$Y,$COLOR[1]);

          $X++;

          $P += $BMP['bytes_per_pixel'];

         }

         $Y--;

         $P+=$BMP['decal'];

    }

    fclose($f1);



 return $res;

 }

 // BMP 保存函数，php本身无

 function imagebmp ($im, $fn = false)

 {

     if (!$im) return false;



     if ($fn === false) $fn = 'php://output';

     $f = fopen ($fn, "w");

     if (!$f) return false;



     $biWidth = imagesx ($im);

     $biHeight = imagesy ($im);

     $biBPLine = $biWidth * 3;

     $biStride = ($biBPLine + 3) & ~3;

     $biSizeImage = $biStride * $biHeight;

     $bfOffBits = 54;

     $bfSize = $bfOffBits + $biSizeImage;



     fwrite ($f, 'BM', 2);

     fwrite ($f, pack ('VvvV', $bfSize, 0, 0, $bfOffBits));



     fwrite ($f, pack ('VVVvvVVVVVV', 40, $biWidth, $biHeight, 1, 24, 0, $biSizeImage, 0, 0, 0, 0));



     $numpad = $biStride - $biBPLine;

     for ($y = $biHeight - 1; $y >= 0; --$y)
    {

        for ($x = 0; $x < $biWidth; ++$x)
    {
             $col = imagecolorat ($im, $x, $y);

             fwrite ($f, pack ('V', $col), 3);
         }

         for ($i = 0; $i < $numpad; ++$i)

             fwrite ($f, pack ('C', 0));

     }

     fclose ($f);

     return true;

 }
?>