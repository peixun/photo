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
class MailAddressListModel extends CommonModel {
	protected $_validate = array(
			array('mail_address','require',MAIL_ADDRESS_REQUIRE), 
			array('mail_address','check_mail',MAIL_FORMAT_ERROR,2,'function'), 
		);
	protected $_auto = array ( 		
		array('status','1'),  // 新增的时候把status字段设置为1
	);
}
?>