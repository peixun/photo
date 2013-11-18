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

class CategoryAction extends CommonAction {
    public function index()
	{
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		$map['pid'] =0;
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
    //执行添加操作

    function add(){
        $RegionConf = D ( "RegionConf" );
		$regWhere ["region_level"] = 2;
		$regionConf = $RegionConf->where ( $regWhere )->select ();
		$this->assign ( "regions", $regionConf );
        $this->display();

    }

    function edit(){
        $RegionConf = D ( "RegionConf" );
		$regWhere ["region_level"] = 2;
		$regionConf = $RegionConf->where ( $regWhere )->select ();

        $model =  D("Category");

        $cate =$model->getById($_GET['id']);

		$this->assign ( "regions", $regionConf );

		$CateGallery =D("CateGallery");
		$cmap['pic_id']=$_GET['id'];
		$cmap['model'] ='Shequ';
		$photoList=$CateGallery->where($cmap)->select();

		$this->assign ( "photoList", $photoList );
		$this->assign ( "vo", $cate );
        $this->display();

    }

    //新增操作
	function insert() {
        $model =  D("Category");
        $data['name_1'] = $_POST['name_1'];
        $data['province_id'] = $_POST['province_id'];
        $data['city_id'] = $_POST['city_id'];
        $data['area_id'] = $_POST['area_id'];

        $data['status'] = 1;
        $data['pid'] = 0;
        $data['content_1'] = $_POST['content_1'];
        $data['seokeyword_1'] = $_POST['seokeyword_1'];
        $data['seocontent_1'] = $_POST['seocontent_1'];
        $data['create_time'] = time();
        $data['update_time'] = time();
        $result = $model->add($data);
        //echo $model->getlastsql();
        if($result===false) {
            $msg = '添加小区楼盘:'.$names;
            $this->saveLog(0,0,$msg);
            $this->error('添加数据失败');
        }else {
            $Model = new Model (); // 实例化一个model对象 没有对应任何数据表

            $sql= "update `xc_cate_gallery` set pic_id='".$result."' where pic_id=0";
			$Model->execute ($sql);
             $msg = '添加小区楼盘:'.$names."ID:".$result;
            $this->saveLog(1,0,$msg);
            $this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
            $this->success('添加数据成功！');
        }

	}
    //更新操作
    function update() {
        $model =  D("Category");
        $data['id'] = $_POST['id'];
        $data['name_1'] = $_POST['name_1'];
        $data['province_id'] = $_POST['province_id'];
        $data['city_id'] = $_POST['city_id'];
        $data['area_id'] = $_POST['area_id'];
        $data['content_1'] = $_POST['content_1'];
        $data['seokeyword_1'] = $_POST['seokeyword_1'];
        $data['seocontent_1'] = $_POST['seocontent_1'];
        $data['update_time'] = time();
       // if(!empty($_POST['uploadList'])){

//            $data['image'] = $_POST['uploadList'][0]['savename'];
  //      }
        $result = $model->save($data);

        if($result===false) {
            $msg = '编辑小区楼盘:'.$names."ID:".$_POST['id'];
            $this->saveLog(0,0,$msg);
            $this->error('编辑数据失败');
        }else {
            $Model = new Model (); // 实例化
            $sql= "update xc_cate_gallery set pic_id='".$_POST['id']."' where pic_id=0";
			$Model->execute($sql);

            $msg = '编辑小区楼盘:'.$names."ID:".$_POST['id'];
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
				$data['model']='Shequ';
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
				$data['model']='Shequ';
			    $data ['pic_id'] = 0;
				$data['images'] = $uploadList[$i]['savename'];
                $data['img_name'] = $_POST['imgname'][$i];
                $data["create_time"]=time();
				$result = $PicGallery->add($data);

			}
		}
	}




	public function get_city(){
		$data['pid'] = $_POST['id'];
		$data['region_level']= 3;
		$city = M('RegionConf')->where($data)->findAll();

		$str = '';
		for($i= 0;$i<count($city);$i++){
			$str .= '<option value="'.$city[$i]['id'].'">'.$city[$i]['name'].'</option>';
		}
		echo  $str;
	}

    public function get_area(){
		$data['pid'] = $_POST['city_id'];
		$data['region_level']= 4;
		$city = M('RegionConf')->where($data)->findAll();

		$str = '';
		for($i= 0;$i<count($city);$i++){
			$str .= '<option value="'.$city[$i]['id'].'">'.$city[$i]['name'].'</option>';
		}
		echo  $str;
	}

    public function get_city_name(){
		$data['id'] = $_POST['id'];

		$city = M('RegionConf')->where($data)->findAll();

		$str = '';
		for($i= 0;$i<count($city);$i++){
			$str .= '<option value="'.$city[$i]['id'].'">'.$city[$i]['name'].'</option>';
		}
		echo  $str;
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
        $upload->thumbPrefix ='m_' ;
        $upload->thumbMaxHeight = '200';
        $upload->thumbMaxWidth =  '170';
		//设置上传文件规则
		$upload->saveRule = 'uniqid';
		//存在同名文件覆盖,可上传相同图片
		$upload->uploadReplace = 'true';
		//删除原图路径
		$upload->thumbRemoveOrigin = false;

		$upload->savePath = './Public/upload/shequ/';
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
		$pic1="./Public/upload/shequ/".$picname['images']."";
		$pic2="./Public/upload/shequ/m_".$picname['images']."";

		@unlink($pic1);
		@unlink($pic2);
		$picg=$PicGallery->where($picmap)->delete();
		if($picg!=false){
			$this->success('删除成功！');
		}else{
			$this->error('删除失败！');
		}
	}
}
?>