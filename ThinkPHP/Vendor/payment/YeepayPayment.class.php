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
    $modules[$i]['code']    = 'Yeepay';

    /* 被整合的第三方程序的名称 */
    $modules[$i]['name']    = '易宝支付';

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

// 易宝农行在线支付模型
require_once(VENDOR_PATH.'payment/Payment.class.php');
class YeepayPayment extends Think implements Payment {
	public $config = array(
	    'yeepay_account'=>'',  //商户编号
        'yeepay_key'=>'',  	  //商户密钥
		
	);

		
	public function getPaymentCode($payment_log_id, $money, $payment_id, $currency_id)
	{
		$currency = D("Currency")->getById($currency_id);
		$payment_info = D("Payment")->getById($payment_id);
		$payment_info['config'] = unserialize($payment_info['config']);
		
		$payment_log = M("PaymentLog")->getById($payment_log_id);
		$data_sn = D($payment_log['rec_module'])->where("id=".$payment_log['rec_id'])->getField("sn");  //数据的单号
		
		$data_merchant_id =  trim($payment_info['config']['yeepay_account']);
        $data_order_id    = $payment_log_id;
        $data_amount      = $money;
        $message_type     = 'Buy';
        $data_cur         = 'CNY';
        $product_id       = '';
        $product_cat      = '';
        $product_desc     = '';
        $address_flag     = '0';

        C("URL_MODEL",3);
        $data_return_url    = 'http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/index.php?m=Payment&a=response&payment_name=Yeepay';
		
        $data_pay_key     = trim($payment_info['config']['yeepay_key']);
        $data_pay_account = trim($payment_info['config']['yeepay_account']);
        $mct_properties   = $payment_log_id;
        $def_url = $message_type . $data_merchant_id . $data_order_id . $data_amount . $data_cur . $product_id . $product_cat
                             . $product_desc . $data_return_url . $address_flag . $mct_properties ;
        $MD5KEY = $this->fw_hmac($def_url, $data_pay_key);

        $def_url  = "\n<form action='https://www.yeepay.com/app-merchant-proxy/node' method='post' target='_blank'>\n";
        $def_url .= "<input type='hidden' name='p0_Cmd' value='".$message_type."'>\n";
        $def_url .= "<input type='hidden' name='p1_MerId' value='".$data_merchant_id."'>\n";
        $def_url .= "<input type='hidden' name='p2_Order' value='".$data_order_id."'>\n";
        $def_url .= "<input type='hidden' name='p3_Amt' value='".$data_amount."'>\n";
        $def_url .= "<input type='hidden' name='p4_Cur' value='".$data_cur."'>\n";
        $def_url .= "<input type='hidden' name='p5_Pid' value='".$product_id."'>\n";
        $def_url .= "<input type='hidden' name='p6_Pcat' value='".$product_cat."'>\n";
        $def_url .= "<input type='hidden' name='p7_Pdesc' value='".$product_desc."'>\n";
        $def_url .= "<input type='hidden' name='p8_Url' value='".$data_return_url."'>\n";
        $def_url .= "<input type='hidden' name='p9_SAF' value='".$address_flag."'>\n";
        $def_url .= "<input type='hidden' name='pa_MP' value='".$mct_properties."'>\n";
        $def_url .= "<input type='hidden' name='pd_FrpId' value=''>\n";
        $def_url .= "<input type='hidden' name='pd_NeedResponse' value='1'>\n";
        $def_url .= "<input type='hidden' name='hmac' value='".$MD5KEY."'>\n";
		
		if(!empty($payment_info['logo']))
			$def_url .= "<input type='image' src='__ROOT__".$payment_info['logo']."' style='border:solid 1px #ccc;'><div class='blank'></div>";
			
        $def_url .= "<input type='submit' class='paybutton' value='" .L("YEEPAY_PAYMENT_BUTTON"). "'>";
		
        $def_url .= "</form>\n";
		
		$def_url.="<br /><span class='red'>".L("PAY_TOTAL_PRICE").":".$currency['unit']." ".number_format($money,2)."</span>";

        return $def_url;
        
	}
	
	private function fw_hmac($data, $key)
    {
        // RFC 2104 HMAC implementation for php.
        // Creates an md5 HMAC.
        // Eliminates the need to install mhash to compute a HMAC
        // Hacked by Lance Rushing(NOTE: Hacked means written)

//        $key  = iconv('GB2312', 'UTF8', $key);
//        $data = iconv('GB2312', 'UTF8', $data);

        $b = 64; // byte length for md5
        if (strlen($key) > $b)
        {
            $key = pack('H*', md5($key));
        }

        $key    = str_pad($key, $b, chr(0x00));
        $ipad   = str_pad('', $b, chr(0x36));
        $opad   = str_pad('', $b, chr(0x5c));
        $k_ipad = $key ^ $ipad ;
        $k_opad = $key ^ $opad;

        return md5($k_opad . pack('H*', md5($k_ipad . $data)));
    }
	
	public function dealResult($get,$post,$request)
	{	
		$payment   =  D("Payment")->where("class_name='Yeepay'")->find();   
		$payment['config'] = unserialize($payment['config']);		
        $merchant_id    = $payment['config']['yeepay_account'];       // 获取商户编号
        $merchant_key   = $payment['config']['yeepay_key'];           // 获取秘钥

        $message_type   = trim($request['r0_Cmd']);
        $succeed        = trim($request['r1_Code']);   // 获取交易结果,1成功,-1失败
        $trxId          = trim($request['r2_TrxId']);
        $amount         = trim($request['r3_Amt']);    // 获取订单金额
        $cur            = trim($request['r4_Cur']);    // 获取订单货币单位
        $product_id     = trim($request['r5_Pid']);    // 获取产品ID
        $orderid        = trim($request['r6_Order']);  // 获取订单ID
        $userId         = trim($request['r7_Uid']);    // 获取产品ID
        $merchant_param = trim($request['r8_MP']);     // 获取商户私有参数
        $bType          = trim($request['r9_BType']);  // 获取订单ID

        $mac            = trim($request['hmac']);      // 获取安全加密串

        ///生成加密串,注意顺序
        $ScrtStr  = $merchant_id . $message_type . $succeed . $trxId . $amount . $cur . $product_id .
                      $orderid . $userId . $merchant_param . $bType;
        $mymac    = $this->fw_hmac($ScrtStr, $merchant_key);

        $return_res = array(
			'info'=>'',
			'status'=>false,
		);

		$payment_log_id = $orderid;
    	$money = $amount;
    	$payment_id = $payment['id'];
    	$currency_id = $payment['currency']; 
    	
        if (strtoupper($mac) == strtoupper($mymac))
        {
            if ($succeed == '1')
            {
                //支付成功
                $return_res['status'] = true;
                return order_paid($payment_log_id,$money,$payment_id,$currency_id); 
            }
        }
        return $return_res;       
          
	}
}
?>