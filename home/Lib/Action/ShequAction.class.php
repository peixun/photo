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
class ShequAction extends PublicAction{
	/**
	 * 空操作
	 */
    public function _empty($action) {
    	if(is_numeric($action)) {
    		$id=(int)$action;

    		$Category=D("Category");
    		$list=$Category->getById($id);
    		if($list!=false){
                $newmap['click_count']= array('exp','click_count+1');
    		    $newmap['id']= $id;
    		    $Category ->save($newmap);

                $Category=D("Category");
                $humap['pid']=$list['id'];
                $huxing =$Category->where($humap)->select();
                $hxcounts =$Category->where($humap)->count('id');
                $count=$Category->where($humap)->count('id');
                if($count>4){
                    $this->assign('caseshow',1);
                }
                if($hxcounts==0){
                    $this->assign('jsshow',1);
                }

    		    $this->assign('huxing', $huxing);
    		    $this->assign('case', $huxing);
    		    $this->assign('hxcounts', $hxcounts);
    		    $this->assign('vo', $list);
                //促销活动
                $Article =D("Article");
                $amap['cate_id']=array('in','0,{$id}');
                $article =$Article->order('is_top desc,id desc')->limit('6')->select();
                $this->assign('article', $article);
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
    	$Article=D("Category");
        import("@.ORG.Pages");
        $map['status']=1;
        $map['pid']=0;
        if($_GET['cid']){
        	$map['area_id']=$_GET['cid'];
        }
        if(!empty($_GET['is_top'])){
            $map['is_top']=1;
        }
        if(!empty($_GET['hot'])){
            $order ='click_count desc,id desc';
        }else{
            $order='id desc';
        }
        $count = $Article->where($map)->count('id');
        $listRows = empty ( $_REQUEST ["listRows"] ) ? 12 : $_REQUEST ["listRows"];
        $p = new Page ( $count, $listRows );
        $list = $Article->where($map)->limit($p->firstRow . ',' . $p->listRows)->order($order)->select();
       //echo  $Article->getlastsql();

        $this->assign('shequList',$list);
        $page = $p->show ();
        $this->assign('page',$page);
        //dump($list);

        //促销活动
        $Article =D("News");

        $article =$Article->order('is_top desc,id desc')->limit('9')->select();
        $this->assign('article', $article);

        $this->display();
    }

}
?>