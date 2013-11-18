<?php
// 本类由系统自动生成，仅供测试用途
class HelpAction extends PublicAction
	{
        public function index(){
            $page =D("Page")->order('id asc')->find();
            $this->assign('page',$page);
            $this->display();
        }
        public function _empty($action) {
    	if(is_numeric($action)) {
    		$id=(int)$action;

    		$Page=D("Page");
    		$list=$Page->getById($id);
    		if($list!=false){

                $this->assign('page', $list);
    			$this->display('index');
    		}else{
    			$this->error("非法操作！");
    		}

    	}else{
    		$this->_404('错误操作');
    	}
    }
    }
?>