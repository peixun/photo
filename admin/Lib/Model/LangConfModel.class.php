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
//语言
class LangConfModel extends CommonModel {
	protected $_validate = array(
		array('lang_name','require',LANG_NAME_REQUIRE), 
		array('show_name','require',SHOW_NAME_REQUIRE),
		array('time_zone','number',TIME_ZONE_MUST_BE_NUM,2),  	
	);	
}
?>