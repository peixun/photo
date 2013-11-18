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
//留言
class MessageModel extends CommonModel {
	protected $_validate = array(
			array('title','require',MESSAGE_TITLE_REQUIRE), 
			array('content','require',MESSAGE_CONTENT_REQUIRE), 
			array('score',array(0,1,2,3,4,5),SCORE_ERROR,2,'in'), 
		);
		
	protected $_auto = array ( 		
		array('create_time','gmtTime',1,'function'), // 对create_time字段在插入的时候写入当前时间戳			
	);
}
?>