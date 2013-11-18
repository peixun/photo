<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 商品类型属性列表
class GoodsTypeAttrAction extends CommonAction{
	//列表
	public function index() {
		$type_id = $_REQUEST['type_id'];
		$this->assign('type_id',$type_id);
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		$map['type_id'] = $type_id;
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
	}
	
	//新增页面
	public function add() {
		$type_id = intval($_REQUEST['type_id']);
		$this->assign("type_id",$type_id);
		$this->assign("param",array('type_id'=>$type_id));  //链接参数
		$this->display ();
	}
	
	//修改页面
	public function edit()
	{			
		$name=$this->getActionName();
		$model = M ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );
		$this->assign ( 'vo', $vo );
		$this->assign("param",array('type_id'=>$vo['type_id']));  //链接参数
		$this->display ();
	}
	
	//增
	function insert() {
		//B('FilterString');
		$type_id = intval($_REQUEST['type_id']);
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) { //保存成功
			$this->saveLog(1,$list);			
			$this->assign ( 'jumpUrl', U("GoodsTypeAttr/index",array('type_id'=>$type_id)) );
			$this->success (L('ADD_SUCCESS'));
		} else {
			//失败提示
			$this->saveLog(0,$list);
			$this->error (L('ADD_FAILED'));
		}
	}
	
	//改
	public function update() {
		//B('FilterString');
		$type_id = intval($_REQUEST['type_id']);
		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();
		if (false !== $list) {
			//成功提示
			$this->saveLog(1);
			$this->assign ( 'jumpUrl', U("GoodsTypeAttr/index",array('type_id'=>$type_id)) );
			$this->success (L('EDIT_SUCCESS'));
		} else {
			//错误提示
			$this->saveLog(0);
			$this->error (L('EDIT_FAILED'));
		}
	}
	
	//删
	public function foreverdelete()
	{
		//删除指定记录
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
								
				//验证属性下是否有商品
				$goods_condition = array ("attr_id" => array ('in', explode ( ',', $id ) ) );
				if(D("GoodsAttr")->where($goods_condition)->count()>0)
				{
					$this->saveLog(0);
					$this->error (L('ATTR_USED'));
				}
				
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ()) {
					//echo $model->getlastsql();
					$this->saveLog(1);
					$this->success (L('DEL_SUCCESS'));
				} else {
					$this->saveLog(0);
					$this->error (L('DEL_FAILED'));
				}
			} else {
				$this->saveLog(0);
				$this->error ( L('INVALID_OP') );
			}
		}
		$this->forward ();
	}
}
?>