<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.top-serve.com
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2012-3-8
 * @Action  Index Action
 * @copyright Copyright &copy; 2009-2011 eyoo Software LLC
 +----------------------------------------------------------
 */

class CompanyAction extends PublicAction{

    /**
     +----------------------------------------------------------
     * 企业中心
     +----------------------------------------------------------
    */

    public function index(){
         $this->checkUser();


		$user =M("User")->getById($_SESSION['uid']);
		$this->assign('user',$user);
		$this->assign('sps',$sps);


		$this->display();


    }
    /**
     +----------------------------------------------------------
     * @企业管理
     +----------------------------------------------------------
    */
	public function management(){
        $user =D("User")->getById($_SESSION['uid']);
        if($user['active']==0){
            $this->redirect("Company/edit",true);
        }

        $Company =D("Company");
        $cwhere['uid']=$_SESSION['uid'];
        $com =$Company->where($cwhere)->find();
        $this->assign('com',$com);

        //qq列表
        $UserQq =D("UserQq");
        $qmap['uid']=$_SESSION['uid'];
        $qq =$UserQq->where($qmap)->order('id desc')->select();

        $this->assign('qq',$qq);

        //分店列表
        $ComanyAddress =D("ComanyAddress");
        $compmap['uid']=$_SESSION['uid'];
        $address =$ComanyAddress->where($compmap)->order('id desc')->select();

        $this->assign('address',$address);

        //企业图片列表
        $PicGallery = D ( 'PicGallery' );
        $pmap['pic_id']=$com['id'];
        $pmap['model']='Company';
        $pic =$PicGallery->where($pmap)->order('id asc')->select();
        $this->assign('pic',$pic);
		$this->display();

	}
    /**
     +----------------------------------------------------------
     * @修改企业信息
     +----------------------------------------------------------
    */
    public function edit(){
       // $this->checkUser();



        $Company =D("Company");
        $cwhere['uid']=$_SESSION['uid'];
        $com =$Company->where($cwhere)->find();


        $this->assign('com',$com);
        $this->display();

    }
    /**
     +----------------------------------------------------------
     * @修改操作执行前操作
     +----------------------------------------------------------
    */

    public function _before_update() {
		$PicGallery = D ( 'PicGallery' );

		if (! empty ( $_FILES ['images'] ['name'] ['0'] )) {
			//echo 'login';
			//exit;
			$uploadList = $this->_upload ( $_FILES );
			for($i = 0; $i < count ( $uploadList ); $i ++) {
				$data ['model'] = 'Company';
				$data ['pic_id'] = $_POST['id'];
				$data ['images'] = $uploadList [$i] ['savename'];
				$data ['img_name'] = $_POST ['imgname'] [$i];
				$data ["create_time"] = time ();
				$result = $PicGallery->add ( $data );

			}
		}
	}
    /**
     +----------------------------------------------------------
     * @执行修改操作
     +----------------------------------------------------------
    */
    public function update(){
        $Company =D("Company");
        if (false === $Company->create ()) {
            $this->error ( $Company->getError () );
        }
        $Company->status=1;

        $business_scope =implode(',',$_POST['business_scope']);

        $good_style =implode(',',$_POST['good_style']);

        $decoration_pattern =implode(',',$_POST['decoration_pattern']);

        $Company->decoration_pattern=$decoration_pattern;
        $Company->business_scope=$business_scope;
        $Company->good_style=$good_style;
        $Company->update_time=time();

        $result = $Company->save();

        if ($result) {
			//更新用户数据
            $User =D("User");
            $data['active']=1;
            $data['id']=$_SESSION['uid'];
            $User->save($data);
            //添加QQ
            $UserQq=D("UserQq");
            if(!empty($_POST['qq'][0])){

                for($i = 0; $i < count ( $_POST['qq'] ); $i ++) {
                    $datas ["qq"] =$_POST['qq'][$i] ;
                    $datas ["create_time"] = time ();
                    $datas ["status"] = 1;
                    $datas ["uid"] = $_SESSION['uid'];
                    $qq = $UserQq->add ( $datas );
                }
            }

            $this->assign("jumpUrl","__APP__/Company/management");
			$this->success ( "公司信息修改成功!" );
		 } else {
			$this->error ( "公司信息修改失败!" );
		 }
    }
    /**
     +----------------------------------------------------------
     * @上传操作
     +----------------------------------------------------------
    */
    function _upload() {
		$upload = new UploadFile ();
		//设置上传文件大小
		$upload->maxSize = 3292200;
		//设置上传文件类型
		$upload->allowExts = explode ( ',', 'jpg,gif,png,jpeg,rar' );
		//设置附件上传目录
		$upload->thumb = true;
		$upload->thumbPrefix = 'l_,m_,s_';
		$upload->thumbMaxHeight = '377,162,118';
		$upload->thumbMaxWidth = '760,326,164';
		//设置上传文件规则
		$upload->saveRule = 'uniqid';
		//存在同名文件覆盖,可上传相同图片
		$upload->uploadReplace = 'true';
		//删除原图路径
		$upload->thumbRemoveOrigin = false;

		$upload->savePath = './Public/upload/company/';
		//$upload->saveRule = true;
		if (! $upload->upload ()) {
			$this->error ( $upload->getErrorMsg () );
		} else {
			$uploadList = $upload->getUploadFileInfo ();
			return $uploadList;
		}
	}
    /**
     +----------------------------------------------------------
     * @添加QQ
     +----------------------------------------------------------
    */
    public function insertQq(){
        $UserQq=D("UserQq");
        for($i = 0; $i < count ( $_POST['qq'] ); $i ++) {
            $datas ["qq"] =$_POST['qq'][$i] ;
            $datas ["create_time"] = time ();
            $datas ["status"] = 1;
            $datas ["uid"] = $_SESSION['uid'];
            $qq = $UserQq->add ( $datas );
        }
        if($qq){
            $this->success('添加QQ成功!');
        }else{
            $this->error('添加QQ失败!');
        }

    }
    /**
     +----------------------------------------------------------
     * @删除QQ
     +----------------------------------------------------------
    */
    public function delqq(){
        $id =$_GET['id'];
        $UserQq =D("UserQq");
        $qmap['id']=$id;
        $qq =$UserQq->where($qmap)->delete();
        if($qq){
            $this->success('删除qq成功!');
        }else{
            $this->error('删除qq失败!');
        }

    }

