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

class VNewsAction extends CommonAction {
	
	//-----VVideo视频操作Start----- 
	//视频首页
	public function home() {
		$VVideo = D ( "VVideo" );
		$list = $VVideo->select ();
		
		$this->assign ( "list", $list );
		$this->display ();
	
	}
	//视频添加
	public function vVideoInsert() {
		$VVideo = D ( 'VVideo' );
		$data ["images"] = $_POST ["images"];
		$data ["name"] = $_POST ["name"];
		$data ["video"] = $_POST ["video"];
		$data ["create_time"] = time ();
		
		$result = $VVideo->add ( $data );
		
		if ($result) {
			$this->success ( "新增成功!" );
		} else {
			$this->error ( "新增失败!" );
		}
	}
	//视频更新
	public function vVideoUpdate() {
		$VVideo = D ( 'VVideo' );
		if ($_POST ["images"]) {
			$data ["images"] = $_POST ["images"];
		}
		$data ["id"] = $_POST ["id"];
		$data ["name"] = $_POST ["name"];
		$data ["video"] = $_POST ["video"];
		$data ["update_time"] = time ();
		$result = $VVideo->save ( $data );
		
		if ($result) {
			$this->success ( "更新成功!" );
		} else {
			$this->error ( "更新失败!" );
		}
	}
	//视频添加前运行
	public function _before_vVideoInsert() {
		
		if (! empty ( $_FILES ['images'] ['name'] ['0'] )) {
			$uploadList = $this->_uploads ( $_FILES );
			$_POST ['images'] = $uploadList [0] ['savename'];
		}
	}
	//视频修改前参数处理
	public function _before_vVideoUpdate() {
		
		if (! empty ( $_FILES ['images'] ['name'] ['0'] )) {
			$uploadList = $this->_uploads ( $_FILES );
			$_POST ['images'] = $uploadList [0] ['savename'];
		}
	}
	//获取值跳至更新页面
	function editvvideo() {
		$VVideo = D ( "VVideo" );
		$id = $_REQUEST [$VVideo->getPk ()];
		$vo = $VVideo->getById ( $id );
		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	//视频图片处理方法
	function _uploads() {
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
		
		$upload->savePath = './Public/upload/vvideoimg/';
		//$upload->saveRule = true;
		if (! $upload->upload ()) {
			$this->error ( $upload->getErrorMsg () );
		} else {
			$uploadList = $upload->getUploadFileInfo ();
			return $uploadList;
		}
	}
	//-----VVideo视频操作结束END-----
	

	//-----VNews新闻操作Start-----
	//新闻添加之前
	public function _before_insert() {
		$PicGallery = D ( 'PicGallery' );
		
		if (! empty ( $_FILES ['images'] ['name'] ['0'] )) {
			//echo 'login';
			//exit;
			$uploadList = $this->_upload ( $_FILES );
			for($i = 0; $i < count ( $uploadList ); $i ++) {
				$data ['model'] = 'VNews';
				$data ['pic_id'] = 0;
				$data ['images'] = $uploadList [$i] ['savename'];
				$data ['img_name'] = $_POST ['imgname'] [$i];
				$data ['create_time'] = time ();
				$result = $PicGallery->add ( $data );
			}
		}
	}
	//新闻图片删除
	 public function delete()
	   {
	 
	   	$PicGallery = D('PicGallery');
	   	$where["id"]=$_GET["id"];
	   	$result = $PicGallery->where($where)->delete();
	   	if($result)
	   	{
	   		$this->success("删除成功!");
	   	}else
	   	{
	   		$this->error("删除失败!");
	   	}
	   }
	//新闻修改之前
	public function _before_update() {
		
		$PicGallery = D ( 'PicGallery' );
		
		if (! empty ( $_FILES ['images'] ['name'] ['0'] )) {
			//echo 'login';
			//exit;
			$uploadList = $this->_upload ( $_FILES );
			for($i = 0; $i < count ( $uploadList ); $i ++) {
				$data ['model'] = 'VNews';
				$data ['pic_id'] = 0;
				$data ['images'] = $uploadList [$i] ['savename'];
				$data ['img_name'] = $_POST ['imgname'] [$i];
				$data ['create_time'] = time ();
				$result = $PicGallery->add ( $data );
			
		//echo $PicGallery ->getlastsql();
			//exit;
			}
		}
	}
	//新闻更新获取值
	public function edit() {
		//$name = $this->getActionName();
		$model = D ( "VNews" );
		$id = $_REQUEST ["id"];
		$vo = $model->getById ( $id );
		
		$photo = D ( 'PicGallery' );
		$map ['pic_id'] = $id;
		$map ['model'] = 'VNews';
		$photoList = $photo->where ( $map )->select ();
		//dump($photoList);
		$this->assign ( 'vo', $vo );
		$this->assign ( 'photoList', $photoList );
		$this->display ();
	}
	//新闻修改
	public function update() {
		$VNews = D ( "VNews" );
		$data ["title"] = $_POST ["title"];
		$data ["content"] = $_POST ["content"];
		$data ["create_time"] = time ();
		$data ["id"] = $_POST ["id"];
		$result = $VNews->save ( $data );
		
		//echo $model->getlastsql();
		//exit;
		// 更新数据
		if ($result != false) {
			$id = $_POST ['id'];
			$Model = new Model (); // 实例化一个model对象 没有对应任何数据表
			$Model->execute ( "update xc_pic_gallery set pic_id='" . $id . "' where pic_id=0" );
			
			//成功提示 
			$this->success ( '更新成功！' );
		} else {
			//错误提示
			$this->error ( '更新失败！' );
		}
	}
	//新闻添加
	public function insert() {
		
		$VNews = D ( "VNews" );
		$data ["title"] = $_POST ["title"];
		$data ["content"] = $_POST ["content"];
		$data ["status"] = 1;
		$data ["create_time"] = time ();
		$results = $VNews->add ( $data );
		if ($results) {
			$id = $VNews->getLastInsID ();
			
			$Model = new Model (); // 实例化一个model对象 没有对应任何数据表
			

			$Model->execute ( "update xc_pic_gallery set pic_id='" . $id . "' where pic_id=0" );
			//exit; 
			$this->success ( '信息新增成功！' );
		} else {
			$this->error ( '信息新增失败！' );
		}
	
	}
	//新闻图片处理方法
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
		
		$upload->savePath = './Public/upload/vnewsimg/';
		//$upload->saveRule = true;
		if (! $upload->upload ()) {
			$this->error ( $upload->getErrorMsg () );
		} else {
			$uploadList = $upload->getUploadFileInfo ();
			return $uploadList;
		}
	}
	//-----VNews新闻操作结束END-----
	

