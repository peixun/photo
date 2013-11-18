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

class PhotoAction extends CommonAction {
 public function delete()
   {
 
   	$PicGallery = D('PicGallery');
   	$where["id"]=$_GET["id"];
   	$result = $PicGallery->where($where)->delete();
   	if($result)
   	{
   		$this->success("ɾ��ɹ�!");
   	}else
   	{
   		$this->error("ɾ��ʧ��!");
   	}
   }
	public function _before_insert() {
	$PicGallery = D('PicGallery');

		if(!empty($_FILES['images']['name']['0'])){
			//echo 'login';
			//exit;
			$uploadList = $this->_upload($_FILES);
			for($i=0; $i<count($uploadList); $i++) {
				$data['model']='News';
			    $data ['pic_id'] = 0;
				$data['news_img'] = $uploadList[$i]['savename'];
                $data['img_name'] = $_POST['imgname'][$i];
				$result = $PicGallery->add($data);
				//echo $PicGallery ->getlastsql();
                //exit;
			}
		}
	}
	public function _before_update() {

		$PicGallery = D('PicGallery');

		if(!empty($_FILES['images']['name']['0'])){
			//echo 'login';
			//exit;
			$uploadList = $this->_upload($_FILES);
			for($i=0; $i<count($uploadList); $i++) {
				$data['model']='News';
			    $data ['pic_id'] = 0;
				$data['news_img'] = $uploadList[$i]['savename'];
                $data['img_name'] = $_POST['imgname'][$i];
				$result = $PicGallery->add($data);
				//echo $PicGallery ->getlastsql();
                //exit;
			}
		}
	}
	
	public function update() {
		$News = D ( "News" );
		$data ["title"] = $_POST ["title"];
		$data ["content"] = $_POST ["content"];
		$data ["create_time"] = time ();
		$data["id"] = $_POST["id"];
		$result = $News->save ( $data );
				 
		//echo $model->getlastsql();
		//exit;
		// �������
		if ($result != false) {
			$id = $_POST ['id'];
			$Model = new Model (); // ʵ��һ��model���� û�ж�Ӧ�κ���ݱ�
			$Model->execute ( "update xc_pic_gallery set pic_id='" . $id . "' where pic_id=0" );
	
			//�ɹ���ʾ 
			$this->success ( '���³ɹ���' );
		} else {
			//������ʾ
			$this->error ( '����ʧ�ܣ�' );
		}
	}
	
	public function insert() {
		
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
			$this->success ( '��Ϣ�����ɹ���' );
		} else {
			$this->error ( '��Ϣ����ʧ�ܣ�' );
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
		
		$upload->savePath = './Public/upload/newsimg/';
		//$upload->saveRule = true;
		if (! $upload->upload ()) {
			$this->error ( $upload->getErrorMsg () );
		} else {
			$uploadList = $upload->getUploadFileInfo ();
			return $uploadList;
		}
	}
	
	public function edit() {
		//$name = $this->getActionName();
		$model = D ( "News" );
		$id = $_REQUEST ["id"];
		$vo = $model->getById ( $id );
		
		$photo = D ( 'PicGallery' );
		$map ['pic_id'] = $id;
		$map ['model'] = 'News';
		$photoList = $photo->where ( $map )->select ();
		//dump($photoList);
		$this->assign ( 'vo', $vo );
		$this->assign ( 'photoList', $photoList );
		$this->display ();
	}
  
}
?>
