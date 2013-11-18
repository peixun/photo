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
//前台菜单
class NavModel extends MultiLangModel {
	protected $_validate = array(
			array('name','require', NAV_NAME_REQUIRE), 
			//array('url','checkUrl',URL_FORMAT_ERROR,2,'function'), // 自定义函数验证密码格式
		);
	protected $_auto = array ( 		
		array('status','1'),  // 新增的时候把status字段设置为1	
	);
}
?>