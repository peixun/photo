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

class SuppliersDepartModel extends CommonModel {
	protected $_validate = array(
			array('depart_name','require','部门名称不能为空'), 
			array('pwd','require','密码不能为空','','',1), // 自定义函数验证密码格式
			array('depart_name','','部门名称已存在',0,'unique',1), 
	
		);
}
?>