<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 *//* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = (isset($modules)) ? count($modules) : 0;

    /* 会员数据整合插件的代码必须和文件名保持一致 */
    $modules[$i]['code']    = 'Tencentpay';

    /* 被整合的第三方程序的名称 */
    $modules[$i]['name']    = '财付通支付';

    /* 被整合的第三方程序的版本 */
    $modules[$i]['version'] = '2.0';

    /* 插件的作者 */
    $modules[$i]['author']  = 'FANWE R&D TEAM';

    /* 支付方式：1：在线支付；0：线下支付 */
    $modules[$i]['online_pay'] = '1';
        
    /* 插件作者的官方网站 */
    $modules[$i]['website'] = 'http://www.fanwe.com';

    return;
}

// 财付通模型
require_once(VENDOR_PATH.'payment/Payment.class.php');
class TencentpayPayment extends Think implements Payment  {
	public $config = array(
	    'tencentpay_id'=>'',  //财付通商户号
        'tencentpay_key'=>'',  //财付通商户密钥
		'tencentpay_sign'=>'',  //自定义签名

	);	
	public function getPaymentCode($payment_log_id, $money, $payment_id, $currency_id)
	{   
        $money = round($money,2);
		$currency = D("Currency")->getById($currency_id);
		$payment_info = D("Payment")->getById($payment_id);
		$payment_info['config'] = unserialize($payment_info['config']);
		/* 订单描述，用订单号替代 */
        $payment_log = M("PaymentLog")->getById($payment_log_id);
		C("URL_MODEL",3);
		$data_return_url = 'http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/index.php?m=Payment&a=response&payment_name=Tencentpay';
        
        $cmd_no = '1';

        /* 获得订单的流水号，补零到10位 */
        $sp_billno = $payment_log_id;

        /* 交易日期 */
        $today = toDate($payment_log['create_time'],'Ymd');

        /* 将商户号+年月日+流水号 */
        $bill_no = str_pad($payment_log_id, 10, 0, STR_PAD_LEFT);
        $transaction_id = $payment_info['config']['tencentpay_id'].$today.$bill_no;

        /* 银行类型:支持纯网关和财付通 */
        $bank_type = '0';

        
        
//		$orderGoods = M('OrderGoods')->where("order_id=".$payment_log['rec_id'])->find();
//		if($orderGoods){
//			$goods_data = M("Goods")->getById($orderGoods['rec_id']);			
//			$data_sn = $goods_data['goods_short_name']==''?$goods_data['name_1']:$goods_data['goods_short_name'];
//			//$quantity = intval(M('OrderGoods')->where("order_id=".$payment_log['rec_id'])->sum('number'));
//		}else{
//			//$quantity = 1;
//			$data_sn = D($payment_log['rec_module'])->where("id=".$payment_log['rec_id'])->getField("sn");
//		}

		$data_sn = D($payment_log['rec_module'])->where("id=".$payment_log['rec_id'])->getField("sn");  //数据的单号
        $desc = $data_sn;
        $attach = $payment_info['config']['tencentpay_sign'];

        /* 编码标准 */
        if (String::is_utf8($desc))
        {
            $desc = iconv('utf-8', 'gbk', $desc);
        }
        

        /* 返回的路径 */
        $return_url = $data_return_url;

        /* 总金额 */
        $total_fee = $money*100;
		
		/* ip */
		$spbill_create_ip = 	$_SERVER['REMOTE_ADDR'];


        /* 货币类型 */
        $fee_type = '1';

        /* 重写自定义签名 */
        //$payment['magic_string'] = abs(crc32($payment['magic_string']));

        /* 数字签名 */
        $sign_text = "cmdno=" . $cmd_no . "&date=" . $today . "&bargainor_id=" . $payment_info['config']['tencentpay_id'] .
          "&transaction_id=" . $transaction_id . "&sp_billno=" . $sp_billno .
          "&total_fee=" . $total_fee . "&fee_type=" . $fee_type . "&return_url=" . $return_url .
          "&attach=" . $attach  ."&spbill_create_ip=".$spbill_create_ip.  "&key=" . $payment_info['config']['tencentpay_key'];
        $sign = strtoupper(md5($sign_text));


        /* 交易参数 */
        $parameter = array(
            'cmdno'             => $cmd_no,                     // 业务代码, 财付通支付支付接口填  1
            'date'              => $today,                      // 商户日期：如20051212
            'bank_type'         => $bank_type,                  // 银行类型:支持纯网关和财付通
            'desc'              => $desc,                       // 交易的商品名称
            'purchaser_id'      => '',                          // 用户(买方)的财付通帐户,可以为空
            'bargainor_id'      => $payment_info['config']['tencentpay_id'],  // 商家的财付通商户号
            'transaction_id'    => $transaction_id,             // 交易号(订单号)，由商户网站产生(建议顺序累加)
            'sp_billno'         => $sp_billno,                  // 商户系统内部的定单号,最多10位
            'spbill_create_ip'   => $spbill_create_ip,            // ip
            'total_fee'         => $total_fee,                  // 订单金额
            'fee_type'          => $fee_type,                   // 现金支付币种
            'return_url'        => $return_url,                 // 接收财付通返回结果的URL
            'attach'            => $attach,                     // 用户自定义签名
            'sign'              => $sign,                       // MD5签名
            //'sys_id'            => '542554970',                 //ecshop C账号 不参与签名
            //'sp_suggestuser'    => '1202822001'                 //财付通分配的商户号

        );

        $def_url  = '<br /><form style="text-align:center;" action="https://www.tenpay.com/cgi-bin/v1.0/pay_gate.cgi" style="margin:0px;padding:0px" >';

        foreach ($parameter AS $key=>$val)
        {
            $def_url  .= "<input type='hidden' name='$key' value='$val' />";
        }
		
		if(!empty($payment_info['logo']))
			$def_url .= "<input type='image' src='__ROOT__".$payment_info['logo']."' style='border:solid 1px #ccc;'><div class='blank'></div>";
			
        $def_url .= "<input type='submit' class='paybutton' value='" .L("TENCENTPAY_PAYMENT_BUTTON"). "'></form>";
		
		$def_url.="<br /><span class='red'>".L("PAY_TOTAL_PRICE").":".$currency['unit']." ".number_format($money,2)."</span>";
        return $def_url;
        
        
	}
	
