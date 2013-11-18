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
class AdvModel extends CommonModel {
	public $_validate	=	array(
		array('name','require',ADV_NAME_REQUIRE),
	);

	public $_auto		=	array(
		array('status','1'),  // 新增的时候把status字段设置为1
		);
	protected $_map = array(
		'code'	=>	'code',  //用于单独操作的字段映射，在上传文件后更新
		'url'	=>	'url',
		'type'	=>	'type',
	);
}
?>