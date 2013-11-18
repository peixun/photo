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
//商品属性
class GoodsTypeAttrModel extends MultiLangModel {
	protected $_validate = array(
			array('name','require',ATTR_NAME_REQUIRE), 
		);	
}
?>