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
class NewsAction extends CommonAction{
	public function index()
	{
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if(intval($_REQUEST['cate_id'])!=0)
		{
			$cate_ids = D("ArticleCate")->getChildIds(intval($_REQUEST['cate_id']));
			$cate_ids[] = intval($_REQUEST['cate_id']);
			$map['cate_id'] = array("in",$cate_ids);
		}
		else
		unset($map['cate_id']);

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

		$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->field("id")->find();
		$default_lang_id = $default_lang_id['id'];  //默认语言的ID
		$select_dispname = "name_".$default_lang_id;

		$lang_ids = implode(",",$lang_ids);
		$this->assign("lang_ids",$lang_ids);
		$lang_names = implode(",",$lang_names);
		$this->assign("lang_names",$lang_names);

		$cate_list = D("ArticleCate")-> where("status=1")-> findAll();
		$cate_list = D("ArticleCate")-> toFormatTree($cate_list,$dispname_arr);

		$this->assign("select_dispname",$select_dispname);
		$this->assign("default_lang_id",$default_lang_id);
		$this->assign("cate_list",$cate_list);

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

		$cate_list = D("ArticleCate")-> where("status=1")-> findAll();
		$cate_list = D("ArticleCate")-> toFormatTree($cate_list,$dispname_arr);

		$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->field("id")->find();
		$default_lang_id = $default_lang_id['id'];  //默认语言的ID
		$select_dispname = "name_".$default_lang_id;

		$name=$this->getActionName();
		$new_sort = D($name) -> max("sort") + 1;
        //echo $new_sort;

		$this->assign('new_sort',$new_sort);
		$this->assign("select_dispname",$select_dispname);
		$this->assign("default_lang_id",$default_lang_id);
		$this->assign("cate_list",$cate_list);
		$this->assign("news_id",0);
		$this->assign("session_id",$_SESSION['verify']);
		$this->display();
	}

    public function _before_insert() {
		if(!empty($_FILES['image']['tmp_name']) ) {
			import("@.ORG.UploadFile");
			$upload = new UploadFile();
			$upload->saveRule='uniqid';
			$upload->allowExts  = explode(',','jpg,gif,png,jpeg');
				$upload->thumb  =  true;
				$upload->thumbPrefix ='m_,s_,l_' ;
			    $upload->thumbMaxWidth = '642,220,45';
				$upload->thumbMaxHeight = '280,96,45';
				//删除原图路径
				$upload->thumbRemoveOrigin = false;
			//上传图片
			$upload->savePath = './Public/upload/Article/';
			if(!$upload->upload()) {
				//捕获上传异常
				$this->error($upload->getErrorMsg());
			}else {
				//取得成功上传的文件信息
				$uploadList = $upload->getUploadFileInfo();
				//dump($uploadList); exit();
				$_POST['image'] = $uploadList[0]['savename'];

			}
		}
	}

	public function insert()
	{
		$lang_envs = D("LangConf")->findAll();
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create()){
			$this->error ( $model->getError());
		}
		$cate_id = intval($_REQUEST['cate_id']);
		//保存当前数据对象
        if($_POST['image']){
            $model->image=$_POST['image'];
        }
		$list = $model->add();
        if($list!=false){
            $msg = '添加资讯:'.$names."ID:".$list;
            $this->saveLog(1,0,$msg);
            $this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ('文章添加成功!');
		} else {
            $msg = '添加资讯:'.$names;
            $this->saveLog(0,0,$msg);
			$this->error ('文章添加失败!');
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

		$cate_list = D("ArticleCate")-> where("status=1")-> findAll();
		$cate_list = D("ArticleCate")-> toFormatTree($cate_list,$dispname_arr);


		$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->field("id")->find();
		$default_lang_id = $default_lang_id['id'];  //默认语言的ID
		$select_dispname = "name_".$default_lang_id;


		$this->assign("select_dispname",$select_dispname);
		$this->assign("default_lang_id",$default_lang_id);
		$this->assign("cate_list",$cate_list);


		$where['id']=$_REQUEST['id'];
		$course = $model->where($where)->find();
		if($course)
		{
			$this->assign('course',$course);
		}

	 $this->display();
	}

    public function update()
	{
        $id = intval($_REQUEST['id']);
        $name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create()){
			$this->error ($model->getError());
		}
		//保存当前数据对象
        if($_POST['image']){
            $model->image=$_POST['image'];
        }
        $model->id=$id;
		$list = $model->save();
        //dump($list);

