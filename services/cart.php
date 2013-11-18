<?php
//购物处理程序
@session_start();
require_once('services_init.php');

//获取所有子集的类
class ChildIds
{
	public function __construct($tb_name)
	{
		$this->tb_name = $tb_name;	
	}
	private $tb_name;
	private $childIds;
	private function _getChildIds($pid = '0', $pk_str='id' , $pid_str ='pid')
	{
//		$childItem_arr = $this->field('id')->where($pid_str."=".$pid)->findAll();
		$childItem_arr = $GLOBALS['db']->getAll("select id from ".DB_PREFIX.$this->tb_name." where ".$pid_str."=".$pid);
		if($childItem_arr)
		{
			foreach($childItem_arr as $childItem)
			{
				$this->childIds[] = $childItem[$pk_str];
				$this->_getChildIds($childItem[$pk_str],$pk_str,$pid_str);
			}
		}
	}
	public function getChildIds($pid = '0', $pk_str='id' , $pid_str ='pid')
	{
		$this->childIds = array();
		$this->_getChildIds($pid,$pk_str,$pid_str);
		return $this->childIds;
	}
}	
	
function dotran($str) {
        $str = str_replace("\r\n",'',$str);
        $str = str_replace("\t",'',$str);
        $str = str_replace("\b",'',$str);
        return $str;
}
function format_price($price)
{
	return conf("BASE_CURRENCY_UNIT").round($price,2);
	
}
function countCartTotal($payment_id=0,$delivery_id=0,$is_protect=0,$delivery_region=array(),$tax,$credit,$isCreditAll,$ecvSn,$ecvPassword)
{
		$delivery_free = 0;
		$delivery_fee = 0;
		
		$session_id = session_id();
		//$cart_item = D("Cart")->where("session_id = '$session_id'")->find();
		$cart_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."cart where session_id='".$session_id."' limit 1");
		$ecvFee = 0;
		$ecvID = 0;
		
		if(!empty($ecvSn))
		{
//			$condition['sn'] = $ecvSn;
//			$condition['password'] = $ecvPassword;
//			$ecv = D("Ecv")->get($condition);
			$ecv = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."ecv where sn='".$ecvSn."' and password = '".$ecvPassword."' limit 1");
			$ecv['ecvType'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."ecv_type where id=".intval($ecv['ecv_type'])." limit 1");
			$ecv['useUser'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id=".intval($ecv['use_user_id'])." limit 1");
			if($ecv)
			{
				$time = get_gmt_time();
			
				if(intval($ecv['use_date_time']) == 0 && intval($ecv['ecvType']['status']) == 1 && $time > intval($ecv['ecvType']['use_start_date']) && ($time < intval($ecv['ecvType']['use_end_date']) ||  intval($ecv['ecvType']['use_end_date']) == 0))
				{
					$ecvFee = round(floatval($ecv['ecvType']['money']),2);					
					$ecvID  = $ecv['id'];
				}
			}
		}
		
		if(floatval($cart_item['data_total_price']) > 0){
			//$discount = D("UserGroup")->where("id = ".$_SESSION['group_id'])->getField('discount');
			$discocunt = $GLOBALS['db']->getOne("select discount from ".DB_PREFIX."user_group where id=".$_SESSION['group_id']);
			$discount = floatval($discount) > 0 ? floatval($discount) : 1;			
		}else{
			$discount = 1;
		}

		//$payment_info = S("CACHE_PAYMENT_".$payment_id);
		$payment_info = $GLOBALS['cache']->get("CACHE_PAYMENT_".$payment_id);
		if($payment_info===false)
		{
	    	//$payment_info = D("Payment")->getById($payment_id);
	    	$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id =".$payment_id);
	    	$GLOBALS['cache']->set("CACHE_PAYMENT_".$payment_id,$payment_info);
		}
	    //$payment_currency = D("Currency")->getById($payment_info['currency']); //支付方式的货币
		$payment_currency = array('unit'=>conf("BASE_CURRENCY_UNIT"),'radio'=>1);
		
		$temp_total_price = round(floatval($cart_item['data_total_price']) * $discount,2);
		
		$discount_price = round(floatval($cart_item['data_total_price']) - $temp_total_price,2);
		
		//修改by hc 免运费的价格判断
		if($cart_item['is_inquiry']==1)
		{
			//$goods_data = M("Goods")->getById($cart_item['rec_id']);
			$goods_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."goods where id =".$cart_item['rec_id']);
			if(floatval($cart_item['data_total_price'])<$goods_data['free_delivery_amount'])
			{
				$cart_item['is_inquiry'] = 0;
			}
		}
		

		$fee = countFee(floatval($cart_item['data_total_price']),$payment_id,$cart_item['data_total_weight'],$delivery_id,$is_protect,$delivery_region,$tax,$delivery_fee,$cart_item['goods_type'],$cart_item['is_inquiry'],$isCreditAll,$credit,$ecvFee);
		
		$total_price = round($temp_total_price+$fee['delivery_fee']+$fee['payment_fee']+$fee['tax_money']+$fee['protect_fee'], 2);
		$user_info = $GLOBALS['cache']->get("CACHE_USER_INFO_".intval($_SESSION['user_id']));
		if($user_info===false)
		{
//			$user_info = M("User")->getById(intval($_SESSION['user_id']));
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($_SESSION['user_id']));
			$GLOBALS['cache']->set("CACHE_USER_INFO_".intval($_SESSION['user_id']),$user_info);
		}
		$userMoney = floatval($user_info['money']);
		if($total_price > 0 && $ecvFee > $total_price)
			$ecvFee = $total_price;
			
		$total_price = $total_price - $ecvFee;
		
		if ($total_price > 0){
			if($credit > $total_price || $isCreditAll == 1)
				$credit = $total_price;
				
			if($credit > $userMoney)
				$credit = $userMoney;
				
			$total_price = $total_price - $credit;			
		}else{
			$credit = 0;
		}
		
	    return array(
			'goods_total_price' => $cart_item['data_total_price'],
			'goods_total_price_format' => format_price($cart_item['data_total_price']),	
			'delivery_fee' =>	$fee['delivery_fee'],
			'delivery_fee_format' => format_price($fee['delivery_fee']),
			'protect_fee'	=> $fee['protect_fee'],
			'protect_fee_format' => format_price($fee['protect_fee']),
			'payment_fee' =>	$fee['payment_fee'],
			'payment_fee_format' => format_price($fee['payment_fee']),
			'total_price' =>	$total_price,
			'total_price_format' =>	format_price($total_price),
			'payment_name' =>	$payment_info['name_1'],
			'payment_total_price_format'	=>	$payment_currency['unit']." ".number_format(round($total_price*$payment_currency['radio'],2),2),
			'promote_card' =>	$promote_card,
			//税款
			'tax' => $tax,
			'tax_money' =>	$fee['tax_money'],
			'tax_money_format' => format_price($fee['tax_money']),
			'delivery_free' => $delivery_free,
			//积分
	    	'total_add_score' => $cart_item['data_total_score'],
	    	'total_add_score_format' => $cart_item['data_total_score'],
			//总重
			'total_weight' => $cart_item['data_total_weight'],
			'credit' => $credit,
			'credit_format' =>	format_price($credit),
			'all_fee' => $total_price + $credit + $ecvFee + $discount_price,
			'all_fee_format' => format_price($total_price + $credit + $ecvFee + $discount_price),
			'is_inquiry' => $cart_item['is_inquiry'],
			'goods_type' => $cart_item['goods_type'],
			'discount_price' => $discount_price,
			'discount_price_format' => format_price($discount_price),
			'ecvFee' => $ecvFee,
			'ecvFee_format' => format_price($ecvFee),
			'ecvID' => $ecvID
		);
} 