	//-----PicGallery图片操作Start-----
	//图片处理方法  取出model=Vphoto的图片
	function vPhotoIndex() {
		$PicGallery = D ( 'PicGallery' );
		$where ["model"] = "vPhoto";
		$list = $PicGallery->where ( $where )->select ();
		if ($list) {
			$this->assign ( "list", $list );
		}
		$this->display ();
	}
	
	//添加图片
	public function vPhotoInsert() {
		$PicGallery = D ( 'PicGallery' );
		
		if (! empty ( $_FILES ['images'] ['name'] ['0'] )) {
			//echo 'login';
			//exit;
			$uploadList = $this->_uploadphoto ( $_FILES );
			for($i = 0; $i < count ( $uploadList ); $i ++) {
				
				$data ['images'] = $uploadList [$i] ['savename'];
				$data ['img_name'] = $_POST ['imgname'] [$i];
				$data ['model'] = 'vPhoto';
				$data ['pic_id'] = 1;
				$data ['create_time'] = time ();
				$result = $PicGallery->add ( $data );
			}
			if ($result) {
				$this->success ( "新增成功!" );
			} else {
				$this->error ( "新增失败!" );
			}
		}
	}
	//图片修改
	public function vPhotoUpdate() {
		
		$PicGallery = D ( 'PicGallery' );
		
		//echo 'login';
		//exit;
		

		if (! empty ( $_FILES ['images'] ['name'] ['0'] )) {
			$uploadList = $this->_uploadphoto ( $_FILES );
			$data ['images'] = $uploadList [0] ['savename'];
		}
		$data ['model'] = 'vPhoto';
		$data ['pic_id'] = 1;
		if ($_POST ["imgname"]) {
			$data ['img_name'] = $_POST ['imgname'];
		}
		$data ['create_time'] = time ();
		$data ["id"] = $_POST ["id"];
		$result = $PicGallery->save ( $data );
	   if($result)
	   {
	   	$this->success("更新成功!");
	   }else
	   {
	   	$this->error("更新失败!");
	   }
		//echo $PicGallery ->getlastsql();
	//exit;
	

	}
	function _uploadphoto() {
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
		
		$upload->savePath = './Public/upload/vphotoimg/';
		//$upload->saveRule = true;
		if (! $upload->upload ()) {
			$this->error ( $upload->getErrorMsg () );
		} else {
			$uploadList = $upload->getUploadFileInfo ();
			return $uploadList;
		}
	}
	function editvphoto() {
		
		$PicGallery = D ( "PicGallery" );
		$id = $_REQUEST ["id"];
		$vo = $PicGallery->getById ( $id );
		$this->assign ( 'vo', $vo );
		$this->display ();
	}

	//-----PicGallery照片操作结束END-----
}
?>