    /**
     +----------------------------------------------------------
     * @添加地址
     +----------------------------------------------------------
    */
    public function insertAddress(){
        $ComanyAddress=D("ComanyAddress");
        for($i = 0; $i < count ( $_POST['name'] ); $i ++) {
            $datas ["name"] =$_POST['name'][$i] ;
            $datas ["address"] =$_POST['address'][$i] ;
            $datas ["tel"] =$_POST['tel'][$i] ;
            $datas ["create_time"] = time ();
            $datas ["status"] = 1;
            $datas ["uid"] = $_SESSION['uid'];
            $qq = $ComanyAddress->add ( $datas );
        }
        if($qq){
            $this->success('添加分店地址成功!');
        }else{
            $this->error('添加分店地址失败!');
        }

    }
    /**
     +----------------------------------------------------------
     * @删除地址
     +----------------------------------------------------------
    */
    public function delAddress(){
        $id =$_GET['id'];
        $ComanyAddress =D("ComanyAddress");
        $qmap['id']=$id;
        $qq =$ComanyAddress->where($qmap)->delete();
        if($qq){
            $this->success('删除分店地址成功!');
        }else{
            $this->error('删除分店地址失败!');
        }

    }
    /**
     +----------------------------------------------------------
     * @添加企业照片
     +----------------------------------------------------------
    */

    public function insertPic(){
        $PicGallery = D ( 'PicGallery' );

		if (! empty ( $_FILES ['images'] ['name'] ['0'] )) {
			//echo 'login';
			//exit;
			$uploadList = $this->_upload ( $_FILES );
			for($i = 0; $i < count ( $uploadList ); $i ++) {
				$data ['model'] = 'Company';
				$data ['pic_id'] = $_POST['pic_id'];
				$data ['images'] = $uploadList [$i] ['savename'];
				$data ['img_name'] = $_POST ['imgname'] [$i];
				$data ["create_time"] = time ();
				$result = $PicGallery->add ( $data );
			}
		}

        if($result){
            $this->success('添加企业图片成功!');
        }else{
            $this->error('添加企业图片失败!');
        }

    }
    /**
     +----------------------------------------------------------
     * @删除企业图片
     +----------------------------------------------------------
    */