//计算订单所有总额（包含促销计算，运费与支付手续费）
function countOrderTotal($id,$payment_id=0,$delivery_id=0,$is_protect=0,$delivery_region=array(),$tax,$credit,$isCreditAll,$ecvSn,$ecvPassword)
	{
		$delivery_free = 0;
		$delivery_fee = 0;
		
//		$order = D("Order")->getById($id);
		$order = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."order where id = ".$id);
		$order_goods = $GLOBALS['cache']->get("CACHE_ORDER_GOODS_".$id);
		if($order_goods===false)
		{
//			$order_goods = D("OrderGoods")->where("order_id = '$id'")->find();
			$order_goods = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."order_goods where order_id = ".$id." limit 1");
			$GLOBALS['cache']->set("CACHE_ORDER_GOODS_".$id,$order_goods);
		}
		//$order_goods = D("OrderGoods")->where("order_id = '$id'")->find();
		
		$ecvFee = 0;
		$ecvID = $order['ecv_id'];

		if(!empty($ecvSn))
		{
//			$condition['sn'] = $ecvSn;
//			$condition['password'] = $ecvPassword;
//			$ecv = D("Ecv")->get($condition);
			$ecv = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."ecv where sn ='".$ecvSn."' and password = '".$ecvPassword."' limit 1");
			$ecv['ecvType'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."ecv_type where id=".intval($ecv['ecv_type'])." limit 1");
			$ecv['useUser'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id=".intval($ecv['use_user_id'])." limit 1");
			if($ecv)
			{
				$time = get_gmt_time();
			
				if(intval($ecv['use_date_time']) == 0 && intval($ecv['ecvType']['status']) == 1 && $time > intval($ecv['ecvType']['use_start_date']) && ($time < intval($ecv['ecvType']['use_end_date']) ||  intval($ecv['ecvType']['use_end_date']) == 0))
				{
					$ecvFee = round(floatval($ecv['ecvType']['money']),2);
					$ecvID  = $ecv['id'];
				}
			}
		}
		elseif($ecvID > 0)
		{
//			$condition['id'] = $ecvID;
//			$ecv = D("Ecv")->get($condition);
			$ecv = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."ecv where id = ".$ecvID);
			
			if($ecv)
			{
				$ecvFee = round(floatval($ecv['ecvType']['money']),2);
				$ecvID  = $ecv['id'];
			}
		}
		
		if(floatval($order_goods['data_total_price']) > 0){
//			$discount = D("UserGroup")->where("id = ".$_SESSION['group_id'])->getField('discount');
			$discount = $GLOBALS['db']->getOne("select discount from ".DB_PREFIX."user_group where id = ".intval($_SESSION['group_id']));
			$discount = floatval($discount) > 0 ? floatval($discount) : 1;			
		}else{
			$discount = 1;
		}
		
