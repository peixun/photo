<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 *///订单
class OrderModel extends CommonModel {
	protected $_auto = array ( 		
		array('update_time','time',3,'function'), 
	);
	
	/**
	 * 计算当前定单的重量
	 *
	 * @param integer $order_id  订单号
	 * @param integer $weight_id  重量
	 */
	public function getOrderWeight($order_id,$weight_id)
	{
		$weight_unit = D("Weight")->getById($weight_id);   //用于返回的重量标准
		
		$rs['weight_unit'] = $weight_unit;
		$rs['weight'] = 0;
		$goods_list = D("OrderGoods")->where("order_id=".$order_id)->findAll();
		foreach($goods_list as $k=>$v)
		{
			$curr_weight_unit = D("Weight")->getById(D($v['rec_module'])->where("id=".$v['rec_id'])->getField("weight_unit"));
			
			if($v['spec_item_id']>0)
			{
				$spec_item_info = D("GoodsSpecItem")->getById($v['spec_item_id']);
				$rs['weight'] += floatval(($spec_item_info['weight']*$v['number'])/$weight_unit['radio']*$curr_weight_unit['radio']);

			}
			else 
			{
				
				$spec_item_info = D($v['rec_module'])->where("id=".$v['rec_id'])->find();
				$rs['weight'] += floatval(($spec_item_info['weight']*$v['number'])/$weight_unit['radio']*$curr_weight_unit['radio']);
				
			}
			
		}
 
		return $rs;
	}
}
?>