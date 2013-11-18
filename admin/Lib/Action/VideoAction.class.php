<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  liuqiang <liuqiang@eyoo.cn>
 * @time    2011-02-08
 * @Action  Index Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */

class VideoAction extends CommonAction {
	//视频首页
	

	public function _before_update() {
		$_POST ["update_time"] = time (); 
	}
	public function _before_insert() {
		$_POST ["create_time"] = time ();
	}
}
?>