//	    $payment_info = D("Payment")->getById($payment_id);
		$payment_info = $GLOBALS['db']->getRow("Select * from ".DB_PREFIX."payment where id = ".$payment_id);
//	    $payment_currency = D("Currency")->getById($payment_info['currency']); //支付方式的货币
		$payment_currency = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."currency where id =".intval($payment_info['currency']));
		
		$temp_total_price = round(floatval($order_goods['data_total_price']) * $discount,2);
		
		$discount_price = round(floatval($order_goods['data_total_price']) - $temp_total_price,2);
		
		$goods_type=0;
		
		if($order['delivery'] > 0 || $order['delivery_refer_order_id'] > 0)
			$goods_type = 1;
			
		if($order['offline'] == 1)
			$goods_type = 2;
		
		$order_incharge = $order["order_incharge"] - $order["ecv_money"];
		
		//修改by hc 免运费的价格判断
		if($order_goods['is_inquiry']==1)
		{
//			$goods_data = M("Goods")->getById($order_goods['rec_id']);
			$goods_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."goods where id =".$order_goods['rec_id']);
			if(floatval($order_goods['data_total_price'])<$goods_data['free_delivery_amount'])
			{
				$order_goods['is_inquiry'] = 0;
			}
		}
		
		$fee = countFee(floatval($order_goods['data_total_price']),$payment_id,$order['order_weight'],$delivery_id,$is_protect,$delivery_region,$tax,$delivery_fee,$goods_type,$order_goods['is_inquiry'],$isCreditAll,$credit,$ecvFee);
		
		$total_price = round($temp_total_price+$fee['delivery_fee']+$fee['payment_fee']+$fee['tax_money']+$fee['protect_fee'], 2);
