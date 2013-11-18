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
//用户组
class UserGroupModel extends MultiLangModel {
	protected $_validate = array(
			array('name','require',GROUP_NAME_REQUIRE), 
			array('discount','checkDiscount',DISCOUNT_FORMAT_ERROR,1,'function'),
		);
	protected $_auto = array ( 		
		array('status','1'),  // 新增的时候把status字段设置为1
		array('discount','priceVal',3,'function'), 
	);
}
?>