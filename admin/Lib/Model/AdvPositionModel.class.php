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
// 广告模型
class AdvPositionModel extends CommonModel {
	public $_validate	=	array(
		array('name','require',ADV_POSITION_NAME_REQUIRE),
	);
	protected $_map = array(
		'is_flash'	=>	'is_flash', 
		'flash_style'	=>	'flash_style',
	);
}
?>