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
        if(!empty($_POST['uploadList'])){
            $data['image'] = $_POST['uploadList'][0]['savename'];
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
                $this->error('添加数据失败');
            }else {
                $this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
                $this->success('添加数据成功！');
            }
        }else{
            $this-error("请选择一张图片");
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
        if(!empty($_POST['uploadList'])){

            $data['image'] = $_POST['uploadList'][0]['savename'];
        }
        $result = $model->save($data);

        if($result===false) {
            $this->error('编辑数据失败');
        }else {
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
		if(!empty($_FILES['image']['tmp_name']) ) {
			import("@.ORG.UploadFile");
			$upload = new UploadFile();
			$upload->maxSize  = eyooC('MAX_UPLOAD') ;
			$upload->saveRule='uniqid';
			$upload->allowExts  = explode(',','jpg,gif,png,jpeg');
				$upload->thumb  =  true;
				$upload->thumbPrefix ='m_' ;
				$upload->thumbMaxHeight = '200';
				$upload->thumbMaxWidth =  '170';
				//删除原图路径
				$upload->thumbRemoveOrigin = false;
			//上传图片
			$upload->savePath = './Public/upload/huxing/';
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
				$upload->thumb  =  false;
				$upload->thumbPrefix ='m_' ;
				$upload->thumbMaxHeight = '200';
				$upload->thumbMaxWidth =  '170';
				//删除原图路径
				$upload->thumbRemoveOrigin = false;
			//上传图片
			$upload->savePath = './Public/upload/huxing/';
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


}
?>