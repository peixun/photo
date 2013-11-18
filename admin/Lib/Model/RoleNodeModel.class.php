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
//后台节点
class RoleNodeModel extends CommonModel {
	protected $_auto = array ( 		
		array('status','1'),  // 新增的时候把status字段设置为1	
	);
	protected $_map = array(
		'auth_type'	=>	'auth_type',  //用于单独操作的字段映射
	);
}
?>