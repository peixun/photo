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
// 重量管理
class CmpanyModel extends MultiLangModel  {
	public $_auto = array(
			array('create_time','time',1,'function'), 			
			array('update_time','time',3,'function'), 			
		);		
}
?>