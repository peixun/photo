<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */

interface Payment{
	
	
	/**
	 * 获取支付代码或提示信息
	 * @param integer $payment_log_id  支付日志ID
	 * @param float $money  实际支付给接口的金额，如$600就直接传入600，而不是原始的 ￥1000 
	 * @param integer $payment_id   支付方式ID
	 * @param integer $currency_id  支付货币ID
	 */
	function getPaymentCode($payment_log_id, $money, $payment_id, $currency_id);
	
	//响应在线支付
	function dealResult($get,$post,$request);
}
?>