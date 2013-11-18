<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.top-serve.com
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2012-03-15
 * @Action  Index Action
 * @copyright Copyright &copy; 2009-2012 eyoo Software LLC
 +----------------------------------------------------------
 */

class HuxingAction extends CommonAction {
    public function index()
	{
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		$map['pid'] = array('neq',0);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=("Category");
		//$this->assign('pid',$map['pid']);  //输出当前列表的父地区ID


		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
	}
    public function add(){
        $model =  D("Category");
        $cwhere['pid']=0;
        $cate =$model->where($cwhere)->select();
        $this->assign('cate',$cate);
        $this->display();

    }

    public function edit(){
        $id=$_GET['id'];
        $model =  D("Category");
        $cwhere['pid']=0;
        $cate =$model->where($cwhere)->select();


        $vo =$model->getById($id);

//        dump($vo);
		$CateGallery =D("CateGallery");
		$cmap['pic_id']=$_GET['id'];
		$cmap['model'] ='Huxing';
		$photoList=$CateGallery->where($cmap)->select();

		$this->assign ( "photoList", $photoList );

        $this->assign('cate',$cate);
        $this->assign('vo',$vo);
        $this->display();

    }
    //新增操作
	function insert() {

        $pid =$_POST['cate_id'];
        $model =  D("Category");
        $cates=$model->getById($pid);
        $data['name_1'] = $_POST['name_1'];
        $data['province_id'] = $cates['province_id'];
        $data['city_id'] = $cates['city_id'];
        $data['area_id'] = $cates['area_id'];
        $data['status'] = 1;
        $data['pid'] =  $pid ;
        $data['content_1'] = $_POST['content_1'];
        $data['seokeyword_1'] = $_POST['seokeyword_1'];
        $data['seocontent_1'] = $_POST['seocontent_1'];
        $data['create_time'] = time();
        $data['update_time'] = time();
        $result = $model->add($data);
       // echo $model->getlastsql();
       // exit;
        if($result===false) {
             $msg = '添加户型:'.$names;
            $this->saveLog(0,0,$msg);

            $this->error('添加数据失败');
        }else {
            $Model = new Model (); // 实例化一个model对象 没有对应任何数据表

            $sql= "update `xc_cate_gallery` set pic_id='".$result."' where pic_id=0";
            $Model->execute ($sql);
             $msg = '添加户型:'.$names."ID:".$result;
            $this->saveLog(1,0,$msg);

            $this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
            $this->success('添加数据成功！');
        }

	}
    //更新操作
    function update() {
        $pid =$_POST['cate_id'];
        $model =  D("Category");

        $cates=$model->getById($pid);
        $data['id'] = $_POST['id'];
        $data['name_1'] = $_POST['name_1'];
        $data['province_id'] = $cates['province_id'];
        $data['city_id'] = $cates['city_id'];
        $data['area_id'] = $cates['area_id'];
        $data['content_1'] = $_POST['content_1'];
        $data['pid'] = $pid;
        $data['seokeyword_1'] = $_POST['seokeyword_1'];
        $data['seocontent_1'] = $_POST['seocontent_1'];
        $data['update_time'] = time();

        $result = $model->save($data);

        if($result===false) {
             $msg = '编辑户型:'.$names;
                $this->saveLog(0,0,$msg);
            $this->error('编辑数据失败');
        }else {
            $Model = new Model (); // 实例化
            $sql= "update xc_cate_gallery set pic_id='".$_POST['id']."' where pic_id=0";
			$Model->execute($sql);

              $msg = '编辑户型:'.$names."ID:".$result;
                $this->saveLog(1,0,$msg);
            $this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
            $this->success('编辑数据成功！');
        }
    }


