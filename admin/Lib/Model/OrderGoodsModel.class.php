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
//订单商品
class OrderGoodsModel extends CommonModel {
	protected $_validate = array(
			array('unit_price','is_numeric',UNIT_PRICE_MUST_BE_NUM,1,'function'), 
			array('number','is_numeric',NUMBER_MUST_BE_NUM,1,'function'), 
			array('total_price','is_numeric',TOTAL_MUST_BE_NUM,1,'function'), 
		);
}
?>