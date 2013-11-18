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
//配送地址列表
class UserConsigneeModel extends CommonModel   {
	protected $_validate = array(
			array('consignee','require',CONSIGNEE_NAME_REQUIRE), 
			array('address','require',CONSIGNEE_ADDRESS_REQUIRE), 
		);
}
?>