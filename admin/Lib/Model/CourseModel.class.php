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
class CourseModel extends MultiLangModel {
	protected $_validate = array(
			array('name','require',CATE_NAME_REQUIRE),
		);
	protected $_auto = array (
		array('status','1'),  // �����ʱ���status�ֶ�����Ϊ1
		array('create_time','gmtTime',1,'function'),
		array('update_time','gmtTime',1,'function'),
		array('start_time','parseToTimeSpanFull',3,'function'),
		array('end_time','parseToTimeSpanFull',3,'function'),
	);
}
?>