<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  设计师 Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */
class DesignerAction extends CommonAction{
	public function index()
	{
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();

		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
       // echo $model->getlastsql();

		$this->display ();
		return;
	}


	//增
	public function add()
	{


        $umap['status']=1;
        $user_list = D("company")-> where($umap)-> findAll();


		$this->assign("user_list",$user_list);

		$this->display();
	}



	public function insert(){
          $name=$this->getActionName();
          $model = D ($name);
          if(!empty($_FILES['image']['tmp_name'])){

           if(eyooC("WATER_MARK")){

    	    	$result = $this->uploadFiles(1,'designer',1,160,120,110,80,true);
    	    }else{

    		    $result = $this->uploadFiles(1,'designer',1,160,120,110,80,true);
            }
            $res = $result['uploadList'];


            $image=$res[0]['savename'];


        }
		$lang_envs = D("LangConf")->findAll();


		if (false === $model->create()){
			$this->error ( $model->getError());

		}
        if(!empty($image)){
            $model->image=$image;
        }
		$model ->uid=getCompanyUid($_POST['com_id']);

		$list = $model->add();


        if($list!=false){
             $msg = '添加设计师:'.$names."ID:".$list;
            $this->saveLog(1,0,$msg);
		    $this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
            $this->success ('添加设计师成功!');
		} else {
             $msg = '添加设计师:'.$names;
            $this->saveLog(0,0,$msg);
			$this->error ('添加设计师失败!');
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

        $umap['status']=1;
        $user_list = D("company")-> where($umap)-> findAll();


        $this->assign("user_list",$user_list);

        $this->assign("user_list",$user_list);

        $this->display();
	}

    public function update()
	{
        if(!empty($_FILES['image']['tmp_name'])){

        if(eyooC("WATER_MARK")){

    	    	$result = $this->uploadFiles(1,'designer',1,160,120,110,80,true);
    	    }else{

    		    $result = $this->uploadFiles(1,'designer',1,160,120,110,80,true);
            }
            $res = $result['uploadList'];


            $image=$res[0]['savename'];



        }
        $id = intval($_REQUEST['id']);
        $name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create()){
			$this->error ($model->getError());
		}
		//保存当前数据对象
        if(!empty($image)){
            $model->image=$image;
        }
        $model->id=$id;
		$list = $model->save();

        if(flase!==$list){
             $msg = '编辑设计师:'.$names."ID:".$id;
            $this->saveLog(1,0,$msg);
            $this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ('设计师编辑成功!');
		} else {
             $msg = '编辑设计师:'.$names."ID:".$id;
            $this->saveLog(0,0,$msg);
			$this->error ('设计师编辑失败!');
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