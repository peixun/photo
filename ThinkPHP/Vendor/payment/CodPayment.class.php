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
    $modules[$i]['code']    = 'Cod';

    /* 被整合的第三方程序的名称 */
    $modules[$i]['name']    = '货到付款';

    /* 被整合的第三方程序的版本 */
    $modules[$i]['version'] = '2.0';

    /* 插件的作者 */
    $modules[$i]['author']  = 'FANWE R&D TEAM';

    /* 支付方式：1：在线支付；0：线下支付 */
    $modules[$i]['online_pay'] = '0';
        
    /* 插件作者的官方网站 */
    $modules[$i]['website'] = 'http://www.fanwe.com';

    return;
}
// 货到付款模型
require_once(VENDOR_PATH.'payment/Payment.class.php');
class CodPayment extends Think implements Payment
{
	public $config = array(
	);
		
	public function getPaymentCode($payment_log_id, $money, $payment_id, $currency_id)
	{		
		$payment_info = D("Payment")->getById($payment_id);
		$payment_info['config'] = unserialize($payment_info['config']);
		$currency = M("Currency")->where("id=".$currency_id)->find();
		
		if(!empty($payment_info['logo']))
			$def_url .= "<input type='image' src='__ROOT__".$payment_info['logo']."' style='border:solid 1px #ccc;'><div class='blank'></div>";
			
		$def_url.="<br /><span class='red'>".L("PAY_TOTAL_PRICE").":".$currency['unit']." ".number_format($money,2)."</span>";
		return $def_url;
	}
	public function dealResult($get,$post,$request)
	{
		return L("INVALID_OPERATION");
	}
}
?>