        if(flase!==$list){
            $this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ('文章编辑成功!');
		} else {
			$this->error ('文章编辑失败!');
		}
    }

    public function _before_update() {
		if(!empty($_FILES['image']['name'])){

			import("@.ORG.UploadFile");

			$upload = new UploadFile();
			//设置上传文件大小
			$upload->maxSize  = 3292200 ;
			//设置上传文件类型
			$upload->allowExts  = explode(',','jpg,gif,png,jpeg,rar');
			$upload->thumb  =  true;
				$upload->thumbPrefix ='m_,s_,l_' ;
			    $upload->thumbMaxWidth = '642,220,45';
				$upload->thumbMaxHeight = '280,96,45';

			//设置上传文件规则
			$upload->saveRule =uniqid;
			//存在同名文件覆盖,可上传相同图片
			//$upload->uploadReplace='true';
			//删除原图路径
			$upload->thumbRemoveOrigin = false;

			$upload->savePath =  './Public/upload/Article/';
			//$upload->saveRule = false;
			if(!$upload->upload()) {
				$this->error($upload->getErrorMsg());
			}else {
				//取得成功上传的文件信息
				$uploadList = $upload->getUploadFileInfo();
				//dump($uploadList); exit;
				$_POST['image']  = $uploadList[0]['savename'];

			}
		}
	}


	public function delAttachment()
	{
		$attachment_id = intval($_REQUEST['attachment_id']);
		$article_id = intval($_REQUEST['article_id']);
		$attahcment_item = D("Attachment")->getById($attachment_id);
		if($rs = D("Attachment")->where("id=".$attachment_id)->delete())
		{
			@unlink($this->getRealPath().$attahcment_item['file_path']);
			D("AttachmentLink")->where("module='Article' and rec_id=".$article_id." and attachment_id=".$attachment_id)->delete();
			echo $rs;
		}
		else
		{
			echo 0;
		}
	}


	public function foreverdelete() {
		//删除指定记录
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			$ids = explode ( ',', $id );
			$names = '';
			foreach($ids as $idd)
			{
				$names .= M("Article")->where("id=".$idd)->getField("name_".DEFAULT_LANG_ID).",";
			}
			if($names!='')
			{
				$names = substr($names,0,strlen($names)-1);
			}
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				$test_condition = $condition;
				$test_condition['type'] = 1;
				if($model->where($test_condition)->count()>0)
				{
					$this->error("系统文章不能删除");
				}
				if (false !== $model->where ( $condition )->delete ()) {
					//echo $model->getlastsql();
					//开始删除相关的附件
					$attachment_ids = D("AttachmentLink")->where("module='Article' and rec_id=".$id)->findAll();
					D("AttachmentLink")->where("module='Article' and rec_id=".$id)->delete();
					if($attachment_ids)
					{
						foreach($attachment_ids as $ids)
						{
							$attachment_ids_arr[] = $ids['attachment_id'];
						}
					}
					else
					{
						$attachment_ids_arr[] = 0;
					}
					$attachment_arr = D("Attachment")->where("id in(".implode(',',$attachment_ids_arr).")")->findAll();
					D("Attachment")->where("id in(".implode(',',$attachment_ids_arr).")")->delete();
					foreach($attachment_arr as $attach_item)
					{
						@unlink($this->getRealPath().$attach_item['file_path']);
					}

					//开始删除相关的留言
					$msgList = D("Message")->where(array ("rec_id" => array ('in', explode ( ',', $id ) ),'rec_module'=>'Article' ))->findAll();
					D("Message")->where(array ("rec_id" => array ('in', explode ( ',', $id ) ),'rec_module'=>'Article' ))->delete();
					foreach($msgList as $msgItem)
					{
						D("Message")->where("pid=".$msgItem['id'])->delete();
					}

					$msg = '删除文章:'.$names."ID:".$id;
					$this->saveLog(1,0,$msg);
					$this->success (L('DEL_SUCCESS'));
				} else {
					$msg = '删除文章:'.$names;
					$this->saveLog(0,0,$msg);
					$this->error (L('DEL_FAILED'));
				}
			} else {
				$msg = '删除文章:'.$names;
				$this->saveLog(0,0,$msg);
				$this->error ( L('INVALID_OP') );
			}
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

	//获取图片信息
	public function setGallery()
	{
		$id = $_REQUEST['id'];
		$gallery = D("NewsGallery")->getById($id);
		echo json_encode($gallery);
	}

}
?>