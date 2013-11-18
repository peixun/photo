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
//布局
class LayoutModel extends CommonModel {
	protected $_validate = array(
			array('tmpl','require',TMPL_REQUIRE), 			
			array('layout_id','require',LAYOUT_ID_REQUIRE), 
		);

}
?>