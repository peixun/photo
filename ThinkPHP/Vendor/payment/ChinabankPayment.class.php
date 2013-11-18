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
    $modules[$i]['code']    = 'Chinabank';

    /* 被整合的第三方程序的名称 */
    $modules[$i]['name']    = '网银在线';

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

// 网银支付模型
require_once(VENDOR_PATH.'payment/Payment.class.php');
class ChinabankPayment extends Think implements Payment {
	public $config = array(
	    'chinabank_account'=>'',  //商户编号
        'chinabank_key'=>'',  	  //MD5密钥
	);	
	
	public function getPaymentCode($payment_log_id, $money, $payment_id, $currency_id)
	{
		$currency = D("Currency")->getById($currency_id);
		$payment_info = D("Payment")->getById($payment_id);
		$payment_info['config'] = unserialize($payment_info['config']);
		
		$data_vid           = trim($payment_info['config']['chinabank_account']);
        $data_orderid       = $payment_log_id;
        $data_vamount       = $money;
        $data_vmoneytype    = 'CNY';
        $data_vpaykey       = trim($payment_info['config']['chinabank_key']);
        C("URL_MODEL",3);
        //modify by chenfq 2010-04-20 改用下名，否则端口丢失
        //$data_vreturnurl    = "http://".$_SERVER['SERVER_NAME'].U("Payment/response",array('payment_name'=>'Chinabank'));
		///$data_vreturnurl = 'http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/index.php?m=Payment&a=response&payment_name=Chinabank';
		$data_vreturnurl = 'http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/index.php?s=/Payment/response/payment_name/Chinabank/';
        $MD5KEY =$data_vamount.$data_vmoneytype.$data_orderid.$data_vid.$data_vreturnurl.$data_vpaykey;
        $MD5KEY = strtoupper(md5($MD5KEY));

        $def_url  = '<form style="text-align:center;" method=post action="https://pay3.chinabank.com.cn/PayGate" >';
        $def_url .= "<input type=HIDDEN name='v_mid' value='".$data_vid."'>";
        $def_url .= "<input type=HIDDEN name='v_oid' value='".$data_orderid."'>";
        $def_url .= "<input type=HIDDEN name='v_amount' value='".$data_vamount."'>";
        $def_url .= "<input type=HIDDEN name='v_moneytype'  value='".$data_vmoneytype."'>";
        $def_url .= "<input type=HIDDEN name='v_url'  value='".$data_vreturnurl."'>";
        $def_url .= "<input type=HIDDEN name='v_md5info' value='".$MD5KEY."'>";
        $def_url .= "<input type=HIDDEN name='remark1' value=''>";
		if(!empty($payment_info['logo']))
			$def_url .= "<input type='image' src='__ROOT__".$payment_info['logo']."' style='border:solid 1px #ccc;'><div class='blank'></div>";
			
        $def_url .= "<input type='submit' class='paybutton' value='前往网上银行支付'>";
        $def_url .= "</form>";
        $def_url.="<br /><span class='red'>".L("PAY_TOTAL_PRICE").":".$currency['unit']." ".number_format($money,2)."</span>";
        return $def_url;       
	}
	
	public function dealResult($get,$post,$request)
	{			
		$return_res = array(
			'info'=>'',
			'status'=>false,
		);
		$payment   =  D("Payment")->where("class_name='Chinabank'")->find();   
		$currency_radio = D("Currency")->where("id=".$payment['currency'])->getField("radio");
    	$payment['config'] = unserialize($payment['config']);
    	
		$v_oid          = trim($post['v_oid']);
        $v_pmode        = trim($post['v_pmode']);
        $v_pstatus      = trim($post['v_pstatus']);
        $v_pstring      = trim($post['v_pstring']);
        $v_amount       = trim($post['v_amount']);
        $v_moneytype    = trim($post['v_moneytype']);
        $remark1        = trim($post['remark1' ]);
        $remark2        = trim($post['remark2' ]);
        $v_md5str       = trim($post['v_md5str' ]);

        /**
         * 重新计算md5的值
         */
        $key            = $payment['config']['chinabank_key'];
        $md5string=strtoupper(md5($v_oid.$v_pstatus.$v_amount.$v_moneytype.$key));
		
        //开始初始化参数
        $payment_log_id = $v_oid;
    	$money = $v_amount;
    	$payment_id = $payment['id'];
    	$currency_id = $payment['currency'];    
        
		/* 检查秘钥是否正确 */
	        if ($v_md5str==$md5string)
	        {
	            if ($v_pstatus == '20')
	            {
	                return order_paid($payment_log_id,$money,$payment_id,$currency_id);   
	            }
	        }
	        else
	        {
	            $return_res['info'] = L("VALID_ERROR");
	            return $return_res; 
	        }
               
	}
}
?>