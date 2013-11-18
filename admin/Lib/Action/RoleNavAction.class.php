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
// 后台的导航菜单
class RoleNavAction extends CommonAction{
	//增
	public function add()
	{
		$new_sort = D(MODULE_NAME)-> max("sort") + 1;
		$this->assign('new_sort',$new_sort);
		$this->display();
	}
}
?>