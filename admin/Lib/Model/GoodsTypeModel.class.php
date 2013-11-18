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
//商品类型
class GoodsTypeModel extends MultiLangModel {
	protected $_validate = array(
			array('name','require',TYPE_NAME_REQUIRE), 
		);	
}
?>