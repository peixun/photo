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

class PhotoAlbumAction extends CommonAction {
	public function delete() {
		
		$PicGallery = D ( 'PicGallery' );
		$where ["id"] = $_GET ["id"];
		$result = $PicGallery->where ( $where )->delete ();
		if ($result) {
			$this->success ( "删除成功!" );
		} else {
			$this->error ( "删除失败!" );
		}
	}
	//相册添加
	public function _before_insert() {
		$PicGallery = D ( 'PicGallery' );
		
		if (! empty ( $_FILES ['images'] ['name'] ['0'] )) {
			//echo 'login';
			//exit;
			$uploadList = $this->_upload ( $_FILES );
			
			for($i = 0; $i < count ( $uploadList ); $i ++) {
				
				$_POST ['albumimg'] = $uploadList [$i] ['savename'];
			
		//echo $PicGallery ->getlastsql();
			//exit;
			}
			$_POST ["create_time"] = time ();
		}
	}
	
	//相片编辑
	public function _before_updates() {
		
		$PicGallery = D ( 'PicGallery' );
		if (! empty ( $_FILES ['images'] ['name'] ['0'] )) {
			//echo 'login';
			//exit;
			$uploadList = $this->_upload ( $_FILES );
			for($i = 0; $i < count ( $uploadList ); $i ++) {
				$data ['model'] = 'Photo';
				$data ['pic_id'] = 0;
				$data ['images'] = $uploadList [$i] ['savename'];
				$data ["create_time"] = time ();
				$data ['img_name'] = $_POST ['imgname'] [$i];
				$result = $PicGallery->add ( $data );
			
		//echo $PicGallery ->getlastsql();
			//exit;
			}
		}
	}
	public function edits() {
			 
		//$name = $this->getActionName();  
		$id = $_REQUEST ["id"];
		$photo = D ( 'PicGallery' );
		$map ['pic_id'] = $id;
		$map ['model'] = 'Photo';
		$photoList = $photo->where ( $map )->select ();
		//dump($photoList);
		

		$this->assign ( 'id', $id );
		$this->assign ( 'photoList', $photoList );
		$this->display ();
	}
	public function _before_update() {
		
		if (! empty ( $_FILES ['images'] ['name'] ['0'] )) {
			//echo 'login';
			//exit;
			$uploadList = $this->_upload ( $_FILES );
			
			for($i = 0; $i < count ( $uploadList ); $i ++) {
				
				$_POST ['albumimg'] = $uploadList [$i] ['savename'];
			
		//echo $PicGallery ->getlastsql();
			//exit;
			}
			$_POST ["update_time"] = time ();
		}
	}
	
	public function updates() {
		 
		$id = $_POST ['id'];
		$Model = new Model (); // ʵ��һ��model���� û�ж�Ӧ�κ���ݱ�
		$Model->execute ( "update xc_pic_gallery set pic_id='" . $id . "' where pic_id=0" );
		if ($Model) {
			$this->success ( "编辑成功!" );
		} else {
			$this->success ( "编辑失败!" );
		}
	}
	
	public function inserts() {
		
		$News = D ( "News" );
		$data ["title"] = $_POST ["title"];
		$data ["content"] = $_POST ["content"];
		$data ["status"] = 1;
		$data ["create_time"] = time ();
		$results = $News->add ( $data );
		if ($results) {
			$id = $News->getLastInsID ();
			
			$Model = new Model (); // ʵ��һ��model���� û�ж�Ӧ�κ���ݱ�
			

			$Model->execute ( "update xc_pic_gallery set pic_id='" . $id . "' where pic_id=0" );
			//exit; 
			$this->success ( "编辑成功!" );
		} else {
			$this->success ( "编辑失败!" );
		}
	
	}
	function _upload() {
		$upload = new UploadFile ();
		//�����ϴ��ļ���С
		$upload->maxSize = 3292200;
		//�����ϴ��ļ�����
		$upload->allowExts = explode ( ',', 'jpg,gif,png,jpeg,rar' );
		//���ø����ϴ�Ŀ¼
		$upload->thumb = true;
		$upload->thumbPrefix = 'm_';
		$upload->thumbMaxHeight = '270';
		$upload->thumbMaxWidth = '270';
		//�����ϴ��ļ�����
		$upload->saveRule = 'uniqid';
		//����ͬ���ļ�����,���ϴ���ͬͼƬ
		$upload->uploadReplace = 'true';
		//ɾ��ԭͼ·��
		$upload->thumbRemoveOrigin = false;
		
		$upload->savePath = './Public/upload/album/';
		//$upload->saveRule = true;
		if (! $upload->upload ()) {
			$this->error ( $upload->getErrorMsg () );
		} else {
			$uploadList = $upload->getUploadFileInfo ();
			return $uploadList;
		}
	}
	//设置封面照片
	function getalbumimg()
	{  
		$PhotoAlbum = D("PhotoAlbum"); 
	 
		$sql = "update xc_photo_album set albumimg='{$_GET["img"]}' ,update_time = ".time()." where id ={$_GET["id"]}";
		$result = $PhotoAlbum->execute($sql);
		 
		if($result)
		{
			$this->success("设为封面成功!");
		}else
		{
			$this->error("设为封面成功	!");
		}
	}

}
?>
