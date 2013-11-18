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
    $modules[$i]['code']    = 'Alipay';

    /* 被整合的第三方程序的名称 */
    $modules[$i]['name']    = '支付宝';

    /* 被整合的第三方程序的版本 */
    $modules[$i]['version'] = '2.0';

    /* 插件的作者 */
    $modules[$i]['author']  = 'FANWE R&D TEAM';

    /* 支付方式：1：在线支付；0：线下支付 */
    $modules[$i]['online_pay'] = '1';
        
    /* 插件作者的官方网站 */
    $modules[$i]['website'] = 'http://www.eyoo.cn';

    return;
}
// 支付宝模型
require_once(VENDOR_PATH.'payment/Payment.class.php');
class AlipayPayment extends Think implements Payment  {
	public $config = array(
	    'alipay_partner'=>'',  //合作者身份ID
        'alipay_service'=>'',  //接口方式
		'alipay_account'=>'',  //支付宝帐号
		'alipay_key'	=>'',  //校验码
	);	
	public function getPaymentCode($payment_log_id, $money, $payment_id, $currency_id)
	{
        //Dir::delDir(getcwd()."/home/Runtime/");
        //Dir::delDir(getcwd()."/home/Runtime/");
        //Dir::delDir(getcwd()."/home/Runtime/");
        //@mkdir(getcwd()."/home/Runtime/",0777);		
		
		//$payment_log_id = 298;
		
		$money = round($money,2);
		$currency = D("Currency")->getById($currency_id);
		$payment_info = D("Payment")->getById($payment_id);
		$payment_info['config'] = unserialize($payment_info['config']);
		$agent = 'C4335319945672464113';
		//dump($payment_info);
		//C("URL_MODEL",0);
		//$data_return_url = "http://".$_SERVER['SERVER_NAME'].U("Payment/response",array('payment_name'=>'Alipay'));
		//$data_notify_url = "http://".$_SERVER['SERVER_NAME'].U("Payment/response",array('payment_name'=>'Alipay'));
		$data_return_url = 'http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/index.php?m=Payment&a=response&payment_name=Alipay';
		$data_notify_url = $data_return_url;

		$real_method = $payment_info['config']['alipay_service'];

        switch ($real_method){
            case '0':
                $service = 'trade_create_by_buyer';
                break;
            case '1':
                $service = 'create_partner_trade_by_buyer';
                break;
            case '2':
                $service = 'create_direct_pay_by_user';
                break;
        }	
        
		$payment_log = M("PaymentLog")->getById($payment_log_id);
		//$data_sn = D($payment_log['rec_module'])->where("id=".$payment_log['rec_id'])->getField("sn");  //数据的单号
        
		
		if($payment_log['rec_module']=='Order'){
			$orderGoods = M('OrderGoods')->where("order_id=".$payment_log['rec_id'])->find();
			$goods_data = M("Goods")->getById($orderGoods['rec_id']);			
			$data_sn = $goods_data['goods_short_name']==''?$goods_data['name_1']:$goods_data['goods_short_name'];
			//$quantity = intval(M('OrderGoods')->where("order_id=".$payment_log['rec_id'])->sum('number'));
		}else{
			//$quantity = 1;
			$data_sn = D($payment_log['rec_module'])->where("id=".$payment_log['rec_id'])->getField("sn");
		}
        $parameter = array(
            'agent'             => $agent,
            'service'           => $service,
            'partner'           => $payment_info['config']['alipay_partner'],
            //'partner'           => ALIPAY_ID,
            '_input_charset'    => 'utf-8',
            'notify_url'        => $data_notify_url,
            'return_url'        => $data_return_url,
            /* 业务参数 */
            'subject'           => $data_sn,
            'out_trade_no'      => 'hx123456'.$payment_log_id, //modify by chenfq 2010-05-17 将$data_sn.$payment_log_id改为：'fw123456'.$payment_log_id
            'price'             => $money,
            'quantity'          => 1,
            'payment_type'      => 1,
            /* 物流参数 */
            'logistics_type'    => 'EXPRESS',
            'logistics_fee'     => 0,
            'logistics_payment' => 'BUYER_PAY_AFTER_RECEIVE',
            /* 买卖双方信息 */
            'seller_email'      => $payment_info['config']['alipay_account']
        );
        
        ksort($parameter);
        reset($parameter);

        $param = '';
        $sign  = '';

        foreach ($parameter AS $key => $val)
        {
        	$param .= "$key=" .urlencode($val). "&";
            $sign  .= "$key=$val&";
        }

        $param = substr($param, 0, -1);
        $sign  = substr($sign, 0, -1). $payment_info['config']['alipay_key'];
        $sign_md5 = md5($sign);
    	//Log::record("getPaymentCode;param:".$param.";sign:".$sign.";md5(sign):".$sign_md5);    
    	//Log::save();
    	
    	/*
    	dump($param);
    	dump($sign);
    	dump($sign_md5);
    	
    	$sign_tmp = "_input_charset=utf-8&agent=C4335319945672464113&logistics_fee=0&logistics_payment=BUYER_PAY_AFTER_RECEIVE&logistics_type=EXPRESS&notify_url=http://w147392.s50.mydiscuz.com/index.php?m=Payment&a=response&payment_name=Alipay&out_trade_no=299&partner=2088002091282930&payment_type=1&price=0.9&quantity=1&return_url=http://w147392.s50.mydiscuz.com/index.php?m=Payment&a=response&payment_name=Alipay&seller_email=hf20@163.com&service=create_partner_trade_by_buyer&subject=100404010815WF585y7azalk3e382sf1r0263f8kidtem3";
    	dump(md5($sign_tmp));
    	*/
		
		$payLinks = '<a onclick="window.open(\'https://www.alipay.com/cooperate/gateway.do?'.$param. '&sign='.$sign_md5.'&sign_type=MD5\')" href="javascript:;"><input type="submit" class="paybutton" name="buy" value="前往支付宝在线支付"/></a>';
		
    	if(!empty($payment_info['logo']))
		{
			$payLinks = '<a href="https://www.alipay.com/cooperate/gateway.do?'.$param. '&sign='.$sign_md5.'&sign_type=MD5" class="payLink"><img src="__ROOT__'.$payment_info['logo'].'" style="border:solid 1px #ccc;" /></a><div class="blank"></div>'.$payLinks;
		}
		
        $def_url = '<div style="text-align:center">'.$payLinks.'</div>';
		$def_url.="<br /><span class='red'>".L("PAY_TOTAL_PRICE").":".$currency['unit']." ".number_format($money,2)."</span>";
        return $def_url;
	}
	
