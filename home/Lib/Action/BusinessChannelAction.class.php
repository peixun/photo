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
class BusinessChannelAction extends PublicAction{
    function _initialize()  {

        $ids=explode('_',$_REQUEST['id']);
        //dump($ids);

        //企业频道右边内容
        $Company =D("Company");
        $coms =$Company->where('uid='.$ids[0])->find();
        $this->assign('coms', $coms);
        //dump($coms);

        //荣誉证书
        $Certificate =D("Certificate");
        $cermap['uid']=$ids[0];
        $certi=$Certificate->where($cermap)->limit('4')->select();

        $this->assign('certi', $certi);

        //预约工地
        $ReservationSite=D("ReservationSiteView");
        $resermap['com_uid']=$ids[0];
        $resite =$ReservationSite->where($resermap)->limit('10')->order('id desc')->select();

        $this->assign('resite', $resite);

        //分店地址
        $ComanyAddress =D("ComanyAddress");
        $compmap['uid']=$ids[0];
        $comaddress =$ComanyAddress->where($compmap)->order('id desc')->select();

        if($comaddress!=false){
            //判断假如不存在分店就不显示
            $this->assign('Addressdiv',1);
        }
        $this->assign('comaddress',$comaddress);
        $this->assign('comaddress1',$comaddress);


        //在线qq
        $UserQq =D("UserQq");
        $qmapss['uid']=$ids[0];
        $qqs =$UserQq->where($qmapss)->select();
        if($qqs==false){
            $this->assign('showdiv',1);
        }
        $this->assign('qqs', $qqs);

        $this->assign('comid',$ids[0]);
         parent::_initialize();
    }
	/**
	 * 空操作
	 */
    public function show() {
        if(empty($_SESSION['uid'])){
			$_SESSION['refer_url'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}else{
			unset($_SESSION['refer_url']);
		}
            $Company =D("Company");
            $id=$_GET['id'];

            $list =$Company->where('uid='.$id)->find();


    		if($list!=false){
                //在建工地
                $Construction =D("Construction");
                $conmap['uid']=$list['uid'];
                $conlist=$Construction->where($conmap)->limit('4')->order('id desc')->select();

    		    $this->assign('vo', $list);
    		    $this->assign('conlist', $conlist);



                //案例
                $Case =D("Case");
                $casemaps['uid']=$list['uid'];
                $casemaps['status']=1;
                $count =$Case ->where($casemaps)->count('id');
                if($count<9){
                    $this->assign('caseshow',1);
                }
                $caselists =$Case ->where($casemaps)->order('is_top desc,id desc')->select();
                $caselist =$Case ->where($casemaps)->order('is_top desc,id desc')->limit('8')->select();

                $this->assign('caselists', $caselists);
                $this->assign('caselist', $caselist);

                //设计师
                $Designer =D("Designer");
                $desmap['com_id']=$list['id'];
                $desmap['status']=1;
                $counts =$Designer ->where($desmap)->count('id');
                if($counts<6){
                    $this->assign('desgnershow',1);
                }
                $designers =$Designer->where($desmap)->order('is_top desc,id desc')->select();
                $designer =$Designer->where($desmap)->limit('5')->order('is_top desc,id desc')->select();

                $this->assign('designers', $designers);
                $this->assign('designer', $designer);

                //案例图片
                $PicGallery =D("PicGallery");
                $picmap['model']='Company';
                $picmap['pic_id']=$list['id'];
                $pics =$PicGallery->where($picmap)->limit('3')->select();
                //dump($pics);
                $this->assign("pics1",$pics);

                //优惠活动
                $Article =D("Article");
                $ammap['uid']=$list['uid'];
                $ammap['status']=1;

                $alist =$Article->where($ammap)->limit('5')->order('is_top desc,id desc')->select();
                $this->assign('alist', $alist);



    			$this->display('show');
    		}else{
    			$this->error("非法操作！");
    		}


    }
    /**
     +----------------------------------------------------------
     * @企业简介
     +----------------------------------------------------------
    */
    public function company(){
        $id=$_GET['id'];

        $Company =D("Company");
        $cwhere['uid']=$id;
        $compay =$Company->where($cwhere)->find();



        $this->assign('compay', $compay);
        $this->display();

    }

    /**
     * 案例中心
    */
    public function caselist(){
        $_SESSION['refer_urls'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    	$Article=D("Case");
        import("@.ORG.Pages");
        $map['status']=1;
        $map['uid']=$_GET['id'];

        $count = $Article->where($map)->count('id');
        $listRows = empty ( $_REQUEST ["listRows"] ) ? 8 : $_REQUEST ["listRows"];
        $p = new Page ( $count, $listRows );
        $list = $Article->where($map)->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();


        $this->assign('list',$list);
        $page = $p->show ();
        $this->assign('page',$page);
        $this->assign('counts',$count);
        //促销活动
        $Article=D("News");

        $newslist = $Article->where($map)->limit('6')->order('id desc')->select();
        //dump();
        $this->assign('newslist',$newslist);
        $this->assign('title','案例中心');
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * @企业案例详情
     +----------------------------------------------------------
    */
    public function caseshow(){
        $_SESSION['refer_urls'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $id=explode('_',$_REQUEST['id']);


        $Case =D("Case")->getById($id[1]);

        if($Case==false){
             $this->assign("jumpUrl","__APP__/");
            $this->error("查看的案例不存在！");
        }

        $this->assign("Case",$Case);

        //案例
        $Company =D("Company");
        $cmap['uid']=$id[0];
        $company =$Company->where($cmap)->find();

        $this->assign("company",$company);

        //案例图片
        $PicGallery =D("PicGallery");
        $picmap['model']='Case';
        $picmap['pic_id']=$id[1];
        $pics =$PicGallery->where($picmap)->select();
        $this->assign("pics",$pics);
        $this->assign("pics1",$pics);

        //在线qq
        $UserQq =D("UserQq");
        $qmap['uid']=$id[0];
        $qq =$UserQq->where($qmap)->select();
        $this->assign('qq', $qq);

        //设计师
        $Desinger =D("Designer");
        //$cmmp['id']=$id;
        $desinger=$Desinger->getById($Case['desinger_id']);
        $this->assign('desinger', $desinger);

        //取得案例下的评论
        $Comment =D("Comment");

        import("@.ORG.Pages");
        $comentmap['status']=1;
        $comentmap['model']='Case';
        $comentmap['case_id']=$id[1];

        $count = $Comment->where($comentmap)->count('id');
        $listRows = empty ( $_REQUEST ["listRows"] ) ? 5 : $_REQUEST ["listRows"];
        $p = new Page ( $count, $listRows );
        $list = $Comment->where($comentmap)->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();


        $this->assign('commentlist',$list);
        $this->assign('count',$count);
        $page = $p->show ();
        $this->assign('page',$page);

        //当前社区的促销活动
        $Article =D("Article");
        $artmap['cate_id']=0;
        $artmap['cate_id']=$Case['cate_pid'];

        $artmap['_logic'] = 'or';
        $artmaps['_complex'] = $artmap;
        $article =$Article->where($artmaps)->limit('6')->select();


        $this->assign('article', $article);


        //查询相似案例
        $Tagged =D("Tagged");
        $tagmap['record_id']=$id[1];
        $tagmap['module']='Case';
        $tags =$Tagged ->where($tagmap)->select();

        $strs =getIds($tags,'tag_id');

        //根据关键字取得关联的招标ID
        $tagmaps['tag_id']=array('in',$strs);
        $tagmaps['module']='Case';
        $tagmaps['record_id']=array('neq',$id[1]);
        $tags1 =$Tagged ->field('record_id')->where($tagmaps)->group('record_id')->select();


        $strs1 =getIds($tags1,'record_id');
        $wheres['id']  =array('in',$strs1);

        $gxlist =D("Case")->where($wheres)->limit('4')->order('is_top desc,id desc')->select();
        if($gxlist!=false){
            $this->assign('gxlist',$gxlist);
        }
        if( $gxlist =D("Case")->where($wheres)->count('id')>4){
            $gxlist1 =D("Case")->where($wheres)->order('is_top desc,id desc')->select();
            //dump($gxlist1);
             $this->assign('gxlist1',$gxlist1);
        }else{
            $this->assign('caseshow','2');
        }

        $this->display();
    }
    /**
     +----------------------------------------------------------
     * @案例添加评论
     +----------------------------------------------------------
    */
    public function inserComment(){
        $Comment =D("Comment");
        $datas['content'] =$_POST['content'];
        if(!empty($_SESSION['uid'])){
            $datas['uid'] =$_SESSION['uid'];
        }else{
            $datas['uid'] =0;
        }
        $datas['case_id'] =$_POST['id'];
        $datas['model'] ='Case';
        $datas['create_time'] =time();
        $datas['update_time'] =time();
        $datas['status'] =1;

        $comment =$Comment->add($datas);
        if($comment!=false){
            $this->success('添加评论成功!');
        }else{
            $this->error('添加评论失败!');
        }

    }
    /**
     +----------------------------------------------------------
     * @设计师列表
     +----------------------------------------------------------
    */
    public function designerlist(){

        $id=$_GET['id'];
        $Company =D("Company");

        $coms =$Company->where('uid='.$id)->find();

        $Designer=D("Designer");
        import("@.ORG.Pages");
        $map['status']=1;
        $map['com_id']=$coms['id'];

        $count = $Designer->where($map)->count('id');
        $listRows = empty ( $_REQUEST ["listRows"] ) ? 8 : $_REQUEST ["listRows"];
        $p = new Page ( $count, $listRows );
        $list = $Designer->where($map)->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();


        $this->assign('list',$list);
        $page = $p->show ();
        $this->assign('page',$page);


        $this->assign('title','设计师列表');
        $this->display();


    }
    /**
     +----------------------------------------------------------
     * @查看设计师详情
     +----------------------------------------------------------
    */
    public function designershow(){
        $id=explode('_',$_REQUEST['id']);
        $Designer=D("Designer");
        $designer =$Designer->getById($id[1]);

        if($designer==false){
            $this->assign("jumpUrl","__APP__/");
            $this->error("查看的设计师不存在！");
        }

        $Case =D("Case");
        $casemap['status']=1;
        $casemap['desinger_id']=$id[1];
        $newcase =$Case->where($casemap)->order('id desc')->find();
        $casecounts =$Case->where($casemap)->count('id');

        //历史案例
        $Case =D("Case");
        $casemaps['desinger_id']=$id[1];
        $casemaps['status']=1;
        $count =$Case ->where($casemaps)->count('id');
        if($count<4){
            $this->assign('caseshow',1);
        }
        $caselists =$Case ->where($casemaps)->order('is_top desc,id desc')->select();
        $caselist =$Case ->where($casemaps)->order('is_top desc,id desc')->limit('4')->select();

        $this->assign('caselists', $caselists);
        $this->assign('caselist', $caselist);


        $PicGallery =D("PicGallery");
        $picmap['pic_id']=$newcase['id'];
        $picmap['model']='Case';
        $piclist =$PicGallery->where($picmap)->order('id desc')->limit('4')->select();
        $casecounts =$PicGallery->count();

        $this->assign('newcase',$newcase);
        $this->assign('piclist',$piclist);
        $this->assign('designer',$designer);
        $this->assign('casecounts',$casecounts);

        $this->display();
    }

    /**
     +----------------------------------------------------------
     * @在建工地列
     +----------------------------------------------------------
    */
    public function constructionlist(){
        $id=$_GET['id'];
        $Construction=D("Construction");
        $wmap['uid']=$id;
        import ( "@.ORG.Pages" );
        //--------最新相册---------
        $count = $Construction->where($wmap)->count ('id');
        $p = new Page ( $count, 6 );
        $construction = $Construction->where($wmap)->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();

        $page = $p->show ();
        $this->assign ( "construc", $construction );
        $this->assign ( "page", $page );
        $this->display();

   }
   /**
    +----------------------------------------------------------
    * @促销活动
    +----------------------------------------------------------
   */
   public function discountslist(){

       $id=$_GET['id'];
        $Article=D("Article");
        $wmap['uid']=$id;
        import ( "@.ORG.Pages" );
        //--------最新相册---------
        $count = $Article->where($wmap)->count ('id');
        $p = new Page ( $count, 10 );
        $list = $Article->where($wmap)->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();

        $page = $p->show ();
        $this->assign ( "list", $list );
        $this->assign ( "page", $page );
        $this->display();

   }
   /**
    +----------------------------------------------------------
    * @查看促销活动
    +----------------------------------------------------------
   */
   public function discountshow(){
       if(empty($_SESSION['uid'])){
			$_SESSION['refer_url'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}else{
			unset($_SESSION['refer_url']);
		}
        $id=explode('_',$_REQUEST['id']);
        $Article=D("Article");
        $article =$Article->getById($id[1]);
        if($article==false){

             $this->assign("jumpUrl","__APP__/");
            $this->error("查看的促销活动不存在！");
        }

        $this->assign('vo',$article);

        if($article){

            $newmap['click_count']= array('exp','click_count+1');
            $newmap['id']= $id[1];
            $Article ->save($newmap);
        }

        //取得案例下的评论
        $Comment =D("ArticleComment");

        import("@.ORG.Pages");
        $comentmap['status']=1;
        $comentmap['article_id']=$id[1];

        $count = $Comment->where($comentmap)->count('id');
        $listRows = empty ( $_REQUEST ["listRows"] ) ? 5 : $_REQUEST ["listRows"];
        $p = new Page ( $count, $listRows );
        $list = $Comment->where($comentmap)->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();

        if($list!=false){


            $this->assign('commentlist',$list);
        }
        $this->assign('count',$count);
        $page = $p->show ();
        $this->assign('page',$page);


        $this->display();
        //dump($article);
   }
    /**
     +----------------------------------------------------------
     * @优惠促销添加评论
     +----------------------------------------------------------
    */
    public function inserAComment(){
        $Comment =D("ArticleComment");
        $datas['content'] =$_POST['content'];
        if(!empty($_SESSION['uid'])){
            $datas['uid'] =$_SESSION['uid'];
        }else{
            $datas['uid'] =0;
        }
        $datas['article_id'] =$_POST['id'];

        $datas['create_time'] =time();
        $datas['update_time'] =time();
        $datas['status'] =1;

        $comment =$Comment->add($datas);
        if($comment!=false){
            $this->success('添加评论成功!');
        }else{
            $this->error('添加评论失败!');
        }

    }
    /**
     +----------------------------------------------------------
     * @服务承诺
     +----------------------------------------------------------
    */
    public function service(){
        $id= $_GET['id'];
        $Company =D("Company");
        $companymap['uid']=$id;
        $companys=$Company->where($companymap)->find();
        $this->assign('companys',$companys);
        $this->assign('title','服务承诺');
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * @联系方式
     +----------------------------------------------------------
    */
    public function contact(){
        $id= $_GET['id'];
        $Company =D("Company");
        $companymap['uid']=$id;
        $companys=$Company->where($companymap)->find();
        $this->assign('companys',$companys);
        $this->assign('title','联系方式');
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * @荣誉证书列表
     +----------------------------------------------------------
    */
    public function certificatelist(){
        $id=$_GET['id'];
        $Certificate =D("Certificate");
        $cermap['uid']=$id;
        $cermap['status']=1;



        import ( "@.ORG.Pages" );
        //--------最新相册---------
        $count = $Certificate->where($cermap)->count ('id');
        $p = new Page ( $count, 10 );
        $list = $Certificate->where($cermap)->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();

        $page = $p->show ();

        $this->assign ( "page", $page );
        $this->assign('certificate',$list);
        $this->assign('title','荣誉证书列表');
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * @查看荣誉证书
     +----------------------------------------------------------
    */
    public function certificateshow(){
        $id=explode('_',$_REQUEST['id']);
        $Certificate=D("Certificate");
        $certicate =$Certificate->getById($id[1]);

        if($certicate==false){
             $this->assign("jumpUrl","__APP__/");
            $this->error("查看的荣誉证书不存在！");
        }

        $this->assign('certicate',$certicate);
        $this->assign('title',$certicate['name']);
        $this->display();

    }

}
?>