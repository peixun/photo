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
// 邮件列表模型
class MailListModel extends CommonModel {
	protected $_validate = array(
			array('mail_title','require',MAIL_TITLE_REQUIRE), 
			array('send_time','require',SEND_TIME_REQUIRE), 
			array('send_time','check_time',TIME_FORMAT_ERROR,2,'function'), 
		);
	protected $_auto = array ( 		
		array('send_time','localStrToTime',3,'function'), 	 
	);

}
?>