//		$userMoney = floatval(D("User")->where("id=".intval($_SESSION['user_id']))->getField("money"));
		$userMoney = floatval($GLOBALS['db']->getOne("select money from ".DB_PREFIX."user where id = ".intval($_SESSION['user_id'])));
		
		if($total_price > 0 && $ecvFee > $total_price)
			$ecvFee = $total_price;
			
		$total_price = $total_price - $ecvFee - $order_incharge;
		
		if ($total_price > 0)
		{
			if($credit > $total_price || $isCreditAll == 1)
				$credit = $total_price;
				
			if($credit > $userMoney)
				$credit = $userMoney;
				
			$total_price = $total_price - $credit;			
		}
		else
		{
			$credit = 0;
		}
		
	    return array(
			'goods_total_price' => $order_goods['data_total_price'],
			'goods_total_price_format' => format_price($order_goods['data_total_price']),	
			'delivery_fee' =>	$fee['delivery_fee'],
			'delivery_fee_format' => format_price($fee['delivery_fee']),
			'protect_fee'	=> $fee['protect_fee'],
			'protect_fee_format' => format_price($fee['protect_fee']),
			'payment_fee' =>	$fee['payment_fee'],
			'payment_fee_format' => format_price($fee['payment_fee']),
			'total_price' =>	$total_price ,
			'total_price_format' =>	format_price($total_price),
			'payment_name' =>	$payment_info['name_1'],
			'payment_total_price_format'	=>	$payment_currency['unit']." ".number_format(round($total_price*$payment_currency['radio'],2),2),
			'promote_card' =>	$promote_card,
			//税款
			'tax' => $tax,
			'tax_money' =>	$fee['tax_money'],
			'tax_money_format' => format_price($fee['tax_money']),
			'delivery_free' => $delivery_free,
			//积分
	    	'total_add_score' => $order_goods['data_total_score'],
	    	'total_add_score_format' => $order_goods['data_total_score'],
			//总重
			'total_weight' => $order['order_weight'],
			'credit' => $credit,
			'credit_format' =>	format_price($credit),
			'all_fee' => $total_price + $credit + $ecvFee + $discount_price + $order_incharge,
			'all_fee_format' => format_price($total_price + $credit + $ecvFee + $discount_price + $order_incharge),
			'is_inquiry' => $order_goods['is_inquiry'],
			'goods_type' => $goods_type,
			'discount_price' => $discount_price,
			'discount_price_format' => format_price($discount_price),
			'ecvFee' => $ecvFee,
			'ecvFee_format' => format_price($ecvFee),
			'ecvID' => $ecvID,
			'incharge' => $order_incharge,
			'incharge_format' => format_price($order_incharge)
		);
	}
