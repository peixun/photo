<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */class ArticleModel extends MultiLangModel  {
	protected $_validate = array(
			array('name','require',ARTICLE_NAME_REQUIRE),
			//array('cate_id','gtZero',ARTICLE_CATE_REQUIRE,0,'function'), // 自定义函数验证密码格式
			array('sort','is_numeric',SORT_MUST_BE_NUM,2,'function'),
			//array('ref_link','checkUrl',URL_FORMAT_ERROR,2,'function'), // 自定义函数验证密码格式
			array('u_name','checkArticleUName','别名不能重复',2,'function'),
		);

	protected $_auto = array (
		array('status','1'),  // 新增的时候把status字段设置为1
		array('create_time','gmtTime',1,'function'), // 对create_time字段在插入的时候写入当前时间戳
		array('update_time','gmtTime',3,'function'),
	);

}
?>