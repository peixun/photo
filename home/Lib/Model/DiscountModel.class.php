<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 *///商品
class DiscountModel extends MultiLangModel {
	protected $_validate = array(
			//array('name','require',GOODS_NAME_REQUIRE),
			//array('market_price','is_numeric',MARKET_PRICE_MUST_BE_NUM,2,'function'),
			//array('shop_price','is_numeric',SHOP_PRICE_MUST_BE_NUM,2,'function'),
			//array('sort','is_numeric',SORT_MUST_BE_NUM,2,'function'),
			//array('stock','is_numeric',STOCK_MUST_BE_NUM,2,'function'),
			//array('promote_begin_time','checkDateFormat',PROMOTE_BEGIN_TIME_ERROR,2,'function'),
			array('end_time','checkDateFormat',PROMOTE_END_TIME_ERROR,2,'function'),
			//array('group_bond_end_time','checkDateFormat',"团购券过期时间格式错误",2,'function'),
			//array('u_name','checkGoodsUName','别名不能重复',2,'function'),
		);

	protected $_auto = array (
		//array('status','1'),  // 新增的时候把status字段设置为1
		//array('create_time','gmtTime',1,'function'), // 对create_time字段在插入的时候写入当前时间戳
		//array('update_time','gmtTime',3,'function'),
		//array('sn','genGoodsSn',3,'function'), 	   //未填写时自动生成货号
		//array('promote_price','shop_price',3,'field'),
		//array('promote_begin_time','localStrToTimeMin',3,'function'),
		array('end_time','localStrToTimeMax',3,'function'),
		//array('group_bond_end_time','localStrToTimeMax',3,'function'),
	);



}
?>