/**
	 * 计算相应的运费与手续费
	 *
	 * @param $total_price  总价
	 * @param $total_weight 总重
	 * @param $delivery_id  配送方式
	 * @param $payment_id   支付方式
	 * @param $is_protect   是否保价
	 * @param $delivery_region   配送地区 array('region_lv1'=>'','region_lv2'=>'','region_lv3'=>'','region_lv4'=>'')
	 * @param $tax 是否开票
	 * 
	 * 返回：支付手续费，运费,保价费, 税款 array('payment_fee'=>'','delivery_fee'=>'','protect_fee'=>'','tax_money'=>'')
	 */
	function countFee($total_price,$payment_id,$total_weight,$delivery_id,$is_protect,$delivery_region,$tax,$count_delivery_fee,$goods_type,$is_inquiry,$isCreditAll,$credit,$ecvFee)
	{
		//计算运费
		$order_delivery_region = 0;
		$delivery_fee = 0;  //用于返回的运费
		$protect_fee = 0;  //用于返回的保价费
		$tax_money = 0;
		$payment_fee = 0;
		
		//if ($total_price <= 0) //add by chenfq 2010-05-12
		//	return array('payment_fee'=>10,'delivery_fee'=>20,'protect_fee'=>30,'tax_money'=>40);
		
		if($goods_type == 1 && $is_inquiry == 0 && $total_price < conf("FREE_DELIVERY_LIMIT"))
		{
			if($delivery_region['region_lv4']>0)
			{
				$order_delivery_region = $delivery_region['region_lv4'];
			}
			elseif($delivery_region['region_lv3']>0)
			{
				$order_delivery_region = $delivery_region['region_lv3'];
			}
			elseif($delivery_region['region_lv2']>0)
			{
				$order_delivery_region = $delivery_region['region_lv2'];
			}
			elseif($delivery_region['region_lv1']>0)
			{
				$order_delivery_region = $delivery_region['region_lv1'];
			}
			//至此查询出当前订定所配送的地址ID

			$delivery_info = $GLOBALS['cache']->get("DELIVERY_INFO_".$delivery_id);
			if($delivery_info===false)
			{
//				$delivery_info = D("Delivery")->getById($delivery_id);
				$delivery_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."delivery where id = ".$delivery_id);
				$GLOBALS['cache']->set("DELIVERY_INFO_".$delivery_id,$delivery_info);
			}

			if($delivery_info)
			{
				$delivery_regions = $GLOBALS['cache']->get("CACHE_DELIVERY_REGIONS_".$delivery_id);
				if($delivery_regions===false)
				{
//					$delivery_regions = D("DeliveryRegion")->where("delivery_id=".$delivery_id)->findAll();
					$delivery_regions = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."delivery_region where delivery_id = ".$delivery_id);
					$GLOBALS['cache']->set("CACHE_DELIVERY_REGIONS_".$delivery_id,$delivery_regions);
				}
							
				if($delivery_regions&&$order_delivery_region>0)
				{				
					//存在当前指定的配送地址
					$region_conf_child_ids = new ChildIds("region_conf");
					foreach($delivery_regions as $k=>$v)
					{
						$region_ids = explode(",",$v['region_ids']);
						$tmp_arr = array();
						foreach($region_ids as $vv)
						{
							$arr = $region_conf_child_ids->getChildIds($vv);
							if($arr != 0)
							$tmp_arr = array_merge($tmp_arr,$arr);
						}
						$region_ids = array_merge($tmp_arr,$region_ids);
						
						if(in_array($order_delivery_region,$region_ids))
						{
							//查询出存在的计算地区
							$region_info = $v;	
															
							if($total_weight>$delivery_info['first_weight'])
							{
								//超过首重
								$delivery_fee += $region_info['first_price'];
								//计算续重
								$delivery_fee += ceil(($total_weight-$delivery_info['first_weight'])/$delivery_info['continue_weight'])*$region_info['continue_price'];
							}
							else 
							{
								$delivery_fee += $region_info['first_price'];
									
							}
							break;
						}
					}
					if(!$region_info)
					{
						//未查询出相应的计算地区判断时否按默认
						if($delivery_info['allow_default']==1)
						{
							//使用默认
							if($total_weight>$delivery_info['first_weight'])
							{
								//超过首重
								$delivery_fee += $delivery_info['first_price'];
								//计算续重
								$delivery_fee += ceil(($total_weight-$delivery_info['first_weight'])/$delivery_info['continue_weight'])*$delivery_info['continue_price'];
							}
							else 
							{
								$delivery_fee += $delivery_info['first_price'];
							}
						}
					}
					
				}
				else 
				{
					//无指定地区时按当前配送方式的默认值计算
					if($delivery_info['allow_default']==1)
						{
							//使用默认
							if($total_weight>$delivery_info['first_weight'])
							{
								//超过首重
								$delivery_fee += $delivery_info['first_price'];
								//计算续重
								$delivery_fee += ceil(($total_weight-$delivery_info['first_weight'])/$delivery_info['continue_weight'])*$delivery_info['continue_price'];
							}
							else 
							{
								$delivery_fee += $delivery_info['first_price'];
							}
						}
				}
				
				if($is_protect==1)
				{
					if($total_price*$delivery_info['protect_radio']/100>$delivery_info['protect_price'])
					{					
						//超过保价底价
						$protect_fee = $total_price*$delivery_info['protect_radio']/100;					
					}
					else 
					{					
						$protect_fee = $delivery_info['protect_price'];
					}
				}
			}
		}
		
		//税率
		if($tax==1)
		{
			$tax_money = $total_price*conf("TAX_RADIO");
		}
		
		$payment_total_price = $total_price + $delivery_fee + $tax_money + $protect_fee + $count_delivery_fee;
