<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 单页管理
class PageAction extends CommonAction{
	public function index()
	{
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model,$map);
		}
		$this->assign("map",$map);
		$this->display ();
		return;
	}

	//增
	public function add()
	{
		$this->display();
	}

	public function insert() {
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
        $list = $model->add ();
		if ($list!==false) { //保存成功




			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('ADD_SUCCESS'));
		} else {

			$this->error (L('ADD_FAILED'));
		}

	}

	//改
	public function edit()
	{
        $where['id']=$_GET['id'];
		$list = D(Page)->where($where)-> find();
        if($list!=false){
		$this->assign("list",$list);
        }else{
		$this->display();
        }
		parent::edit();
	}

	public function update()
	{
        $lang_envs = D("LangConf")->select();
		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save();
		 if($list!=false){
            $this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ('单页信息编辑成功!');
		} else {
			$this->error ('单页信息编辑失败!');
		}
		}



	//同步父分类类型
	public function loadCateType()
	{
		$cate_id = intval($_REQUEST['cate_id']);
		echo D("ArticleCate")->where("id=".$cate_id)->getField("type");
	}
}
?>