	public function dealResult($get,$post,$request)
	{	
		if (!empty($post))
        {
            foreach($post as $key => $data)
            {
                $get[$key] = $data;
            }
        }
        
		$return_res = array(
			'info'=>'',
			'status'=>false,
		);
		$payment   =  D("Payment")->where("class_name='Alipay'")->find();   
		$currency_radio = D("Currency")->where("id=".$payment['currency'])->getField("radio");
    	$payment['config'] = unserialize($payment['config']);
    	$seller_email = rawurldecode($get['seller_email']);
    	
    	
    	
        /* 检查数字签名是否正确 */
        ksort($get);
        reset($get);

	
        foreach ($get AS $key=>$val)
        {
            if ($key != 'sign' && $key != 'sign_type' && $key != 'code' && $key!='payment_name')
            {
                $sign .= "$key=$val&";
            }
        }

        $sign = substr($sign, 0, -1) . $payment['config']['alipay_key'];
		if (md5($sign) != $get['sign'])
        {
            $return_res['info'] = L("VALID_ERROR");
            return $return_res; 
        }
		
        //初始化处理订单函数的参数.//modify by chenfq 2010-05-17 $get['subject']===>'fw123456'
        //$payment_log_id = intval(str_replace($get['subject'], '', $get['out_trade_no']));
        $payment_log_id = intval(str_replace('fw123456', '', $get['out_trade_no']));
        
    	//$payment_log_id = $get['out_trade_no'];
    	$money = $get['total_fee'];
    	$payment_id = $payment['id'];
    	$currency_id = $payment['currency']; 
    	//Log::record("dealResult:".$get['out_trade_no'].";".$get['trade_status']);    
    	//Log::save();
    	/*   
    	dump($get['out_trade_no']);
    	dump($get['subject']);
    	dump(str_replace($get['subject'], '', $get['out_trade_no']));
    	dump($payment_log_id);
    	*/
		if ($get['trade_status'] == 'TRADE_SUCCESS' || $get['trade_status'] == 'TRADE_FINISHED' || $get['trade_status'] == 'WAIT_SELLER_SEND_GOODS'){
		   return order_paid($payment_log_id,$money,$payment_id,$currency_id);
		}else{
		   return false;
		}    	
	}
}
?>