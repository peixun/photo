<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 商品分类
class GoodsCateAction extends CommonAction{
	//列表
	public function index() {
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
			$this->_list ( $model, $map, '', false, true, 'id','pid', $dispname_arr);
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
	
	
	public function insert() {
		//B('FilterString');
		$name=$this->getActionName();
		$model = D ($name);
		$data  = $model->create ();
		if (false === $data) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add ($data);
		if ($list!==false) { //保存成功
			$msg = '添加分类'.$data['name_'.DEFAULT_LANG_ID];
			$this->saveLog(1,$list,$msg);	
			
//			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('ADD_SUCCESS'));
		} else {
			//失败提示
			$msg = '添加分类'.$data['name_'.DEFAULT_LANG_ID];
			$this->saveLog(0,0,$msg);	
			$this->error (L('ADD_FAILED'));
		}
	}
	
	public function update() {
		//B('FilterString');
		$name=$this->getActionName();
		$model = D ( $name );
		$data = $model->create ();
		if (false === $data) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ($data);
		if (false !== $list) {
			//成功提示
			$msg = '修改分类'.$data['name_'.DEFAULT_LANG_ID];
			$this->saveLog(1,$list,$msg);	
//			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('EDIT_SUCCESS'));
		} else {
			//错误提示
			$msg = '修改分类'.$data['name_'.DEFAULT_LANG_ID];
			$this->saveLog(0,0,$msg);	
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
				$names .= M("GoodsCate")->where("id=".$idd)->getField("name_".DEFAULT_LANG_ID).",";
			}
			if($names!='')
			{
				$names = substr($names,0,strlen($names)-1);
			}
			if (isset ( $id )) {
				//进行分类删除的验证
				if($model->where('pid='.$id)->count() > 0)
				$this->error(L('CHILD_CATE_EXIST'));
				if(D("Goods")->where("cate_id=".$id)->count() > 0)
				$this->error(L('GOODS_EXIST'));
				//验证结束
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ()) {
					//echo $model->getlastsql();
					$msg = '删除分类:'.$names."ID:".$id;
					$this->saveLog(1,0,$msg);
					
					$this->success (L('DEL_SUCCESS'));
				} else {
					$msg = '删除分类:'.$names."ID:".$id;
					$this->saveLog(0,0,$msg);
					$this->error (L('DEL_FAILED'));
				}
			} else {
				$msg = '删除分类:'.$names;
				$this->saveLog(0,0,$msg);
				$this->error ( L('INVALID_OP') );
			}
		}
		$this->forward ();
	}
}
?>