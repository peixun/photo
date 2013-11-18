<?php
/**
 +----------------------------------------------------------
 * @Link    http://www.eyoo.cn
 * @author  eric yang <yangxiao242@gmail.com>
 * @time    2010-12-20
 * @Action  xxx Action
 * @copyright Copyright &copy; 2009-2010 eyoo Software LLC
 +----------------------------------------------------------
 */// 后台权限节点菜单
class RoleNodeAction extends CommonAction{
	//增
	public function add()
	{
		$new_sort = D(MODULE_NAME)-> max("sort") + 1;
		$this->assign('new_sort',$new_sort);
		$nav_list = D("RoleNav")->findAll();
		$this->assign("nav_list",$nav_list);
		$this->display();
	}
	
	
	
	function insert() {
		//B('FilterString');
		$name=$this->getActionName();
		$model = D ($name);
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		if($data['module']=='')
		{
			$this->error("模块不能为空");
		}
		if($data['module_name'] == '')
			$data['module_name']  =  $data['module'];
		if($_REQUEST['module']==""&&$_REQUEST['action']!="")
			$model->auth_type = 2;
		elseif($_REQUEST['module']!=""&&$_REQUEST['action']=="")
			$model->auth_type = 1;
		else
			$model->auth_type = 0;
		
		if(M("RoleNode")->where("module='".$data['module']."' and action='".$data['action']."'")->count()>0)
		{
			$this->error("已存在该节点");
		}
		//保存当前数据对象
		$list=$model->add ($data);
		if ($list!==false) { //保存成功
			$this->saveLog(1,$list);
//			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('ADD_SUCCESS'));
		} else {
			//失败提示
			$this->saveLog(0,$list);
			$this->error (L('ADD_FAILED'));
		}
	}
	
	//改
	function edit() {
		$name=$this->getActionName();
		$model = M ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );
		$this->assign ( 'vo', $vo );
		$nav_list = D("RoleNav")->findAll();
		$this->assign("nav_list",$nav_list);
		$this->display ();
	}
	function update() {
		//B('FilterString');
		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $data = $model->create ()) {
			$this->error ( $model->getError () );
		}
		if($data['module']=='')
		{
			$this->error("模块不能为空");
		}
		if($data['module_name'] == '')
			$data['module_name']  =  $data['module'];
			
		if($_REQUEST['module']==""&&$_REQUEST['action']!="")
			$model->auth_type = 2;
		elseif($_REQUEST['module']!=""&&$_REQUEST['action']=="")
			$model->auth_type = 1;
		else
			$model->auth_type = 0;
			
		if(M("RoleNode")->where("module='".$data['module']."' and action='".$data['action']."' and id<>'".$data['id']."'")->count()>0)
		{
			$this->error("已存在该节点");
		}
		// 更新数据
		$list=$model->save ($data);
		if (false !== $list) {
			//成功提示
			$this->saveLog(1);
//			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success (L('EDIT_SUCCESS'));
		} else {
			//错误提示
			$this->saveLog(0);
			$this->error (L('EDIT_FAILED'));
		}
	}
	
	public function getNodeList()
	{
		$auth_type = intval($_REQUEST['auth_type']);
		$node_list = D("RoleNode")->where("auth_type=".$auth_type)->findAll();
		echo json_encode($node_list);
	}
	
	public function foreverdelete() {
		//删除指定记录
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ()) {
					//echo $model->getlastsql();
					D("RoleAccess")->where(array ("node_id" => array ('in', explode ( ',', $id ) ) ))->delete();
					$this->saveLog(1);
					$this->success (L('DEL_SUCCESS'));
				} else {
					$this->saveLog(0);
					$this->error (L('DEL_FAILED'));
				}
			} else {
				$this->saveLog(0);
				$this->error ( L('INVALID_OP'));
			}
		}
		$this->forward ();
	}
	
}
?>