<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 文章分类
class ArticleCateAction extends CommonAction{
	public function index()
	{
		$lang_envs = D("LangConf")->findAll();
		$dispname_arr = array();
		foreach($lang_envs as $lang_item)
		{
			$dispname_arr[] = "name_".$lang_item['id'];
		}
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		$map['status'] = 1;
		$map['pid'] = 0;
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map, 'sort', true, true, 'id','pid', $dispname_arr);
		}
		$this->display ();
		return;
	}

	//增
	public function add()
	{
		$lang_envs = D("LangConf")->field("id")->findAll();
		$dispname_arr = array();
		foreach($lang_envs as $lang_item)
		{
			$dispname_arr[] = "name_".$lang_item['id'];
		}
		$list = D(MODULE_NAME)-> where("status=1")-> findAll();
		$list = D(MODULE_NAME)-> toFormatTree($list,$dispname_arr);


		$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->field("id")->find();
		$default_lang_id = $default_lang_id['id'];  //默认语言的ID
		$select_dispname = "name_".$default_lang_id;


		$new_sort = D(MODULE_NAME)->where("status=1") -> max("sort") + 1;
		$this->assign('new_sort',$new_sort);
		$this->assign("select_dispname",$select_dispname);
		$this->assign("cate_list",$list);
		$this->display();
	}


	public function insert() {
		//B('FilterString');
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		$data['type'] = 2;
		//保存当前数据对象
		$list=$model->add ($data);
		if ($list!==false) { //保存成功
			$msg = '添加文章分类'.$_REQUEST['name_'.DEFAULT_LANG_ID];
			$this->saveLog(1,$list,$msg);
//			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('ADD_SUCCESS'));
		} else {
			//失败提示
			$msg = '添加文章分类'.$_REQUEST['name_'.DEFAULT_LANG_ID];
			$this->saveLog(0,0,$msg);
			$this->error (L('ADD_FAILED'));
		}
	}

	//改
	public function edit()
	{
		$lang_envs = D("LangConf")->field("id")->findAll();
		$dispname_arr = array();
		foreach($lang_envs as $lang_item)
		{
			$dispname_arr[] = "name_".$lang_item['id'];
		}

		//开始获取当前分类与其子分类的ID
		$pk = D(MODULE_NAME)->getPk();
		$childIds = D(MODULE_NAME)->getChildIds($_REQUEST[$pk]);
		$childIds[] = $_REQUEST[$pk];
		$childIds_str = implode(",",$childIds);

		$list = D(MODULE_NAME)-> where("status=1 and ".$pk." not in(".$childIds_str.")")-> findAll();
		$list = D(MODULE_NAME)-> toFormatTree($list,$dispname_arr);


		$default_lang_id = D("LangConf")->where("lang_name='".C('DEFAULT_LANG')."'")->field("id")->find();
		$default_lang_id = $default_lang_id['id'];  //默认语言的ID
		$select_dispname = "name_".$default_lang_id;

		$this->assign("select_dispname",$select_dispname);
		$this->assign("cate_list",$list);

		parent::edit();
	}

	public function update()
	{
		$id = intval($_REQUEST['id']);
		$type = 2;

		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		$data['type'] = 2;

		// 更新数据
		$list=$model->save ($data);

		if (false !== $list) {
			//成功提示
			//开始同步子分类与文章的类型

			$child_cate_ids = D("ArticleCate")->getChildIds($id);
			$child_cate_ids[] = $id;

			D("ArticleCate")->where(array('id'=>array('in',$child_cate_ids)))->setField("type",$type);
			D("Article")->where(array('cate_id'=>array('in',$child_cate_ids)))->setField("type",$type);

			//$msg = '修改文章分类'.$_REQUEST['name_'.DEFAULT_LANG_ID];
			//$this->saveLog(1,$list,$msg);
//			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('EDIT_SUCCESS'));
		} else {
			//错误提示
			$msg = '修改文章分类'.$_REQUEST['name_'.DEFAULT_LANG_ID];
			$this->saveLog(0,$list,$msg);
			$this->error (L('EDIT_FAILED'));
		}
	}

	//删
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
				$names .= M("ArticleCate")->where("id=".$idd)->getField("name_".DEFAULT_LANG_ID).",";
			}
			if($names!='')
			{
				$names = substr($names,0,strlen($names)-1);
			}
			if (isset ( $id )) {
				//进行分类删除的验证
				if($model->where('pid='.$id)->count() > 0)
				$this->error(L('CHILD_CATE_EXIST'));
				if(D("Article")->where("cate_id=".$id)->count() > 0)
				$this->error(L('ARTICLE_EXIST'));
				//验证结束
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ()) {
					//echo $model->getlastsql();
					$msg = '删除文章分类:'.$names."ID:".$id;
					$this->saveLog(1,0,$msg);
					$this->success (L('DEL_SUCCESS'));
				} else {
					$msg = '删除文章分类:'.$names."ID:".$id;
					$this->saveLog(0,0,$msg);
					$this->error (L('DEL_FAILED'));
				}
			} else {
				$msg = '删除文章分类:'.$names;
				$this->saveLog(0,0,$msg);
				$this->error ( L('INVALID_OP') );
			}
		}
		$this->forward ();
	}

	//同步父分类类型
	public function loadCateType()
	{
		$cate_id = intval($_REQUEST['cate_id']);
		echo D("ArticleCate")->where("id=".$cate_id)->getField("type");
	}
}
?>