<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 文章分类
class OlRegistrationAction extends CommonAction{
    function read(){
		$Olregistration = D ( "OlRegistration" );
		$where ['id'] = $_REQUEST ['id'];
		$list = $Olregistration->where($where)->find();
		if($list){
			$this->assign("list",$list);
		}
		    $this->display();
	}
}
?>