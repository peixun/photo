<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.to-serve.com
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2012-03-29
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */
class CaseAction extends PublicAction{
	/**
	 * 空操作
	 */
    public function _empty($action) {
    	if(is_numeric($action)) {
    		$id=(int)$action;

    		$Case=D("Case");
    		$list=$Case->getById($id);
    		if($list!=false){
    		    $newmap['click_count']= array('exp','click_count+1');
    		    $newmap['id']= $id;
    		    $Case ->save($newmap);
    		    $this->assign('vo', $list);

    			$this->display('read');
    		}else{
    			$this->error("非法操作！");
    		}

    	}else{
    		$this->_404('错误操作');
    	}
    }
    /**
     * 案例中心
    */
    public function index(){
    	$Article=D("Case");
        import("@.ORG.Pages");
        $map['status']=1;
        //if($_GET['cid']){
        //	$map['cate_id']=$_GET['cid'];
        //}
        $count = $Article->where($map)->count('id');
        $listRows = empty ( $_REQUEST ["listRows"] ) ? 6 : $_REQUEST ["listRows"];
        $p = new Page ( $count, $listRows );
        $list = $Article->where($map)->limit($p->firstRow . ',' . $p->listRows)->order('is_top desc,id desc')->select();


        $this->assign('list',$list);
        $page = $p->show ();
        $this->assign('page',$page);
        //促销活动
        $Article=D("News");

        $newslist = $Article->where($map)->limit('6')->order('id desc')->select();
        //print_r($list);exit();
        $this->assign('newslist',$newslist);
        $this->assign('title','案例中心');
        $this->display();
    }

}
?>