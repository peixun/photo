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
class NewsAction extends PublicAction{
	/**
	 * 空操作
	 */
    public function _empty($action) {
        if(empty($_SESSION['uid'])){
			$_SESSION['refer_url'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}else{
			unset($_SESSION['refer_url']);
		}
    	if(is_numeric($action)) {
    		$id=(int)$action;

    		$Article=D("News");
    		$list=$Article->getById($id);
    		if($list!=false){
    		    $newmap['click_count']= array('exp','click_count+1');
    		    $newmap['id']= $id;
    		    $Article ->save($newmap);
    		    $this->assign('vo', $list);

                //推荐促销活动
                $maps['is_top']=1;
                $toplist = $Article->where($maps)->limit('6')->order('id desc')->select();
                $this->assign('toplist',$toplist);

                $Comment =D("Comment");
                $comentmap['status']=1;
                $comentmap['case_id']=$id;
                $comentmap['model']='News';

                $count = $Comment->where($comentmap)->count('id');
                $listRows = empty ( $_REQUEST ["listRows"] ) ? 25 : $_REQUEST ["listRows"];
                $p = new Page ( $count, $listRows );
                $commentlist = $Comment->where($comentmap)->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();
                $page = $p->show ();
                $this->assign('page',$page);
                $this->assign('commentlist',$commentlist);

    			$this->display('read');
    		}else{
    			$this->error("非法操作！");
    		}

    	}else{
    		$this->_404('错误操作');
    	}
    }
    /**
     * 资讯首页
    */
    public function index(){
    	$Article=D("News");
        import("@.ORG.Pages");
        $map['status']=1;
        if($_GET['cid']){
        	$map['cate_id']=$_GET['cid'];
        }
        $count = $Article->where($map)->count('id');
        $listRows = empty ( $_REQUEST ["listRows"] ) ? 6 : $_REQUEST ["listRows"];
        $p = new Page ( $count, $listRows );
        $list = $Article->where($map)->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();


        $this->assign('newsList',$list);
        $page = $p->show ();
        $this->assign('page',$page);
        $ArticleCate =D("ArticleCate");
        $catemap['status']=1;
        $cate =$ArticleCate->select();
        $this->assign('cate',$cate);

        //推荐促销活动
        $maps['is_top']=1;
        $toplist = $Article->where($maps)->limit('6')->order('id desc')->select();
        $this->assign('toplist',$toplist);

        $this->display();
    }

}
?>