	public function dealResult($get,$post,$request)
	{
		$return_res = array(
			'info'=>'',
			'status'=>false,
		);
		
		$payment   =  D("Payment")->where("class_name='Tencentpay'")->find();   
		//$currency_radio = D("Currency")->where("id=".$payment['currency'])->getField("radio");
    	$payment['config'] = unserialize($payment['config']);
		
	 	/*取返回参数*/
        $cmd_no         = $request['cmdno'];
        $pay_result     = $request['pay_result'];
        $pay_info       = $request['pay_info'];
        $bill_date      = $request['date'];
        $bargainor_id   = $request['bargainor_id'];
        $transaction_id = $request['transaction_id'];
        $sp_billno      = $request['sp_billno'];
        $total_fee      = $request['total_fee'];
        $fee_type       = $request['fee_type'];
        $attach         = $request['attach'];
        $sign           = $request['sign'];

        //$payment    = D("Payment")->where("class_name='Tencentpay'")->find(); 
        //$order_sn   = $bill_date . str_pad(intval($sp_billno), 5, '0', STR_PAD_LEFT);
        //$log_id = preg_replace('/0*([0-9]*)/', '\1', $sp_billno); //取得支付的log_id
        $log_id = intval($sp_billno);
 

        /* 如果pay_result大于0则表示支付失败 */
        if ($pay_result > 0)
        {
            $return_res['info'] = "支付失败";
            return $return_res;
        }

        
        $payment_log_info = M("PaymentLog")->getById($log_id);
//        //开始查询金额
//		switch($payment_log_info['rec_module'])
//		{
//			case 'UserIncharge':
//				$field = 'money';
//				break;
//			case 'Order':
//				$field = 'order_total_price';
//				break;
//		}
//		$total_price = D($payment_log_info['rec_module'])->where("id=".$payment_log_info['rec_id'])->getField($field);
//		$total_price *= $currency_radio;
		$total_price = $total_fee / 100;

        /* 检查支付的金额是否相符 */
//        if ($total_price!=($total_fee / 100))
//        {
//            $return_res['info'] = "金额不对";
//            return $return_res;
//        }

        /* 检查数字签名是否正确 */
        $sign_text  = "cmdno=" . $cmd_no . "&pay_result=" . $pay_result .
                          "&date=" . $bill_date . "&transaction_id=" . $transaction_id .
                            "&sp_billno=" . $sp_billno . "&total_fee=" . $total_fee .
                            "&fee_type=" . $fee_type . "&attach=" . $attach .
                            "&key=" . $payment['config']['tencentpay_key'];
        $sign_md5 = strtoupper(md5($sign_text));
        if ($sign_md5 != $sign)
        {
            $return_res['info'] = "验证失败";
            return $return_res;
        }
        else
        {
        	
        	$payment_log_vo = D("PaymentLog")->getById($log_id);
        	$payment_id = $payment_log_vo['payment_id'];
        	$currency_id = $payment_log_vo['currency_id'];
  			
        	return order_paid($log_id, $total_price, $payment_id,$currency_id);
            /* 改变订单状态 */

        }
        
	}
}
?>