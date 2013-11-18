<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 商品
class NewGoodsAction extends CommonAction{
	//查询
	public function search() {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		$map['status'] = array('in','0,1');


		if(intval($_REQUEST['cate_id'])!=0)
		{
			$cate_ids = D("GoodsCate")->getChildIds(intval($_REQUEST['cate_id']));
			$cate_ids[] = intval($_REQUEST['cate_id']);
			$map['cate_id'] = array("in",$cate_ids);
		}
		else
		unset($map['cate_id']);

		$this->assign("cate_id",$_REQUEST['cate_id']);

		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}

		if(intval($_REQUEST['city_id'])==0)
		   $map['city_id'] = array('in',$_SESSION['admin_city_ids']);

		if(intval($_REQUEST['suppliers_id'])==0)
		   unset($map['suppliers_id']);

		$this->assign("cate_id",$_REQUEST['cate_id']);
		$this->assign("city_id",$_REQUEST['city_id']);
		$this->assign("suppliers_id",$_REQUEST['suppliers_id']);
		$this->assign("goods_name",$_REQUEST['name']);

		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->assign("map",$map);
		$lang_envs = D("LangConf")->findAll();
        echo 'ok';
        dump($lang_envs);
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

		$cate_list = D("GoodsCate")-> where("status=1")-> findAll();
		$cate_list = D("GoodsCate")-> toFormatTree($cate_list,$dispname_arr);

		$this->assign("select_dispname",$select_dispname);
		$this->assign("default_lang_id",$default_lang_id);
		$this->assign("cate_list",$cate_list);

		//商品分类
		$city_list = D("GroupCity")->where("status=1")->order("is_defalut desc,id asc")->findAll();
		$this->assign("city_list",$city_list);
		//供应商家
		$suppliers_list = D("Suppliers")->where("status=1")->findAll();
		$this->assign("suppliers_list",$suppliers_list);

		$this->display ("Goods:search");
		return;
	}
	//列表
	public function index() {
		//列表过滤器，生成查询Map对象
		
		$name=$this->getActionName();
		$model = D ($name);
		$list=$model->select();
		$this->assign('list',$list);
		$this->display();
	}
	
	//增
	public function add()
	{
		$cate_list = D("GoodsCate")-> where("status=1")-> findAll();
		$cate_list = D("GoodsCate")-> toFormatTree($cate_list,$dispname_arr);
		$this->assign("cate_list",$cate_list);
		$this->display();
	}

	public function insert()
	{
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}

		//保存当前数据对象
		$newgoods_id = $model->add ($data);
		if($newgoods_id){
			$this->success (L('ADD_SUCCESS')." ".$mail_msg);
		}else{
			$this->error (L('ADD_FAILED'));
		}
	}

	//改
	public function edit()
	{
		$cate_list = D("GoodsCate")-> where("status=1")-> findAll();
		$cate_list = D("GoodsCate")-> toFormatTree($cate_list,$dispname_arr);
		$this->assign("cate_list",$cate_list);
		$name=$this->getActionName();
		$model = D ($name);
		$vo=$model->find($_GET['id']);
		$this->assign('vo',$vo);
		$this->display();
	}

	public function update()
	{
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}

		//保存当前数据对象
		$newgoods_id = $model->save($data);
		if($newgoods_id){
			$this->success (L('EDIT_SUCCESS')." ".$mail_msg);
		}else{
			$this->error (L('EDIT_FAILED'));
		}
	}
	public function delete() {
		$name=$this->getActionName();
		$model = M ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			$ids = explode ( ',', $id );
			$names = '';
			foreach($ids as $idd)
			{
				$names .= M("NewGoods")->where("id=".$idd)->getField("name_".DEFAULT_LANG_ID).",";
			}
			if($names!='')
			{
				$names = substr($names,0,strlen($names)-1);
			}
			if (isset ( $id )) {
				if(!$_SESSION['all_city'])
				$condition = array ($pk => array ('in', explode ( ',', $id ) ),"city_id"=>array("in",$_SESSION['admin_city_ids']) );
				else
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				$list=$model->where ( $condition )->delete();
				if ($list!==false) {
					$msg = '删除新品:'.$names;
					$this->saveLog(1,0,$msg);
					$this->success ( L('DEL_SUCCESS') );
				} else {
					$msg = '删除新品:'.$names;
					$this->saveLog(0,0,$msg);
					$this->error (L('DEL_FAILED'));
				}
			} else {
				$msg = '彻底删除新品,ID:'.$id;
				$this->saveLog(0,0,$msg);
				$this->error ( L('INVALID_OP') );
			}
		}
	}

	
	/*---------------------------------
	* _before_insert
	----------------------------------*/
	public function _before_insert() {
		if(!empty($_FILES['images']['tmp_name']) ) {
			import("@.ORG.UploadFile");
			$upload = new UploadFile();
			$upload->maxSize  = eyooC('MAX_UPLOAD') ;
			$upload->saveRule='uniqid';
			$upload->allowExts  = explode(',','jpg,gif,png,jpeg');
				$upload->thumb  =  true;
				$upload->thumbPrefix ='m_,t_' ;
				$upload->thumbMaxHeight = '400,145';
				$upload->thumbMaxWidth =  '400,145';
				//删除原图路径
				$upload->thumbRemoveOrigin = false;
			//上传图片
			$upload->savePath = 'Public/upload/newgoods/';	
			if(!$upload->upload()) {
				//捕获上传异常
				$this->error($upload->getErrorMsg());
			}else {
				//取得成功上传的文件信息
				$uploadList = $upload->getUploadFileInfo();
				//dump($uploadList); exit();
				$_POST['images']  = $uploadList[0]['savename'];
			}
		}
	}

	public function _before_update() {
		if(!empty($_FILES['images']['tmp_name']) ) {
			import("@.ORG.UploadFile");
			$upload = new UploadFile();
			$upload->maxSize  = eyooC('MAX_UPLOAD') ;
			$upload->saveRule='uniqid';
			$upload->allowExts  = explode(',','jpg,gif,png,jpeg');
				$upload->thumb  =  true	;
				$upload->thumbPrefix ='m_,t_' ;
				$upload->thumbMaxHeight = '400,145';
				$upload->thumbMaxWidth =  '400,145';
				//删除原图路径
				$upload->thumbRemoveOrigin = false;
			//上传图片
			$upload->savePath = 'Public/upload/newgoods/';				
			if(!$upload->upload()) {
				//捕获上传异常
				$this->error($upload->getErrorMsg());
			}else {
				//取得成功上传的文件信息
				$uploadList = $upload->getUploadFileInfo();
				//dump($uploadList); exit();
				$_POST['images']  = $uploadList[0]['savename'];
				//echo $_POST['picname'];
			}
		}
	}
}
?>