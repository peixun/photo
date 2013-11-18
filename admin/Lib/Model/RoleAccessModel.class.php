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
//角色权限列表
class RoleAccessModel extends CommonModel {
	protected $_validate = array(
			array('node_id','gtZero',NODE_ID_REQUIRE,0,'function'), // 自定义函数验证密码格式
		);
}
?>