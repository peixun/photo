<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 文章
class CaseAction extends CommonAction{
	public function index()
	{
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();


		$this->assign("cate_id",$_REQUEST['cate_id']);

		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map, 'sort', true );
		}
		$this->assign("map",$map);

        //echo $model->getLastsql();

		$this->display ();
		return;
	}


	//增
	public function add()
	{
		$lang_envs = D("LangConf")->findAll();
		$lang_ids = array();
		$dispname_arr = array();
		$lang_names = array();
		foreach($lang_envs as $lang_item)
		{
			$dispname_arr[] = "name_".$lang_item['id'];
			$lang_ids[]=$lang_item['id'];
			$lang_names[] = $lang_item['lang_name'];
		}
		$lang_ids = implode(",",$lang_ids);
		$this->assign("lang_ids",$lang_ids);
		$lang_names = implode(",",$lang_names);
		$this->assign("lang_names",$lang_names);

        $umap['type']=2;
        $umap['active']=1;
        $umap['status']=1;
        $user_list = D("User")-> where($umap)-> findAll();



		$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->field("id")->find();
		$default_lang_id = $default_lang_id['id'];  //默认语言的ID
		$select_dispname = "name_".$default_lang_id;


		$new_sort = D(MODULE_NAME) -> max("sort") + 1;
        //echo $new_sort;

		$this->assign('new_sort',$new_sort);
		$this->assign("select_dispname",$select_dispname);
		$this->assign("default_lang_id",$default_lang_id);
		$this->assign("cate_list",$cate_list);
		$this->assign("user_list",$user_list);

        $Category =D("Category");
        $cate =$Category->where('pid=0')->select();




        $this->assign ( "cate", $cate );




		$this->display();
	}

     /**
     +----------------------------------------------------------
     * @上传案例图片
     +----------------------------------------------------------
    */
    public function _before_insert(){
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
		$upload->maxSize = eyooC ( "MAX_UPLOAD" );
		//设置上传文件类型
		$upload->allowExts = explode ( ',', 'jpg,gif,png,jpeg,rar' );
		//设置附件上传目录
		$upload->thumb = true;
		$upload->thumbPrefix = 'l_,m_,s_';
		$upload->thumbMaxHeight = '210,170,103';
		$upload->thumbMaxWidth = '280,200,138';
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


	public function insert(){
          $name=$this->getActionName();
          $model = D ($name);

		$lang_envs = D("LangConf")->findAll();


		if (false === $model->create()){
			$this->error ( $model->getError());

		}

		$list = $model->add();

        if($list!=false){
            $Model = new Model (); // 实例化一个model对象 没有对应任何数据表
			$Model->execute ( "update xc_pic_gallery set pic_id='" .$list. "' where pic_id=0" );
             $msg = '添加案例:'.$names."ID:".$list;
            $this->saveLog(1,0,$msg);
		    $this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
            $this->success ('案例添加成功!');
		} else {
             $msg = '添加案例:'.$names."ID:".$list;
            $this->saveLog(0,0,$msg);
			$this->error ('案例添加失败!');
		}
	}

    public function edit()
	{
        $lang_envs = D("LangConf")->findAll();

		$name=$this->getActionName();
        $model = D ($name);
        $id = $_REQUEST [$model->getPk ()];
        $vo = $model->getById ( $id );
		$this->assign ( 'vo', $vo );


        $lang_envs = D("LangConf")->findAll();
		$lang_ids = array();
		$dispname_arr = array();
		$lang_names = array();
		foreach($lang_envs as $lang_item)
		{
			$dispname_arr[] = "name_".$lang_item['id'];
			$lang_ids[]=$lang_item['id'];
			$lang_names[] = $lang_item['lang_name'];
		}
		$lang_ids = implode(",",$lang_ids);
		$this->assign("lang_ids",$lang_ids);
		$lang_names = implode(",",$lang_names);
		$this->assign("lang_names",$lang_names);

		$Category =D("Category");
        $cate =$Category->where('pid=0')->select();

		$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->field("id")->find();
		$default_lang_id = $default_lang_id['id'];  //默认语言的ID
		$select_dispname = "name_".$default_lang_id;

        $umap['type']=2;
        $umap['active']=1;
        $umap['status']=1;
        $user_list = D("User")-> where($umap)-> findAll();


		$this->assign("select_dispname",$select_dispname);
		$this->assign("default_lang_id",$default_lang_id);
		$this->assign("cate",$cate);
		$this->assign("user_list",$user_list);

        $PicGallery = D ( 'PicGallery' );
        $pwhere['pic_id']=$_GET['id'];
        $pwhere['model']='Case';
        $pic =$PicGallery->where($pwhere)->select();

        $this->assign('pic',$pic);

	    $this->display();
	}
     /**
     +----------------------------------------------------------
     * @编辑案例上传案例图片
     +----------------------------------------------------------
    */
    public function _before_update(){
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
    public function update()
	{

        $id = intval($_REQUEST['id']);
        $name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create()){
			$this->error ($model->getError());
		}

        $model->id=$id;
		$list = $model->save();

        if(flase!==$list){

             $msg = '编辑案例:'.$names."ID:".$id;
            $this->saveLog(1,0,$msg);
            $this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ('案例编辑成功!');
		} else {
            $msg = '编辑案例:'.$names."ID:".$id;
            $this->saveLog(0,0,$msg);
			$this->error ('案例编辑失败!');
		}
    }




	public function delPic()
	{
		$id = intval($_REQUEST['id']);

		$attahcment_item = D("PicGallery")->getById($id);
		if($rs = D("PicGallery")->where("id=".$id)->delete())
		{
			    @unlink("./Public/upload/case/".$attahcment_item['images']);
            	@unlink("./Public/upload/case/".'l_'.$attahcment_item['images']);
            	@unlink("./Public/upload/case/".'m_'.$attahcment_item['images']);
			$this->success ('删除成功!');
		}
		else
		{
			$this->error ('删除失败!');
		}
	}




	public function deletePreview()
	{
		$article_id = intval($_REQUEST['id']);
		$a_info = D("Article")->getById($article_id);
		if($a_info['preview']!="")
		{
			@unlink($this->getRealPath().$a_info['preview']);
			D("Article")->where("id=".$article_id)->setField("preview","");
		}
		$this->success (L('DEL_SUCCESS'));
	}

	//移动文章至分类
	public function moveArticle()
	{
		$name=$this->getActionName();
			$model = M ($name);
			if (! empty ( $model )) {
				$pk = $model->getPk ();
				$id = $_REQUEST [$pk];
				$cate_id = $_REQUEST['cate_id'];
				$cate_info = D("ArticleCate")->getById($cate_id);
				if (isset ( $id )) {
					$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				$test_condition = $condition;
				$test_condition['type'] = 1;
				if($model->where($test_condition)->count()>0)
				{
					$this->error("系统文章不能移动");
				}

					$list=$model->where ( $condition )->setField ( 'cate_id', $cate_id );
					$list=$model->where ( $condition )->setField ( 'type', $cate_info['type'] );
					if ($list!==false) {
						$msg = '移动文章,ID:'.$id;
						$this->saveLog(1,0,$msg);
						$this->success ( L('MOVE_SUCCESS') );
					} else {
						$msg = '移动文章,ID:'.$id;
						$this->saveLog(0,0,$msg);
						$this->error (L('MOVE_FAILED'));
					}
				} else {
					$msg = '移动文章,ID:'.$id;
					$this->saveLog(0,0,$msg);
					$this->error ( L('INVALID_OP') );
				}
			}
		$this->forward ();
	}


     public function get_huxing_name(){
		$data['pid'] = $_POST['id'];

		$city = M('Category')->where($data)->findAll();

		$str = '';
		for($i= 0;$i<count($city);$i++){
			$str .= '<option value="'.$city[$i]['id'].'">'.$city[$i]['name_1'].'</option>';
		}
		echo  $str;
	}

    public function get_desinger(){

        $Company =D("Company");
        $comap['uid']= $_POST['id'];
        $comp=$Company->where( $comap)->find();

        $dmap['com_id']=$comp['id'];

		$city = M('Designer')->where($dmap)->findAll();

		$str = '';
		for($i= 0;$i<count($city);$i++){
			$str .= '<option value="'.$city[$i]['id'].'">'.$city[$i]['name_1'].'</option>';
		}
		echo  $str;
	}

}
?>