<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 管理员
class JoinEventAction extends CommonAction{
	   function read(){
		$JoinEvent = D ( "JoinEvent" );
		$where ['id'] = $_REQUEST ['id'];
		$list = $JoinEvent->where($where)->find();
		if($list){
			$this->assign("list",$list);
		}
		    $this->display();
	}
}
?>