//		$user_info = S("CACHE_USER_INFO_".intval($_SESSION['user_id']));
		$user_info = $GLOBALS['cache']->get("CACHE_USER_INFO_".intval($_SESSION['user_id']));
		if($user_info===false)
		{
//			$user_info = M("User")->getById(intval($_SESSION['user_id']));
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id =".intval($_SESSION['user_id']));
			$GLOBALS['cache']->set("CACHE_USER_INFO_".intval($_SESSION['user_id']),$user_info);
		}
		$userMoney = floatval($user_info['money']);
		
		$pay_free_fee = 0;
		
		if($ecvFee > $pay_free_fee)
			$ecvFee = $pay_free_fee;
			
		$pay_free_fee = $payment_total_price - $ecvFee;
		
		if($isCreditAll == 1 || $credit > $pay_free_fee)
			$credit = $pay_free_fee;
		
		if($credit > $userMoney)
			$credit = $userMoney;
			
		$pay_free_fee = $pay_free_fee - $credit;
		
		if($pay_free_fee > 0)
		{
			//$payment_info = D("Payment")->getById($payment_id); //支付方式
			$payment_info = $GLOBALS['cache']->get("CACHE_PAYMENT_".$payment_id);
			if($payment_info===false)
			{
//		    	$payment_info = D("Payment")->getById($payment_id);
				$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id =".$payment_id);
		    	$GLOBALS['cache']->set("CACHE_PAYMENT_".$payment_id,$payment_info);
			}
			if($payment_info)
			{
				//$payment_currency = D("Currency")->getById($payment_info['currency']); //支付方式的货币
				//$payment_currency = array('unit'=>eyooC("BASE_CURRENCY_UNIT"),'radio'=>1);
				$payment_currency = array('id'=>1,'unit'=>conf("BASE_CURRENCY_UNIT"),'radio'=>1);
				if($payment_info['fee_type']==0)
				{
					//定额
					$payment_fee = $payment_info['fee'];
				}
				else
				{
					$payment_fee = ($payment_total_price - $credit) * $payment_info['fee'] / 100;
				}
			}
		}
		
		return array('payment_fee'=>$payment_fee,'delivery_fee'=>$delivery_fee,'protect_fee'=>$protect_fee,'tax_money'=>$tax_money);
	}
//处理购物车统计
if($_REQUEST['m']=='Cart'&&$_REQUEST['a']=='getCartTotal')
{
	   	$payment_id = intval($_REQUEST['payment_id']);
   		$delivery_id = intval($_REQUEST['delivery_id']);
   		$is_protect = intval($_REQUEST['is_protect']);
   		$delivery_region = array(
   			'region_lv1'=>intval($_REQUEST['region_lv1']),
   			'region_lv2'=>intval($_REQUEST['region_lv2']),
   			'region_lv3'=>intval($_REQUEST['region_lv3']),
   			'region_lv4'=>intval($_REQUEST['region_lv4'])
   		);
   		$tax = intval($_REQUEST['tax']);
		$credit = floatval($_REQUEST['credit']);
		$isCreditAll = intval($_REQUEST['isCreditAll']);
		$ecvSn = trim($_REQUEST['ecvSn']);
		$ecvPassword = trim($_REQUEST['ecvPassword']);
   		
   		$cart_total = countCartTotal($payment_id,$delivery_id,$is_protect,$delivery_region,$tax,$credit,$isCreditAll,$ecvSn,$ecvPassword);
   		$GLOBALS['tpl']->assign("cart_total",$cart_total);
   		$GLOBALS['tpl']->assign("lang",$GLOBALS['Ln']);
   		$cart_total['html'] = dotran($GLOBALS['tpl']->fetch("Inc/goods/cart_total.tpl"));
   		//$cart_total['html'] = 'abcde';
   		header("Content-Type:text/html; charset=utf-8");
		echo json_encode($cart_total);
}

//处理订单购物统计
if($_REQUEST['m']=='Order'&&$_REQUEST['a']=='getOrderTotal')
{

		$payment_id = intval($_REQUEST['payment_id']);
   		$delivery_id = intval($_REQUEST['delivery_id']);
   		$is_protect = intval($_REQUEST['is_protect']);
   		$delivery_region = array(
   			'region_lv1'=>intval($_REQUEST['region_lv1']),
   			'region_lv2'=>intval($_REQUEST['region_lv2']),
   			'region_lv3'=>intval($_REQUEST['region_lv3']),
   			'region_lv4'=>intval($_REQUEST['region_lv4'])
   		);
   		$tax = intval($_REQUEST['tax']);
		$credit = floatval($_REQUEST['credit']);
		$isCreditAll = intval($_REQUEST['isCreditAll']);
		$ecvSn = trim($_REQUEST['ecvSn']);
		$ecvPassword = trim($_REQUEST['ecvPassword']);
		$order_id = intval($_REQUEST['id']);
		
		$cart_total = countOrderTotal($order_id,$payment_id,$delivery_id,$is_protect,$delivery_region,$tax,$credit,$isCreditAll,$ecvSn,$ecvPassword);
		
		$GLOBALS['tpl']->assign("cart_total",$cart_total);
   		$GLOBALS['tpl']->assign("lang",$GLOBALS['Ln']);
   		$cart_total['html'] = dotran($GLOBALS['tpl']->fetch("Inc/goods/cart_total.tpl"));
   		//$cart_total['html'] = $delivery_region['region_lv4'];
		echo json_encode($cart_total);

}
?>