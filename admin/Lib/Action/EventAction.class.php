<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 活动
class EventAction extends CommonAction{
	public function index()
	{
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();

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

		$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->field("id")->find();
		$default_lang_id = $default_lang_id['id'];  //默认语言的ID
		$select_dispname = "name_".$default_lang_id;

		$this->assign("select_dispname",$select_dispname);
		$this->assign("default_lang_id",$default_lang_id);

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

		$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->field("id")->find();
		$default_lang_id = $default_lang_id['id'];  //默认语言的ID
		$select_dispname = "name_".$default_lang_id;

		$new_sort = D(MODULE_NAME) -> max("sort") + 1;
        //echo $new_sort;

		$this->assign('new_sort',$new_sort);
		$this->assign("select_dispname",$select_dispname);
		$this->assign("default_lang_id",$default_lang_id);
		$this->assign("session_id",$_SESSION['verify']);
		$this->display();
	}

    public function _before_insert() {
		if(!empty($_FILES['image']['tmp_name']) ) {
			import("@.ORG.UploadFile");
			$upload = new UploadFile();
			$upload->saveRule='uniqid';
			$upload->allowExts  = explode(',','jpg,gif,png,jpeg');
				$upload->thumb  =  false;
				$upload->thumbPrefix ='m_' ;
				$upload->thumbMaxHeight = '400';
				$upload->thumbMaxWidth =  '400';
				//删除原图路径
				$upload->thumbRemoveOrigin = false;
			//上传图片
			$upload->savePath = './Public/upload/Event/';
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
		//保存当前数据对象
        if($_POST['image']){
            $model->image=$_POST['image'];
        }
		$list = $model->add();
        if($list!=false){
			$this->success ('活动添加成功!');
		} else {
			$this->error ('活动添加失败!');
		}
	}


    public function read(){
        $Event =  D("Event");
        $whereid['id']=  $_REQUEST['id'];
        $inlist = $Event->where($whereid)->find();

        import("@.ORG.Page");//导入类
        $app['event_id']=$id;
        $count = D("join_event")->where($app)->count();//统计数据总数
        $page = new Page($count,5);//定义一个变量控制每页显示信息条数
		$comments=D("join_event")->where("ask_id='".$id."'")->order("create_time asc")->select();
        $show = $page->show();//将数据映射到页面
		$this->assign("comments",$comments);
        $this->assign( "page", $show);
        $this->assign('inlist',$inlist);
		$this->edit();
    }

    public function edit()
	{
        $lang_envs = D("LangConf")->findAll();

		$name=$this->getActionName();
		$model = D ($name);

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
        echo $model->getlastsql();

        if(flase!==$list){
			$this->success ('活动编辑成功!');
		} else {
			$this->error ('活动编辑失败!');
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
			$upload->thumbPrefix ='m_,s_' ;
			$upload->thumbMaxHeight = '450,150';
			$upload->thumbMaxWidth =  '320,120';

			//设置上传文件规则
			$upload->saveRule =uniqid;
			//存在同名文件覆盖,可上传相同图片
			//$upload->uploadReplace='true';
			//删除原图路径
			$upload->thumbRemoveOrigin = false;

			$upload->savePath =  './Public/upload/Event/';
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
		$Event_id = intval($_REQUEST['Event_id']);
		$attahcment_item = D("Attachment")->getById($attachment_id);
		if($rs = D("Attachment")->where("id=".$attachment_id)->delete())
		{
			@unlink($this->getRealPath().$attahcment_item['file_path']);
			D("AttachmentLink")->where("module='Event' and rec_id=".$Event_id." and attachment_id=".$attachment_id)->delete();
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
				$names .= M("Event")->where("id=".$idd)->getField("name_".DEFAULT_LANG_ID).",";
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
					$this->error("系统活动不能删除");
				}
				if (false !== $model->where ( $condition )->delete ()) {
					//echo $model->getlastsql();
					//开始删除相关的附件
					$attachment_ids = D("AttachmentLink")->where("module='Event' and rec_id=".$id)->findAll();
					D("AttachmentLink")->where("module='Event' and rec_id=".$id)->delete();
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
					$msgList = D("Message")->where(array ("rec_id" => array ('in', explode ( ',', $id ) ),'rec_module'=>'Event' ))->findAll();
					D("Message")->where(array ("rec_id" => array ('in', explode ( ',', $id ) ),'rec_module'=>'Event' ))->delete();
					foreach($msgList as $msgItem)
					{
						D("Message")->where("pid=".$msgItem['id'])->delete();
					}

					$msg = '删除活动:'.$names."ID:".$id;
					$this->saveLog(1,0,$msg);
					$this->success (L('DEL_SUCCESS'));
				} else {
					$msg = '删除活动:'.$names;
					$this->saveLog(0,0,$msg);
					$this->error (L('DEL_FAILED'));
				}
			} else {
				$msg = '删除活动:'.$names;
				$this->saveLog(0,0,$msg);
				$this->error ( L('INVALID_OP') );
			}
		}
	}

	public function deletePreview()
	{
		$Event_id = intval($_REQUEST['id']);
		$a_info = D("Event")->getById($Event_id);
		if($a_info['preview']!="")
		{
			@unlink($this->getRealPath().$a_info['preview']);
			D("Event")->where("id=".$Event_id)->setField("preview","");
		}
		$this->success (L('DEL_SUCCESS'));
	}

	//获取图片信息
	public function setGallery()
	{
		$id = $_REQUEST['id'];
		$gallery = D("NewsGallery")->getById($id);
		echo json_encode($gallery);
	}

	//删除图片
	public function delGallery()
	{
		$id = $_REQUEST['id'];
		$item = D("NewsGallery")->getById($id);
		@unlink($this->getRealPath().$item['origin_img']);
		@unlink($this->getRealPath().$item['big_img']);
		@unlink($this->getRealPath().$item['small_img']);
		D("NewsGallery")->where('id='.$id)->delete();
	}
}
?>