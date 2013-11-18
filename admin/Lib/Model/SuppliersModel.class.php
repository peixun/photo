<?php
// +----------------------------------------------------------------------
// | Fanwe 多语商城建站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: awfigq(67877605@qq.com)
// +----------------------------------------------------------------------

class SuppliersModel extends CommonModel{
	protected $_validate = array(
			array('name','require',SUPPLIERS_NAME_REQUIRE),
			array('cate_id','gtZero',"请选择分类",0,'function'), // 自定义函数验证密码格式
	);
		
	protected $_auto = array ( 		
		array('status','1'),  // 新增的时候把status字段设置为1
	);
}
?>