	public function picDelete() {
        $id = intval($_REQUEST['id']);

		$attahcment_item = D("PicGallery")->getById($id);
		if($rs = D("PicGallery")->where("id=".$id)->delete())
		{
			    @unlink("./Public/upload/company/".$attahcment_item['images']);
            	@unlink("./Public/upload/company/".'l_'.$attahcment_item['images']);
            	@unlink("./Public/upload/company/".'m_'.$attahcment_item['images']);
            	@unlink("./Public/upload/company/".'s_'.$attahcment_item['images']);
            $this->assign("jumpUrl","__APP__/Company/management");
			$this->success ('删除成功!');
		}
		else
		{
			$this->error ('删除失败!');
		}
	}
    /**
     +----------------------------------------------------------
     * @删除案例图片
     +----------------------------------------------------------
    */
    public function delPic()
	{
		$id = intval($_REQUEST['id']);

		$attahcment_item = D("PicGallery")->getById($id);
		if($rs = D("PicGallery")->where("id=".$id)->delete())
		{
			    @unlink("./Public/upload/case/".$attahcment_item['images']);
            	@unlink("./Public/upload/case/".'l_'.$attahcment_item['images']);
            	@unlink("./Public/upload/case/".'m_'.$attahcment_item['images']);
            //$this->assign("jumpUrl","__APP__/Company/mycase");
			$this->success ('删除成功!');
		}
		else
		{
			$this->error ('删除失败!');
		}
	}
    /**
     +----------------------------------------------------------
     * @优惠活动列表
     +----------------------------------------------------------
    */
    public function news(){
        $Article=D("Article");
        $wmap['uid']=$_SESSION['uid'];
        import ( "@.ORG.Pages" );
        //--------最新相册---------
        $count = $Article->where($wmap)->count ('id');
        $p = new Page ( $count, 10 );
        $article = $Article->where($wmap)->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();

        $page = $p->show ();
        $this->assign ( "article", $article );
        $this->assign ( "page", $page );
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * @添加优惠活动信息
     +----------------------------------------------------------
    */
    public function addNews(){
        $Category =D("Category");
        $cate =$Category->where('pid=0')->select();
        $this->assign ( "cate", $cate );
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * @执行优惠活动操作
     +----------------------------------------------------------
    */
    public function insertNews(){

        if(!empty($_FILES['image']['tmp_name'])){

            if(eyooC("WATER_MARK")){
                //  echo 'ok';
    	    	$result = $this->uploadFile(1,'news',1,326,163,220,90,true);
    	    }else{

    		    $result = $this->uploadFile(1,'news',1,326,163,220,90,true);
            }
            $res = $result['uploadList'];

            //$uploadList = $this->_Aupload ( $_FILES );
            $data ['image'] = $res[0]['savename'];

        }
        $Article=D("Article");
        $data['name_1']=$_POST['name_1'];
        $data['cate_id']=$_POST['cate_id'];
        $data['create_time']=time();
        $data['update_time']=time();
        $data['status']=1;
        $data['content_1']=htmlCv($_POST['content_1']);
        $data['brief_1']=$_POST['brief_1'];

        $data['uid']=$_SESSION['uid'];
        $article =$Article->add($data);


        if($article){
            $this->assign("jumpUrl","__APP__/Company/news");
            $this->success('数据添加成功!');
        }else{
            $this->error('数据添加失败!');
        }

    }
    /**
     +----------------------------------------------------------
     * @修改优惠活动
     +----------------------------------------------------------
    */
    public function editNews(){
         $id=$_GET['id'];
         $Article=D("Article");
         $vo =$Article->getById($id);
         $Category =D("Category");
         $cate =$Category->where('pid=0')->select();
         $this->assign ( "cate", $cate );
         $this->assign('vo',$vo);
         $this->display();

    }

    /**
     +----------------------------------------------------------
     * @执行优惠活动编辑操作
     +----------------------------------------------------------
    */
    public function updateNews(){

        if(!empty($_FILES['image']['tmp_name']['0'])){
             if(eyooC("WATER_MARK")){
                //  echo 'ok';
    	    	$result = $this->uploadFile(1,'news',1,326,163,160,90,true);
    	    }else{

    		    $result = $this->uploadFile(1,'news',1,326,163,160,90,true);
            }
            $res = $result['uploadList'];
            $data ['image'] = $res [0] ['savename'];
        }
        $Article=D("Article");
        $data['name_1']=$_POST['name_1'];
        $data['update_time']=time();

        $data['content_1']=htmlCv($_POST['content_1']);
        $data['brief_1']=$_POST['brief_1'];
        $data['cate_id']=$_POST['cate_id'];
        $data['id']=$_POST['id'];
        $article =$Article->save($data);

        if($article){
            $this->assign("jumpUrl","__APP__/Company/news");
            $this->success('数据编辑成功!');
        }else{
            $this->error('数据编辑失败!');
        }

    }
    /**
     +----------------------------------------------------------
     * @删除优惠活动
     +----------------------------------------------------------
    */
    public function delNews(){
        $id=$_GET['id'];
        $Article=D("Article");
        $amap['id']=$id;
        $vo=$Article->where($amap)->delete();
        if($vo){
            $this->success("数据删除成功！");
        }else{
            $this->error("数据删除失败！");
        }
    }
    /**
     +----------------------------------------------------------
     * @案例列表
     +----------------------------------------------------------
    */
    public function mycase(){
        $Case=D("Case");
        $wmap['uid']=$_SESSION['uid'];
        import ( "@.ORG.Pages" );
        //--------最新相册---------
        $count = $Case->where($wmap)->count ('id');
        $p = new Page ( $count, 10 );
        $case = $Case->where($wmap)->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();
        //echo $Case->getlastsql();

        $page = $p->show ();
        $this->assign ( "case", $case );
        $this->assign ( "page", $page );
        $this->display();

    }
    /**
     +----------------------------------------------------------
     * @添加案例
     +----------------------------------------------------------
    */
    public function addCase(){
        $this->assign('title','添加案例');
        $Category =D("Category");
        $cate =$Category->where('pid=0')->select();
        //dump($cate);
        $Company=D("Company");
        $cmap['uid']=$_SESSION['uid'];
        $com =$Company->where($cmap)->find();
        //echo $Company->getlastsql();
        //dump($com);
        $Designer=D("Designer");
        $dwhere['com_id']=$com['id'];
        $designer =$Designer->where($dwhere)->select();
        //echo $Designer->getlastsql();
        //dump($designer);
        $this->assign ( "cate", $cate );
        $this->assign ( "designer", $designer );
        $this->display();
    }
     /**
     +----------------------------------------------------------
     * @删除优惠活动
     +----------------------------------------------------------
    */
    public function delCase(){
        $id=$_GET['id'];
        $Article=D("Case");
        $amap['id']=$id;
        $vo=$Article->where($amap)->delete();
        if($vo){
            $this->success("数据删除成功！");
        }else{
            $this->error("数据删除失败！");
        }
    }
    /**
     +----------------------------------------------------------
     * @上传案例图片
     +----------------------------------------------------------
    */
    public function _before_insertCase(){
        $PicGallery = D ( 'PicGallery' );

		if (! empty ( $_FILES ['images'] ['name'] ['0'] )) {
			//echo 'login';
			//exit;
			$uploadList = $this->_uploads ( $_FILES );
			for($i = 0; $i < count ( $uploadList ); $i ++) {
				$data ['model'] = 'Case';
				$data ['pic_id'] = 0;
				$data ['images'] = $uploadList [$i] ['savename'];
				$data ['img_name'] = $_POST ['imgname'] [$i];
				$data ["create_time"] = time ();
				$result = $PicGallery->add ( $data );
			}
		}
    }
    /**
     +----------------------------------------------------------
     * @上传操作
     +----------------------------------------------------------
    */
    function _uploads() {
		$upload = new UploadFile ();
		//设置上传文件大小
		$upload->maxSize = 329220011;
		//设置上传文件类型
		$upload->allowExts = explode ( ',', 'jpg,gif,png,jpeg,rar' );
		//设置附件上传目录
		$upload->thumb = true;
		$upload->thumbPrefix = 'l_,m_,s_';
		$upload->thumbMaxHeight = '360,170,103';
		$upload->thumbMaxWidth = '520,200,138';
		//设置上传文件规则
		$upload->saveRule = 'uniqid';
		//存在同名文件覆盖,可上传相同图片
		$upload->uploadReplace = 'true';
		//删除原图路径
		$upload->thumbRemoveOrigin = false;

		$upload->savePath = './Public/upload/case/';
		//$upload->saveRule = true;
		if (! $upload->upload ()) {
			$this->error ( $upload->getErrorMsg () );
		} else {
			$uploadList = $upload->getUploadFileInfo ();
			return $uploadList;
		}
	}


    /**
     +----------------------------------------------------------
     * @执行添加案例操作
     +----------------------------------------------------------
    */
    public function insertCase(){
        $Case =D("Case");
        $data['name_1']=$_POST['name_1'];
        $data['cate_pid']=$_POST['cate_pid'];
        $data['cate_id']=$_POST['cate_id'];
        $data['desinger_id']=$_POST['desinger_id'];
        $data['area']=$_POST['area'];
        $data['styles']=$_POST['styles'];
        $data['tags']=$_POST['tags'];
        $data['budget']=$_POST['budget'];
        $data['create_time']=time();
        $data['update_time']=time();
        $data['content_1']=$_POST['content_1'];
        $data['uid']=$_SESSION['uid'];
        $data['status']=1;
        $case =$Case ->add($data);
        if($case){
            $Model = new Model (); // 实例化一个model对象 没有对应任何数据表
			$Model->execute ( "update xc_pic_gallery set pic_id='" .$case. "' where pic_id=0" );
            $this->saveTag($_POST["tags"],$case,'Case');
            $this->assign("jumpUrl","__APP__/Company/mycase");
            $this->success('添加数据成功!');
        }else{
            $this->error('添加数据失败!');
        }

    }
    /**
     +----------------------------------------------------------
     * @编辑案例
     +----------------------------------------------------------
    */
    public function editCase(){
        $Case =D("Case");
        $case =$Case->getById($_GET['id']);
        $PicGallery = D ( 'PicGallery' );
        $pwhere['pic_id']=$_GET['id'];
        $pwhere['model']='Case';
        $pic =$PicGallery->where($pwhere)->select();
        $this->assign('vo',$case);
        $this->assign('pic',$pic);

        $Category =D("Category");
        $cate =$Category->where('pid=0')->select();
        //dump($cate);
        $Company=D("Company");
        $cmap['uid']=$_SESSION['uid'];
        $com =$Company->where($cmap)->find();
        //echo $Company->getlastsql();
        //dump($com);
        $Designer=D("Designer");
        $dwhere['com_id']=$com['id'];
        $designer =$Designer->where($dwhere)->select();
        //echo $Designer->getlastsql();
        //dump($designer);
        $this->assign ( "cate", $cate );
        $this->assign ( "designer", $designer );
        $this->display();
    }
     /**
     +----------------------------------------------------------
     * @编辑案例上传案例图片
     +----------------------------------------------------------
    */
    public function _before_updateCase(){
        $PicGallery = D ( 'PicGallery' );

		if (! empty ( $_FILES ['images'] ['name'] ['0'] )) {

			$uploadList = $this->_uploads ( $_FILES );
			for($i = 0; $i < count ( $uploadList ); $i ++) {
				$data ['model'] = 'Case';
				$data ['pic_id'] = $_POST['id'];
				$data ['images'] = $uploadList [$i] ['savename'];
				$data ['img_name'] = $_POST ['imgname'] [$i];
				$data ["create_time"] = time ();
				$result = $PicGallery->add ( $data );
			}
		}
    }
    /**
     +----------------------------------------------------------
     * @执行案例更新操作
     +----------------------------------------------------------
    */
    public function updateCase(){
        $Case =D("Case");
        $data['name_1']=$_POST['name_1'];
        $data['cate_pid']=$_POST['cate_pid'];
        $data['cate_id']=$_POST['cate_id'];
        $data['desinger_id']=$_POST['desinger_id'];
        $data['area']=$_POST['area'];
        $data['styles']=$_POST['styles'];
        $data['tags']=$_POST['tags'];
        $data['budget']=$_POST['budget'];
        $data['update_time']=time();
        $data['content_1']=$_POST['content_1'];
        $data['id']=$_POST['id'];

        $case =$Case ->save($data);
        if($case){
            $this->saveTag($_POST["tags"],$_POST['id'],'Case');
            $this->assign("jumpUrl","__APP__/Company/mycase");
            $this->success('修改数据成功!');
        }else{
            $this->error('修改数据失败!');
        }
    }
    /**
     +----------------------------------------------------------
     * @设计师列表
     +----------------------------------------------------------
    */
    public function designer(){
        $Designer=D("Designer");


        $Company=D("Company");
        $cmap['uid']=$_SESSION['uid'];
        $com =$Company->where($cmap)->find();
        $wmap['com_id']=$com['id'];
        import ( "@.ORG.Pages" );
        //--------最新相册---------
        $count = $Designer->where($wmap)->count ('id');
        $p = new Page ( $count, 10 );
        $designer = $Designer->where($wmap)->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();

        $page = $p->show ();
        $this->assign ( "designer", $designer );
        $this->assign ( "page", $page );
        $this->display();

    }
    /**
     +----------------------------------------------------------
     * @添加设计师
     +----------------------------------------------------------
    */
    public function addDesigner(){
        $this->assign('title','添加设计师');
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * @执行设计师添加操作
     +----------------------------------------------------------
    */
    public function insertDesigner(){

        if(!empty($_FILES['image']['tmp_name'])){

            if(eyooC("WATER_MARK")){
                //  echo 'ok';
    	    	$result = $this->uploadFile(1,'designer',1,160,120,110,80,true);
    	    }else{

    		    $result = $this->uploadFile(1,'designer',1,160,120,110,80,true);
            }
            $res = $result['uploadList'];
           // dump( $res);
            //$uploadList = $this->_Aupload ( $_FILES );
            $data ['image'] = $res[0]['savename'];
           // exit;
        }
        $Company=D("Company");
        $cmap['uid']=$_SESSION['uid'];
        $com =$Company->where($cmap)->find();
        $Designer=D("Designer");
        $data['name_1']=$_POST['name_1'];
        $data['com_id']=$com['id'];
        $data['uid']=$_SESSION['uid'];
        $data['cost']=$_POST['cost'];
        $data['good_style']=$_POST['good_style'];
        $data['mobile']=$_POST['mobile'];
        $data['good_project']=$_POST['good_project'];
        $data['good_unit']=$_POST['good_unit'];
        $data['work_years']=$_POST['work_years'];
        $data['create_time']=time();
        $data['update_time']=time();
        $data['status']=1;
        $data['content_1']=htmlCv($_POST['content_1']);

        $article =$Designer->add($data);


        if($article){
            $this->assign("jumpUrl","__APP__/Company/designer");
            $this->success('数据添加成功!');
        }else{
            $this->error('数据添加失败!');
        }


    }
    /**
     +----------------------------------------------------------
     * @修改设计师
     +----------------------------------------------------------
    */
    public function editDesigner(){
        $id=$_GET['id'];
        $Designer=D("Designer");
        $vo=$Designer->getById($id);
        $this->assign('vo',$vo);
        $this->display();
    }

     /**
     +----------------------------------------------------------
     * @执行优惠活动编辑操作
     +----------------------------------------------------------
    */
    public function updateDesigner(){

        if(!empty($_FILES['image']['tmp_name']['0'])){
             if(eyooC("WATER_MARK")){
                //  echo 'ok';
    	    	$result = $this->uploadFile(1,'designer',1,160,120,110,80,true);
    	    }else{

    		    $result = $this->uploadFile(1,'designer',1,160,120,110,80,true);
            }
            $res = $result['uploadList'];
            $data ['image'] = $res [0] ['savename'];
        }

       $Designer=D("Designer");
        $data['name_1']=$_POST['name_1'];

        $data['cost']=$_POST['cost'];
        $data['good_style']=$_POST['good_style'];
        $data['mobile']=$_POST['mobile'];
        $data['good_project']=$_POST['good_project'];
        $data['good_unit']=$_POST['good_unit'];
        $data['work_years']=$_POST['work_years'];
        $data['update_time']=time();

        $data['content_1']=htmlCv($_POST['content_1']);
        $data['id']=$_POST['id'];
        $article =$Designer->save($data);

        if($article){
            $this->assign("jumpUrl","__APP__/Company/designer");
            $this->success('数据编辑成功!');
        }else{
            $this->error('数据编辑失败!');
        }

    }
    /**
     +----------------------------------------------------------
     * @删除设计师
     +----------------------------------------------------------
    */
    public function delDesigner(){
        $id=$_GET['id'];
        $Designer=D("Designer");
        $amap['id']=$id;
        $vo=$Designer->where($amap)->delete();
        if($vo){
            $this->success("数据删除成功！");
        }else{
            $this->error("数据删除失败！");
        }
    }
    /**
     +----------------------------------------------------------
     * @在建工地
     +----------------------------------------------------------
    */
    public function construction(){
        $Construction=D("Construction");
        $wmap['uid']=$_SESSION['uid'];
        import ( "@.ORG.Pages" );
        //--------最新相册---------
        $count = $Construction->where($wmap)->count ('id');
       $listRows = empty ( $_REQUEST ["listRows"] ) ? 10 : $_REQUEST ["listRows"];
        $p = new Page ( $count, $listRows );
        $construction = $Construction->where($wmap)->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();

        $page = $p->show ();
        $this->assign ( "construc", $construction );
        $this->assign ( "page", $page );
        $this->display();

    }
    /**
     +----------------------------------------------------------
     * @添加在建工地
     +----------------------------------------------------------
    */
    public function addConstruction(){
        $this->assign('title','添加在建工地');

        $this->display();
    }
    /**
     +----------------------------------------------------------
     * @执行在建工地添加操作
     +----------------------------------------------------------
    */
    public function insertCons(){
        $Construction=D("Construction");
        $data['name_1']=$_POST['name_1'];
        $data['area']=$_POST['area'];
        $data['root_unit']=$_POST['root_unit'];
        $data['budget']=$_POST['budget'];
        $data['visit_time']=$_POST['visit_time'];
        $data['create_time']=time();
        $data['uid']=$_SESSION['uid'];
        $cons =$Construction->add($data);

        if($cons){
            $this->assign("jumpUrl","__APP__/Company/construction");
            $this->success('添加数据成功!');
        }else{
            $this->error('添加数据失败!');
        }
    }
    /**
     +----------------------------------------------------------
     * @修改在建工地
     +----------------------------------------------------------
    */
    public function editConstruction(){
         $Construction=D("Construction");
         $id=$_GET['id'];
         $cons =$Construction->getById($id);
         if($cons){
             $this->assign('vo',$cons);
             $this->display();
         }else{
            $this->error('非法操作!');
         }

    }
    /**
     +----------------------------------------------------------
     * @执行在建工地修改操作
     +----------------------------------------------------------
    */
    public function updateCons(){
        $Construction=D("Construction");
        $data['id']=$_POST['id'];
        $data['name_1']=$_POST['name_1'];
        $data['area']=$_POST['area'];
        $data['root_unit']=$_POST['root_unit'];
        $data['budget']=$_POST['budget'];
        $data['visit_time']=$_POST['visit_time'];
        $data['update_time']=time();

        $cons =$Construction->save($data);

        if($cons){
            $this->assign("jumpUrl","__APP__/Company/construction");
            $this->success('修改数据成功!');
        }else{
            $this->error('修改数据失败!');
        }
    }
    /**
     +----------------------------------------------------------
     * @删除在建工地
     +----------------------------------------------------------
    */
    public function delConstruction(){
        $id=$_GET['id'];
        $Construction=D("Construction");
        $amap['id']=$id;
        $vo=$Construction->where($amap)->delete();
        if($vo){
            $this->success("数据删除成功！");
        }else{
            $this->error("数据删除失败！");
        }
    }
    /**
     +----------------------------------------------------------
     * @荣誉证书列表
     +----------------------------------------------------------
    */
    public function certificate(){
        $Certificate =D("Certificate");
        $wmap['uid']=$_SESSION['uid'];
        import ( "@.ORG.Pages" );
        //--------最新相册---------
        $count = $Certificate->where($wmap)->count ();
        $listRows = empty ( $_REQUEST ["listRows"] ) ? 10 : $_REQUEST ["listRows"];
        $p = new Page ( $count, $listRows );
        $certificate = $Certificate->where($wmap)->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();

        $page = $p->show ();
        $this->assign ( "certificate", $certificate );
        $this->assign ( "page", $page );
        $this->display();

    }
    /**
     +----------------------------------------------------------
     * @添加荣誉证书
     +----------------------------------------------------------
    */
    public function addCertificate(){

        $this->assign('title','添加荣誉证书');
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * @执行荣誉证书操作
     +----------------------------------------------------------
    */
    public function insertCertificate(){
        if(!empty($_FILES['image']['tmp_name'])){

            if(eyooC("WATER_MARK")){
                //  echo 'ok';
    	    	$result = $this->uploadFile(1,'certificate',1,326,163,160,90,true);
    	    }else{

    		    $result = $this->uploadFile(1,'certificate',1,326,163,160,90,true);
            }
            $res = $result['uploadList'];

            //$uploadList = $this->_Aupload ( $_FILES );
            $data ['image'] = $res[0]['savename'];

        }
        $Certificate=D("Certificate");
        $data['name']=$_POST['name'];
        $data['create_time']=time();
        $data['status']=1;
        $data['content']=htmlCv($_POST['content']);
        $data['uid']=$_SESSION['uid'];
        $article =$Certificate->add($data);


        if($article){
            $this->assign("jumpUrl","__APP__/Company/certificate");
            $this->success('数据添加成功!');
        }else{
            $this->error('数据添加失败!');
        }


    }
    /**
     +----------------------------------------------------------
     * @修改荣誉证书
     +----------------------------------------------------------
    */
    public function editCertificate(){
        $id=$_GET['id'];
        $Certificate=D("Certificate");
        $vo=$Certificate->getById($id);
        $this->assign('vo',$vo);
        $this->assign('title','修改荣誉证书');
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * @执行修改操作
     +----------------------------------------------------------
    */
    public function updateCertificate(){
        if(!empty($_FILES['image']['tmp_name']['0'])){
           if(eyooC("WATER_MARK")){
                //  echo 'ok';
    	    	$result = $this->uploadFile(1,'certificate',1,326,163,160,90,true);
    	    }else{

    		    $result = $this->uploadFile(1,'certificate',1,326,163,160,90,true);
            }
            $res = $result['uploadList'];
            $data ['image'] = $res [0] ['savename'];
        }

        $Certificate=D("Certificate");
        $data['id']=$_POST['id'];
        $data['name']=$_POST['name'];
        $data['update_time']=time();
        $data['content']=htmlCv($_POST['content']);

        $article =$Certificate->save($data);

        if($article){
            $this->assign("jumpUrl","__APP__/Company/certificate");
            $this->success('数据编辑成功!');
        }else{
            $this->error('数据编辑失败!');
        }

    }
    /**
     +----------------------------------------------------------
     * @删除荣誉证书
     +----------------------------------------------------------
    */
    public function delCertificate(){
        $id=$_GET['id'];
        $Certificate=D("Certificate");
        $amap['id']=$id;
        $vo=$Certificate->where($amap)->delete();
        if($vo){
            $this->success("数据删除成功！");
        }else{
            $this->error("数据删除失败！");
        }

    }
    //修改密码
    public function password(){
     $this->checkUser();
     //读取公告信息
        $article =D("Article")->where('cate_id=22')->limit('10')->order('id desc')->select();

        $this->assign('user',$user);
         //dump($article);
        if($article!=false){
            $this->assign('article',$article);
        }
        $this->display();
    }

	 //执行修改密码
    public function doPasswd(){
        $this->checkUser();
        $password=trim($_POST['oldpws']);
        $id=$_SESSION['uid'];
        $user =D('User')->getById($id);
        if($user['user_pwd']==md5($password)){
            $data['id']=$id;
            $data['user_pwd']=md5($_POST['newPassword']);
            $users =D('User')->save($data);
            //echo D('User')->getlastsql();
            if($users){
                $this->success ('修改用户密码成功！');
            }else{
                 $this->error ('修改用户密码失败！');
            }
        }else{
            $this->error ('输入的旧密码不正确');
        }

    }
    /**
     +----------------------------------------------------------
     * @公司列表
     +----------------------------------------------------------
    */
    public function showlist(){
        $Company =D("CompanyView");
        if(!empty($_GET['service_area'])){

            if($_GET['service_area']==1){
                $_SESSION['service_area']='';
            }else{
                $cmap['service_area']=array('like',"%{$_GET['service_area']}%");
                $_SESSION['service_area']=$_GET['service_area'];
            }
        }else{
            if(!empty($_SESSION['service_area'])){
                $cmap['service_area']=array('like',"%{$_SESSION['service_area']}%");
            }
        }
        if(!empty($_GET['business_scope'])){

            if($_GET['business_scope']==4){
                 $_SESSION['business_scope']='';
            }else{
                 $cmap['business_scope']=array('like',"%{$_GET['business_scope']}%");
                $_SESSION['business_scope']=$_GET['business_scope'];
            }
        }else{
            if(!empty($_SESSION['business_scope'])){
                $cmap['business_scope']=array('like',"%{$_SESSION['business_scope']}%");
            }
        }

         if(!empty($_GET['decoration_pattern'])){

            if($_GET['decoration_pattern']==5){
                 $_SESSION['decoration_pattern']='';
            }else{
                 $cmap['decoration_pattern']=array('like',"%{$_GET['decoration_pattern']}%");
                $_SESSION['decoration_pattern']=$_GET['decoration_pattern'];
            }
        }else{
            if(!empty($_SESSION['decoration_pattern'])){
                $cmap['decoration_pattern']=array('like',"%{$_SESSION['decoration_pattern']}%");
            }
        }

         if(!empty($_GET['good_style'])){

            if($_GET['good_style']==3){
                 $_SESSION['good_style']='';
            }else{
                $cmap['good_style']=array('like',"%{$_GET['good_style']}%");
                $_SESSION['good_style']=$_GET['good_style'];
            }
        }else{
            if(!empty($_SESSION['good_style'])){
                $cmap['good_style']=array('like',"%{$_SESSION['good_style']}%");
            }
        }

        if(!empty($_GET['main_price'])){

            if($_GET['main_price']==10){
                 $_SESSION['main_price']='';
            }else{
                $cmap['main_price']=$_GET['main_price'];
                $_SESSION['main_price']=$_GET['main_price'];
            }
        }else{
            if(!empty($_SESSION['main_price'])){
                $cmap['main_price']=$_SESSION['main_price'];
            }
        }

        $cmap['type']=2;
        $cmap['active']=1;
        $cmap['is_top']=0;
        import("@.ORG.Pages");

        $count = $Company->where($cmap)->count('id');
        $listRows = empty ( $_REQUEST ["listRows"] ) ? 20 : $_REQUEST ["listRows"];
        $p = new Page ( $count, $listRows );
        $list = $Company->where($cmap)->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();
        //echo $Company->getlastsql();
        $this->assign('list',$list);
        $page = $p->show ();
        $this->assign('page',$page);
        //促销活动
        $Article=D("News");

        $newslist = $Article->where($map)->limit('6')->order('id desc')->select();

        $cmap1['type']=2;
        $cmap1['active']=1;
        $cmap1['is_top']=1;
        $top= $Company->where($cmap1)->limit(3)->order('id desc')->select();

        $this->assign('top',$top);
        //dump();
        $this->assign('newslist',$newslist);
        $this->assign('title','装修公司列表');
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * @服务承诺
     +----------------------------------------------------------
    */
    public function service(){
        $Company =D("Company");
        $cmaps['uid']=$_SESSION['uid'];
        $com =$Company->where($cmaps)->find();
        $this->assign('com',$com);
        $this->assign('title','服务承诺');
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * @执行修改服务承诺
     +----------------------------------------------------------
    */
    public function insertService(){
        $Company =D("Company");
        $data['service']=$_POST['content_1'];
        $cmaps['uid']=$_SESSION['uid'];
        $company =$Company->where($cmaps)->save($data);
        if($company!=false){
            $this->success('数据修改成功!');
        }else{
            $this->error('数据修改失败!');
        }

    }

    /**
     +----------------------------------------------------------
     * @联系方式
     +----------------------------------------------------------
    */
    public function contact(){
        $Company =D("Company");
        $cmaps['uid']=$_SESSION['uid'];
        $com =$Company->where($cmaps)->find();
        $this->assign('com',$com);
        $this->assign('title','服务承诺');
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * @执行修改联系方式
     +----------------------------------------------------------
    */
    public function insertContact(){
        $Company =D("Company");
        $data['contact']=$_POST['content_1'];
        $cmaps['uid']=$_SESSION['uid'];
        $company =$Company->where($cmaps)->save($data);
        if($company!=false){
            $this->success('数据修改成功!');
        }else{
            $this->error('数据修改失败!');
        }

    }

    /**
     +----------------------------------------------------------
     * @预约工地
     +----------------------------------------------------------
    */
    public function reservation(){
        $ReservationSite =D("ReservationSite");

        $reservationmap['uid']=$_SESSION['uid'];
        import("@.ORG.Pages");

        $count = $ReservationSite->where($reservationmap)->count('id');
        $listRows = empty ( $_REQUEST ["listRows"] ) ? 10 : $_REQUEST ["listRows"];
        $p = new Page ( $count, $listRows );
        $list = $ReservationSite->where($reservationmap)->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();
        $this->assign('list',$list);
        $page = $p->show ();
        $this->assign('page',$page);

        $this->display();
    }
    /**
     +----------------------------------------------------------
     * @预约管理
     +----------------------------------------------------------
    */
    public function book(){
        $Booking =D("Booking");

        $reservationmap['select_uid']=$_SESSION['uid'];
        import("@.ORG.Pages");

        $count = $Booking->where($reservationmap)->count('id');
        $listRows = empty ( $_REQUEST ["listRows"] ) ? 10 : $_REQUEST ["listRows"];
        $p = new Page ( $count, $listRows );
        $list = $Booking->where($reservationmap)->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();
        $this->assign('list',$list);
        $page = $p->show ();
        $this->assign('page',$page);
        $this->assign('title','预约管理');
        $this->display();
    }
    /**
     +----------------------------------------------------------
     * @删除预约
     +----------------------------------------------------------
    */
    public function delBook(){
        $id=$_POST['id'];
        $Booking =D("Booking");
        $res =$Booking->where('id='.$id)->delete();
        if($res){
            echo '1';
        }else{
            echo '0';
        }

    }

     /**
     +----------------------------------------------------------
     * @收藏管理
     +----------------------------------------------------------
    */
    public function watchlist(){
        $Watchlist =D("Watchlist");

        $reservationmap['select_uid']=$_SESSION['uid'];
        import("@.ORG.Pages");

        $count = $Watchlist->where($reservationmap)->count('id');
        $listRows = empty ( $_REQUEST ["listRows"] ) ? 10 : $_REQUEST ["listRows"];
        $p = new Page ( $count, $listRows );
        $list = $Watchlist->where($reservationmap)->limit($p->firstRow . ',' . $p->listRows)->order('id desc')->select();
        $this->assign('list',$list);
        $page = $p->show ();
        $this->assign('page',$page);
        $this->assign('title','收藏管理');
        $this->display();
    }
}
?>