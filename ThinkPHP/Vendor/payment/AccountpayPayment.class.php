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
    $modules[$i]['code']    = 'Accountpay';

    /* 被整合的第三方程序的名称 */
    $modules[$i]['name']    = '余额支付';

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

// 余额支付模型
require_once(VENDOR_PATH.'payment/Payment.class.php');
class AccountpayPayment extends Think implements Payment {
	public $config = array(

	);
		
	public function getPaymentCode($payment_log_id, $money, $payment_id, $currency_id)
	{
		$result = order_paid($payment_log_id,$money,$payment_id,$currency_id);
		$def_url = $result['info'];
		return $def_url;
	}
	
	public function dealResult($get,$post,$request)
	{
		return L("INVALID_OPERATION");
	}
}
?>