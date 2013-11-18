<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 商品
class CompanyAction extends CommonAction{
	//增

    public function about()
	{
		$name=$this->getActionName();
		$model = D ($name);
		$vo=$model->find(3);
		$this->assign('vo',$vo);
		$this->display();
	}

	public function doAbout()
	{

		$data['id']=3;
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}

		//保存当前数据对象
		$newgoods_id = $model->save($data);
		if($newgoods_id){
			$this->success (L('EDIT_SUCCESS')." ".$mail_msg);
		}else{
			$this->error (L('EDIT_FAILED'));
		}
	}

    function update(){
         $Company =D("Company");
         if (false === $Company->create ()) {
				$this->error ( $Company->getError () );
		 }
         $Company->status=1;

         $logo =$this->upload_logo();
         $Company->update_time=time();

         if(!empty($logo)){
            $Company->logo=$logo;
         }


         $Company->update_time=time();
	     $result = $Company->save();

         if($result){
            $msg = '编辑公司信息:'.$names;
            $this->saveLog(1,0,$msg);
           $this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ( "公司信息修改成功!" );
		 } else {
              $msg = '编辑公司信息:'.$names;
            $this->saveLog(0,0,$msg);
			$this->error ( "公司信息修改失败!" );
		 }

    }

     public function upload_logo() {
        $paths ='./Public/upload/logo/thum/';
        $paths1 ='./Public/upload/logo/photo/';
        if (!is_dir($paths)) @mk_dir($paths);
        if (!is_dir($paths1)) @mk_dir($paths1);
        //dump($_FILES);
        if(!empty($_FILES['logo']['tmp_name'])){
            $uploadList = $this->_uploadss($_FILES);
        // dump($uploadList);
            $logo = $uploadList['0']['savename'];
            imagezoom($paths1.$logo,  $paths.$logo, 988, 98, '#f0f0f0');
            return $logo;
        }
        }

    function _uploadss() {
        import("@.ORG.UploadFile");
		$upload = new UploadFile ();
		//设置上传文件大小
		$upload->maxSize  = '1000201020102012010201' ;
		//设置上传文件类型
		$upload->allowExts = explode ( ',', 'jpg,gif,png,jpeg' );
		//设置附件上传目录
		$upload->thumb = true;
        $upload->thumbPrefix ='m_' ;
        $upload->thumbMaxHeight = '98';
        $upload->thumbMaxWidth =  '988';
		//设置上传文件规则
		$upload->saveRule = 'uniqid';
		//存在同名文件覆盖,可上传相同图片
		$upload->uploadReplace = 'true';
		//删除原图路径
		$upload->thumbRemoveOrigin = false;

		$upload->savePath = './Public/upload/logo/photo/';
		//$upload->saveRule = true;
		if (! $upload->upload ()) {
			$this->error ( $upload->getErrorMsg () );
		} else {
         //   dump($_FILES);
			$uploadList = $upload->getUploadFileInfo ();

			return $uploadList;
		}
	}

   function  addpic(){
        $id=$_REQUEST['id'];
        //企业图片列表
        $PicGallery = D ( 'PicGallery' );
        $pmap['pic_id']=$id;
        $pmap['model']='Company';
        $pic =$PicGallery->where($pmap)->order('id asc')->select();
        $this->assign('pic',$pic);
        $this->assign('id',$id);
        $this->display();
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
			$uploadList = $this->_uploadssss ( $_FILES );
			for($i = 0; $i < count ( $uploadList ); $i ++) {
				$data ['model'] = 'Company';
				$data ['pic_id'] = $_POST['id'];
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
     * @上传操作
     +----------------------------------------------------------
    */
    function _uploadssss() {
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

			$this->success ('删除成功!');
		}
		else
		{
			$this->error ('删除失败!');
		}
	}
    function  addqq(){
        $id=$_REQUEST['id'];

        $UserQq =D("UserQq");
        $qmap['uid']=getCompanyUid($id);
        $qq =$UserQq->where($qmap)->order('id desc')->select();

        $this->assign('qq',$qq);
        $this->assign('id',getCompanyUid($id));
        $this->display();
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
            $datas ["uid"] = $_POST['id'];
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
     * @添加企业信息
     +----------------------------------------------------------
    */
    public function add(){
       $User =D('User');
       $user_list =$User->where('type=2')->select();
       $this->assign('user_list',$user_list);
       $this->display();
    }
}
?>