<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 地区配置
class GroupCityAction extends CommonAction{
	public function index()
	{
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		$map['pid'] = 0;
		$this->assign("map",$map);
		
		if (! empty ( $model )) {
			$this->_list( $model, $map, 'sort', true, true, 'id','pid');
		}

		$this->display ();
	}
	
	//增
	public function add() {
		$new_sort = D(MODULE_NAME)->where("status=1") -> max("sort") + 1;
		$this->assign('new_sort',$new_sort);
		
		$lang_envs = D("LangConf")->field("id")->findAll();
		$dispname_arr = array();
		foreach($lang_envs as $lang_item)
		{
			$dispname_arr[] = "name";
		}
		$list = D(MODULE_NAME)-> where("status=1")-> findAll();
		$list = D(MODULE_NAME)-> toFormatTree($list,$dispname_arr);	

		$default_seo_title = D("LangConf")->where("lang_name='".eyooC("DEFAULT_LANG")."'")->getField("shop_title");
		$default_seo_keywords = D("LangConf")->where("lang_name='".eyooC("DEFAULT_LANG")."'")->getField("seokeyword");
		$default_seo_description = D("LangConf")->where("lang_name='".eyooC("DEFAULT_LANG")."'")->getField("seocontent");
		

		$this->assign("default_seo_title",$default_seo_title);
		$this->assign("default_seo_keywords",$default_seo_keywords);
		$this->assign("default_seo_description",$default_seo_description);
		$this->assign('cate_list',$list);
		
		$this->display ();
	}
	
	function insert() {
		//B('FilterString');
		$name=$this->getActionName();
		$model = D ($name);
		$data = $model->create ();
		if ($data === false) {
			$this->error ( $model->getError () );
		}
		
		$py = new Pinyin();
		$py_name = $py->complie($data['name']);
		if(M("GroupCity")->where("py='".$py_name."'")->count()>0)
		{
			$py_name = $py_name. "_".(M("GroupCity")->where("py='".$py_name."'")->count()+1);
		}
		$data['py'] = $py_name;
		
		
		//保存当前数据对象
		$list=$model->add ($data);
		//dump($model->getLastSql());
		if ($list!==false) { //保存成功
			if($_REQUEST['is_defalut'] == 1)
				$model-> where('id<>'.$_REQUEST['id'])->setField('is_defalut','0');
			$this->saveLog(1,$list);
			$this->success (L('ADD_SUCCESS'));
		} else {
			//失败提示
			$this->saveLog(0,$list);
			$this->error (L('ADD_FAILED'));
		}
	}
	
	public function edit() {
		$name=$this->getActionName();
		$model = M ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );
		
		$lang_envs = D("LangConf")->field("id")->findAll();
		$dispname_arr = array();
		foreach($lang_envs as $lang_item)
		{
			$dispname_arr[] = "name";
		}
		$list = D(MODULE_NAME)-> where("status=1 and id <>".$id)-> findAll();
		$list = D(MODULE_NAME)-> toFormatTree($list,$dispname_arr);	

		$this->assign('cate_list',$list);
				
		$this->assign ( 'vo', $vo );
		$this->display();
	}
	
	function update() {
		//B('FilterString');
		$name=$this->getActionName();
		$model = D ($name);
		$data = $model->create ();
		if (false === $data) {
			$this->error ( $model->getError () );
		}
		$py = new Pinyin();
		$py_name = $py->complie($data['name']);
		if(M("GroupCity")->where("py='".$py_name."' and id<>".$data['id'])->count()>0)
		{
			$py_name = $py_name. "_".(M("GroupCity")->where("py='".$py_name."'")->count()+1);
		}
		$data['py'] = $py_name;
		// 更新数据
		$list=$model->save ($data);
		if (false !== $list) {
			//成功提示
			if($_REQUEST['is_defalut'] == 1)
				$model-> where('id<>'.$_REQUEST['id'])->setField('is_defalut','0');
			$this->saveLog(1);
			$this->success (L('EDIT_SUCCESS'));
		} else {
			//错误提示
			$this->saveLog(0);
			$this->error (L('EDIT_FAILED'));
		}
	}
	
	public function getGroupCityName($cID)
	{
		return D("GroupCity")->where("id=$cID")->getField('name');
	}
}
?>