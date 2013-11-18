<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2007 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

// 后台用户模块
class FlashimgAction extends CommonAction {


	function insert() {
		$model =  D("Flashimg");
		$data['title'] = $_POST['title'];
		$data['url'] = $_POST['url'];
		if(!empty($_POST['uploadList'])){
			$data['image'] = $_POST['uploadList'][0]['savename'];
			$data['create_time'] = time();
			$data['update_time'] = time();
			$result = $model->add($data);
			if($result===false) {
				$this->error('添加失败');
			}else {
				$this->success('添加成功！');
			}
		}else{
			$this-error("请选择一张图片");
		}
	}


	function _before_add(){
		$res = M('Flashimg')->order('sort desc')->find();
		$sort = intval($res['sort'])+1;
		$this->assign('sort',$sort);
	}


/*
	function insert() {
		$model =  D("Flashimg");
		$data['title'] = $_POST['title'];
		$data['small'] = $_POST['small'];

		$cou = count($_POST['uploadList']);
		if($cou == 2){
			for( $i=0; $i<=count($_POST['uploadList']) ; $i++)
			{
				if($_POST['uploadList'][$i]['key'] == 'image'){

					$data['image'] = $_POST['uploadList'][$i]['savename'];
				}
				if($_POST['uploadList'][$i]['key'] == 'small_image'){

					$data['small_image'] = $_POST['uploadList'][$i]['savename'];
				}
			}
		}

		$data['create_time'] = time();
		$data['update_time'] = time();
		$result = $model->add($data);

		if($result===false) {
			$this->error('添加失败');
		}else {
			$this->success('添加成功！');
		}

	}*/


function update() {
	$model =  D("Flashimg");
	$data['id'] = $_POST['id'];
	$data['title'] = $_POST['title'];
    $data['url'] = $_POST['url'];
	$data['sort'] =$_POST['sort'];
	if(!empty($_POST['uploadList'])){

		$data['image'] = $_POST['uploadList'][0]['savename'];
	}
	$result = $model->save($data);

	if($result===false) {
		$this->error('编辑失败');
	}else {
		$this->success('编辑成功！');
	}
}

	/*function update() {
		$model =  D("Flashimg");
		$data['id'] = $_POST['id'];
		$data['title'] = $_POST['title'];
		$data['small'] = $_POST['small'];
		$cou = count($_POST['uploadList']);
		if($cou == 2){
			for( $i=0; $i<=count($_POST['uploadList']) ; $i++)
			{
				if($_POST['uploadList'][$i]['key'] == 'image'){

					$data['image'] = $_POST['uploadList'][$i]['savename'];
				}
				if($_POST['uploadList'][$i]['key'] == 'small_image'){

					$data['small_image'] = $_POST['uploadList'][$i]['savename'];
				}
			}
		}
		$result = $model->save($data);

		if($result===false) {
			$this->error('编辑失败');
		}else {
			$this->success('编辑成功！');
		}

	}






	/*
	 +-------------------------------------
		保存图片
		在执行update方法之前执行_before_update
	 +-------------------------------------
	*/
		public function _before_insert() {
		if(!empty($_FILES['image']['tmp_name']) ) {
			import("@.ORG.UploadFile");
			$upload = new UploadFile();
			$upload->maxSize  = eyooC('MAX_UPLOAD') ;
			$upload->saveRule='uniqid';
			$upload->allowExts  = explode(',','jpg,gif,png,jpeg');
				$upload->thumb  =  true;
				$upload->thumbPrefix ='m_' ;
				$upload->thumbMaxHeight = '300';
				$upload->thumbMaxWidth =  '370';
				//删除原图路径
				$upload->thumbRemoveOrigin = false;
			//上传图片
			$upload->savePath = './Public/upload/Flashimg/';
			if(!$upload->upload()) {
				//捕获上传异常
				$this->error($upload->getErrorMsg());
			}else {
				//取得成功上传的文件信息
				$uploadList = $upload->getUploadFileInfo();
				//dump($uploadList); exit();
				//$_POST['image']  = $uploadList[0]['savename'];
				$_POST['uploadList'] = $uploadList;
			}
		}
	}


	public function _before_update() {
		if(!empty($_FILES['image']['tmp_name']) ) {
			import("@.ORG.UploadFile");
			$upload = new UploadFile();
			$upload->maxSize  = eyooC('MAX_UPLOAD') ;
			$upload->saveRule='uniqid';
			$upload->allowExts  = explode(',','jpg,gif,png,jpeg');
				$upload->thumb  =  true;
				$upload->thumbPrefix ='m_' ;
				$upload->thumbMaxHeight = '300';
				$upload->thumbMaxWidth =  '370';
				//删除原图路径
				$upload->thumbRemoveOrigin = false;
			//上传图片
			$upload->savePath = './Public/upload/Flashimg/';
			if(!$upload->upload()) {
				//捕获上传异常
				$this->error($upload->getErrorMsg());
			}else {
				//取得成功上传的文件信息
				$uploadList = $upload->getUploadFileInfo();
				//dump($uploadList); exit();
				//$_POST['image']  = $uploadList[0]['savename'];
				$_POST['uploadList'] = $uploadList;
			}
		}
	}







//	 public function _before_update() {
//        // 检测图片
//        if(!empty($_FILES['image']['tmp_name']) || !empty($_FILES['small_image']['tmp_name'])) {
//            //执行上传操作
//            import("@.ORG.UploadFile");
//			$upload = new UploadFile();
//            $upload->maxSize  = eyooC('MAX_UPLOAD') ;
//            //$upload->maxSize  = C('UPLOAD_MAX_SIZE') ;
//            $upload->allowExts  = explode(',','jpg,gif,png,jpeg');
//            $upload->thumb  =  false;
//            //$upload->thumbPrefix   =  'm_,s_';
//            //$upload->thumbMaxHeight = '518,97';
//            //$upload->thumbMaxWidth =  '314,129';
//            $upload->savePath = './Public/upload/Flashimg/';
//			$upload->saveRule='uniqid';
//
//            if(!$upload->upload()){
//                //捕获上传异常
//                $this->error($upload->getErrorMsg());
//            }else {
//                 //取得成功上传的文件信息
//                $uploadList = $upload->getUploadFileInfo();
//				dump($uploadList);
//               // $_POST['image']  = $uploadList[0]['savename'];
//				 $_POST['image']  = $uploadList;
//            }
//        }else{
//            unset($_POST['image']);
//        }
//    }
}
?>