	/*
	 +-------------------------------------
		保存图片
		在执行update方法之前执行_before_update
	 +-------------------------------------
	*/
		public function _before_insert() {
		$PicGallery = D('CateGallery');

		if(!empty($_FILES['images']['name']['0'])){
			//echo 'login';
			//exit;
			$uploadList = $this->_upload($_FILES);
			for($i=0; $i<count($uploadList); $i++) {
				$data['model']='Huxing';
			    $data ['pic_id'] = 0;
				$data['images'] = $uploadList[$i]['savename'];
                $data['img_name'] = $_POST['imgname'][$i];
                $data["create_time"]=time();
				$result = $PicGallery->add($data);

			}
		}
	}


	public function _before_update() {
		$PicGallery = D('CateGallery');

		if(!empty($_FILES['images']['name']['0'])){

			$uploadList = $this->_upload($_FILES);
			for($i=0; $i<count($uploadList); $i++) {
				$data['model']='Huxing';
			    $data ['pic_id'] = 0;
				$data['images'] = $uploadList[$i]['savename'];
                $data['img_name'] = $_POST['imgname'][$i];
                $data["create_time"]=time();
				$result = $PicGallery->add($data);

			}
		}
	}


    public function swTopStatus() {

			$status = $_REQUEST['status'];
			$model = D ("Category");
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			$item = $model->getById($id);

			$list = $model->where($pk."=".$id)->setField('is_top',1);

			if ($list!==false) {
				//$this->saveLog(1);
				$this->assign ( "jumpUrl", $this->getReturnUrl () );
				$this->success ('置顶成功！');
			} else {
				$this->saveLog(0);
				$this->error  ('置顶失败!');
			}
		}

		public function uTopStatus() {

			$status = $_REQUEST['status'];
			$model = D ("Category");
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			$item = $model->getById($id);

			$list = $model->where($pk."=".$id)->setField('is_top',0);

			if ($list!==false) {
				//$this->saveLog(1);
				$this->assign ( "jumpUrl", $this->getReturnUrl () );
				$this->success ('取消置顶成功！');
			} else {
				$this->saveLog(0);
				$this->error  ('取消置顶失败!');
			}
		}
 	function _upload() {
        import("@.ORG.UploadFile");
		$upload = new UploadFile ();
		//设置上传文件大小
		$upload->maxSize  = eyooC('MAX_UPLOAD') ;
		//设置上传文件类型
		$upload->allowExts = explode ( ',', 'jpg,gif,png,jpeg,rar' );
		//设置附件上传目录
		$upload->thumb = true;
        $upload->thumbPrefix ='m_,s_' ;
        $upload->thumbMaxHeight = '200,99';
        $upload->thumbMaxWidth =  '280,138';
		//设置上传文件规则
		$upload->saveRule = 'uniqid';
		//存在同名文件覆盖,可上传相同图片
		$upload->uploadReplace = 'true';
		//删除原图路径
		$upload->thumbRemoveOrigin = false;

		$upload->savePath = './Public/upload/huxing/';
		//$upload->saveRule = true;
		if (! $upload->upload ()) {
			$this->error ( $upload->getErrorMsg () );
		} else {
			$uploadList = $upload->getUploadFileInfo ();
			return $uploadList;
		}
	}
    //删除图片
	public function deletePic(){
		$PicGallery = D('CateGallery');
		$picmap['id']=$_GET['id'];
		$picname=$PicGallery->getById($_GET['id']);
		$pic1="./Public/upload/huxing/".$picname['images']."";
		$pic2="./Public/upload/huxing/m_".$picname['images']."";

		@unlink($pic1);
		@unlink($pic2);
		$picg=$PicGallery->where($picmap)->delete();
		if($picg!=false){
			$this->success('删除成功！');
		}else{
			$this->error(删除失败！);
		}
	}

    /**
* foreverdelete 删除
*/
	public function foreverdelete() {

		$model =D("Category");
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
                $res= $model->where ( $condition )->delete ();
                 //   echo $model->getlastsql();
                 //   exit;
				if (false !== $res) {
					$this->success ( '删除成功！');
				} else {
					$this->error ('删除失败！');
				}
			} else {
				$this->error ( '非法操作' );
			}
		}
		$this->forward ();
	}


}
?>