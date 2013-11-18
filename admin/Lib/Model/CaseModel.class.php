<?php
/*---------------------------------------------------------
|+   Url     :   www.thinkphp.cn
|+  作用   ： 系统项目组可以共用的基类库，继承则可，自动加载
|+ Author :  Mustache
|+ Name  :   商品信息分类模型
-----------------------------------------------------------*/

class CaseModel extends CommonModel {
	protected $_validate = array(
			array('name','require',CATE_NAME_REQUIRE),
		);
    protected $_auto = array (
		array('status','1'),  // 新增的时候把status字段设置为1
		array('create_time','gmtTime',1,'function'), // 对create_time字段在插入的时候写入当前时间戳
		array('update_time','gmtTime',3,'function'),
	);
}
?>